<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Exam;

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
}