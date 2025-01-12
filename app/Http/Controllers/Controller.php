<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\User;
use App\Traits\response;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, response;
    public function users(){
        $users = User::get();
        return $this->success('success',200,'data', UserResource::collection($users));
    }

    public function courses(){
        $courses = Course::get();
        return $this->success('success',200,'data',CourseResource::collection($courses));
    }
}
