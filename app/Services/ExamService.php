<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\StudentAnswer;
use App\Models\UserExam;
use App\Models\UserExamTimeLine;
use Exception;
use Illuminate\Support\Facades\DB;

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

    public function completed($exam, $user,$data){
        $this->StudentEnrolledCourseValidation($exam, $user);
        if($exam->status == 'completed')
            throw new Exception('this exam is already completed');

        $total_minutes = $user->UserExamTimeLines()
                                ->where('exam_id', $exam->id)
                                ->select(DB::raw('SUM(TIMESTAMPDIFF(MINUTE, `start`, `end`)) as total_minutes'))
                                ->value('total_minutes');

        if($exam->exam_time < $total_minutes)
            throw new Exception('exam time is ended');

        $this->CreateStudentAnswer($user, $data['answers']);

        $user->UserExamTimeLines()
                    ->where('exam_id', $exam->id)
                    ->latest()
                    ->where('end', null)
                    ->update(['end' => now()]);

        UserExam::where('exam_id', $exam->id)
                    ->where('user_id', $user->id)
                    ->update(['status' => 'completed']);
    }

    public function CreateStudentAnswer($user, $answers){
        $answers = collect($answers)->unique('question_id')->values()->all();
        $user->StudentAnswers()->createMany($answers);
    }

    public function StudentEnrolledCourseValidation($exam, $user){
        $user_course = $user->Courses()->where('course_id', $exam->course_id)->first();
        if(!$user_course)
            throw new Exception('this user not enrolled in this course');
    }
}