<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\UserExam;
use App\Models\UserExamTimeLine;
use Exception;

class ExamService
{
    public function create($data){
        $exam = Exam::create([
            'title'  => $data['title']?? null,
            'course_id' => $data['course_id']?? null,
            'exam_time' => $data['exam_time']?? null,
        ]);

        foreach($data['questions'] as $question_data){
            $question = $exam->Questions()->create($question_data);
            $question->Answers()->createMany($question_data['answers']);
        }
    }

    public function start($exam, $user){
        $this->StudentEnrolledCourseValidation($exam, $user);
        $userExam = $user->Exams()->where('course_id', $exam->id)->first();
        if($userExam)
            throw new Exception('you already started this exam');
        UserExam::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id
        ]);
        UserExamTimeLine::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'start' => now(),
        ]);
    }

    public function pausing($exam, $user){
        $this->StudentEnrolledCourseValidation($exam, $user);
        $UserExamTimeLine = $user->UserExamTimeLines()
                                ->where('exam_id', $exam->id)
                                ->latest()
                                ->where('end', null)
                                ->first();
        if(!$UserExamTimeLine)
            throw new Exception('you have not started this exam');

        $UserExamTimeLine->update(['end' => now()]);
    }

    public function resuming($exam, $user){
        $this->StudentEnrolledCourseValidation($exam, $user);
        $UserExamTimeLine = $user->UserExamTimeLines()
                                ->where('exam_id', $exam->id)
                                ->latest()
                                ->where('end', null)
                                ->first();

        if($UserExamTimeLine)
            throw new Exception('you already resuming this exam');

        UserExamTimeLine::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'start' => now(),
        ]);
    }

    public function StudentEnrolledCourseValidation($exam, $user){
        $user_course = $user->Courses()->where('course_id', $exam->course_id)->first();
        if(!$user_course)
            throw new Exception('this user not enrolled in this course');
    }
}