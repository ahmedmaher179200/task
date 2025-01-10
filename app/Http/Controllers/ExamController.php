<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exam\CompletedRequest;
use App\Http\Requests\Exam\CreateRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserExam;
use App\Services\ExamService;
use App\Traits\response;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
{
    use response;
    public $ExamService;
    public function __construct(ExamService $ExamService){
        $this->ExamService = $ExamService;
    }
    public function create(CreateRequest $request){
        DB::beginTransaction();
        $data = $request->only('title', 'exam_time','course_id', 'questions');
        $this->ExamService->create($data);
        DB::commit();
        return $this->success('success',200);
    }

    public function show($exam_id){
        $exam = Exam::find($exam_id);
        if(!$exam)
            return $this->failed('exam not found',404);

        return $this->success('success',
                                200,
                                'data',
                                new ExamResource($exam));
    }

    public function start(Request $request, $exam_id){
        try {
            DB::beginTransaction();
            $user = User::find($request->user_id);
            if(!$user)
                return $this->failed('user not found',404);
    
            $exam = Exam::find($exam_id);
            if(!$exam)
                return $this->failed('exam not found',404);
    
            $this->ExamService->start($exam, $user);
            DB::commit();
            return $this->success('success',200,'data',new ExamResource($exam));
        } catch (Exception $e) {
            return $this->failed($e->getMessage(),400);
        }
    }

    public function pausing(Request $request,$exam_id){
        try {
            DB::beginTransaction();   
            $user = User::find($request->user_id);
            if(!$user)
                return $this->failed('user not found',404);
            
            $exam = Exam::find($exam_id);
            if(!$exam)
                return $this->failed('exam not found',404);
    
            $this->ExamService->pausing($exam, $user);
            DB::commit();
            return $this->success('success',200);
        } catch (Exception $e) {
            return $this->failed($e->getMessage(),400);
        }
    }

    public function resuming(Request $request,$exam_id){
        try {
            DB::beginTransaction();   
            $user = User::find($request->user_id);
            if(!$user)
                return $this->failed('user not found',404);
            
            $exam = Exam::find($exam_id);
            if(!$exam)
                return $this->failed('exam not found',404);
    
            $this->ExamService->resuming($exam, $user);
            DB::commit();
            return $this->success('success',200);
        } catch (Exception $e) {
            return $this->failed($e->getMessage(),400);
        }
    }

    public function completed(CompletedRequest $request,$exam_id){
        try {
            DB::beginTransaction();   
            $user = User::find($request->user_id);
            if(!$user)
                return $this->failed('user not found',404);
            
            $exam = Exam::find($exam_id);
            if(!$exam)
                return $this->failed('exam not found',404);

            $data = $request->only('answers');
            $this->ExamService->completed($exam, $user, $data);
            DB::commit();
            return $this->success('success',200);
        } catch (Exception $e) {
            return $this->failed($e->getMessage(),400);
        }
    }

    public function general($exam_id){
        $exam = Exam::find($exam_id);
        if(!$exam)
            return $this->failed('exam not found',404);

        $students_completed_exam = UserExam::where('status', 'completed')
                                            ->where('exam_id', $exam_id)
                                            ->count();

        $users_course_count = UserCourse::where('course_id', $exam->course_id)->count();

        $students_not_answer_all_questions = User::whereHas('StudentAnswers', function($query) use($exam_id){
                                                        $query->whereHas('Question', function($query) use($exam_id){
                                                            $query->where('model_id', $exam_id)
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
