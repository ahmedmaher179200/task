<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exam\CompletedRequest;
use App\Http\Requests\Exam\CreateRequest;
use App\Http\Requests\Exam\PausingRequest;
use App\Http\Requests\Exam\ResumingRequest;
use App\Http\Requests\Exam\StartRequest;
use App\Http\Resources\ExamResource;
use App\Models\Exam;
use App\Models\User;
use App\Services\ExamService;
use App\Traits\response;
use Exception;
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
        $exam = $this->ExamService->create($data);
        DB::commit();
        return $this->success('success',200,'data',new ExamResource($exam));
    }

    public function show($exam_id){
        $exam = $exam = $this->getExam($exam_id);
        return $this->success('success',200,'data',new ExamResource($exam));
    }

    public function start(StartRequest $request, $exam_id){
        try {
            DB::beginTransaction();
            $user = User::find($request->user_id);
            $exam = $this->getExam($exam_id);
            $this->ExamService->start($exam, $user);
            DB::commit();
            return $this->success('success',200,'data',new ExamResource($exam));
        } catch (Exception $e) {
            return $this->failed($e->getMessage(),400);
        }
    }

    public function pausing(PausingRequest $request,$exam_id){
        try {
            DB::beginTransaction();   
            $user = User::find($request->user_id);
            $exam = $this->getExam($exam_id);
            $this->ExamService->pausing($exam, $user);
            DB::commit();
            return $this->success('success',200);
        } catch (Exception $e) {
            return $this->failed($e->getMessage(),400);
        }
    }

    public function resuming(ResumingRequest $request,$exam_id){
        try {
            DB::beginTransaction();   
            $user = User::find($request->user_id);
            $exam = $this->getExam($exam_id);
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
            $exam = $this->getExam($exam_id);
            $data = $request->only('answers');
            $this->ExamService->completed($exam, $user, $data);
            DB::commit();
            return $this->success('success',200);
        } catch (Exception $e) {
            return $this->failed($e->getMessage(),400);
        }
    }

    public function getExam($exam_id){
        $exam = Exam::find($exam_id);
        if(!$exam)
            throw new Exception('you already resuming this exam');

        return $exam;
    }
}
