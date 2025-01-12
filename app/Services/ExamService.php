<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Exam;
use App\Models\Question;
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
        return $exam;
    }

    public function start($exam, $user){
        $this->StudentEnrolledCourseValidation($exam, $user);
        $userExam = $user->Exams()->where('exam_id', $exam->id)->first();
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
        $UserExamTimeLine = $user->UserExamTimeLines()->LastOpenOne($exam->id)->first();
        if(!$UserExamTimeLine)
            throw new Exception('you have not started this exam');

        $UserExamTimeLine->update(['end' => now()]);
    }

    public function resuming($exam, $user){
        $this->StudentEnrolledCourseValidation($exam, $user);
        $UserExamTimeLine = $user->UserExamTimeLines()->LastOpenOne($exam->id)->first();
        if($UserExamTimeLine)
            throw new Exception('you already resuming this exam');

        UserExamTimeLine::create([
            'user_id' => $user->id,
            'exam_id' => $exam->id,
            'start' => now(),
        ]);
    }

    public function completed($exam, $user,$data){
        $this->CompletedExamValidation($exam, $user);
        $mark = $this->CreateStudentAnswers($user, $exam,$data['answers']);
        $user->UserExamTimeLines()->LastOpenOne($exam->id)->update(['end' => now()]);
        UserExam::where('exam_id', $exam->id)
                    ->where('user_id', $user->id)
                    ->update(['status' => 'completed','mark' => $mark]);
    }

    public function CompletedExamValidation($exam, $user){
        $user_course = $user->Courses()->where('course_id', $exam->course_id)->first();
        if(!$user_course)
            throw new Exception('this user not enrolled in this course');

        if($exam->status == 'completed')
            throw new Exception('this exam is already completed');

        $total_minutes = $user->GetExamTimeLinesTotalMinutes($exam->id);
        if($exam->exam_time < $total_minutes)
            throw new Exception('exam time is ended');
    }

    public function CreateStudentAnswers($user, $exam, $answers){
        $this->AnswerQuestionValidation($exam, $answers);
        $mark = 0;
        foreach($answers as $answer_data){
            $answer = Answer::where('is_answer', 1)
                                    ->where('question_id', $answer_data['question_id'])
                                    ->first();

            if($answer && $answer_data['submit_answer'] == $answer->answer_text)
                $mark += $answer->Question?->points;

            $user->StudentAnswers()->create([
                "question_id" => $answer_data['question_id'],
                "submit_answer" => $answer_data['submit_answer'],
                'exam_id' => $exam->id,
            ]);
        }
        return $mark;
    }

    public function AnswerQuestionValidation($exam, $answers){
        $questionIds = array_column($answers, 'question_id');
        $questions_count = Question::where('model_id', $exam->id)
                                    ->where('model_type', Exam::class)
                                    ->whereIn('id', $questionIds)
                                    ->count();        
        if($questions_count != count($questionIds))
            throw new Exception('question not belong to this exam');
    }

    public function StudentEnrolledCourseValidation($exam, $user){
        $user_course = $user->Courses()->where('course_id', $exam->course_id)->first();
        if(!$user_course)
            throw new Exception('this user not enrolled in this course');
    }
}