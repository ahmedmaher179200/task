<?php

namespace App\Http\Controllers;

use App\Http\Requests\Exam\CreateRequest;
use App\Services\ExamService;
use App\Traits\response;
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
}
