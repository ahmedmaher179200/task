<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exam\ShowRequest;
use App\Models\Exam;
use App\Models\StudentAnswer;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserExam;
use App\Models\UserExamTimeLine;
use App\Traits\response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    use response;
    public function results(ShowRequest $request){
        $examTotalscore = Exam::find($request->exam_id)->Questions()->sum('points');

        $data = StudentAnswer::where('exam_id', $request->exam_id)
                                ->join('users', 'users.id', '=', 'student_answer.user_id')
                                ->join('questions', 'questions.id', '=', 'student_answer.question_id')
                                ->join('answers', 'answers.question_id', '=', 'student_answer.question_id')
                                ->where('answers.is_answer', 1)
                                ->groupBy('users.id') 
                                ->select('users.name',
                                    'users.id',
                                    'users.name',
                                    DB::raw('COUNT(student_answer.id) as answer_count'),
                                    DB::raw('SUM(IF(student_answer.submit_answer = answers.answer_text, questions.points, 0)) as total_points'),
                                    DB::raw('(SUM(IF(student_answer.submit_answer = answers.answer_text, questions.points, 0)) / '.$examTotalscore.') * 100 as percentage')
                                )
                                ->get();
        
        return $this->success('success',200,'data',$data);
    }

    public function questionsAnalysis(ShowRequest $request){
        $baseQuery = StudentAnswer::where('exam_id', $request->exam_id)
                                ->join('questions', 'questions.id', '=', 'student_answer.question_id')
                                ->join('answers', 'answers.question_id', '=', 'student_answer.question_id')
                                ->where('answers.is_answer', 1)
                                ->groupBy('questions.id') 
                                ->select(
                                    'questions.id',
                                    'questions.question_text',
                                    'questions.type',
                                    'questions.points',
                                    DB::raw('SUM(IF(student_answer.submit_answer = answers.answer_text, 1, 0)) as correct_answer')
                                );

        $hardest = (clone $baseQuery)
            ->orderByDesc('correct_answer')
            ->first();
        
        $easiest = (clone $baseQuery)
            ->orderBy('correct_answer')
            ->first();
        return $this->success('success',200,'data',[
            'hardest' => $hardest?->correct_answer,
            'easiest' => $easiest?->correct_answer,
        ]);
    }

    public function averageTime(ShowRequest $request){
        $total_time_in_minutes = UserExamTimeLine::join('user_exam', 'user_exam_time_line.exam_id', '=', 'user_exam.exam_id')
                        ->where('user_exam_time_line.exam_id', $request->exam_id)
                        ->where('user_exam.status', 'completed')
                        ->groupBy('user_exam_time_line.id')
                        ->select(
                            'user_exam_time_line.id',
                            DB::raw('TIMESTAMPDIFF(MINUTE, user_exam_time_line.start, user_exam_time_line.end) AS total_minutes')
                        )
                        ->get()
                        ->sum('total_minutes');

        $answered_counts = StudentAnswer::where('exam_id', $request->exam_id)->count();

        return $this->success('success',200,'data',round(($total_time_in_minutes / $answered_counts), 2));
    }

    public function general(ShowRequest $request){
        $exam = Exam::find($request->exam_id);
        $students_completed_exam = UserExam::where('status', 'completed')
                                            ->where('exam_id', $request->exam_id)
                                            ->count();

        $users_course_count = UserCourse::where('course_id', $exam->course_id)->count();
        $students_not_answer_all_questions = User::whereHas('StudentAnswers', function($query) {
                                                        $query->whereHas('Question', function($query){
                                                            $query->where('model_id', Request()->exam_id)
                                                                ->where('model_type', Exam::class);
                                                        });
                                                    }, '<', count($exam->Questions))
                                                    ->count();
        return $this->success('success',
                            200,
                            'data',[
                                'students_completed_exam' => $students_completed_exam, 
                                'students_missed_exam' => $users_course_count - $students_completed_exam, 
                                'students_not_answer_all_questions' => $students_not_answer_all_questions
                            ]);
    }
}
