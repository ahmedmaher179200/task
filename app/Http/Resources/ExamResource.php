<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'exam_time' => $this->exam_time,
            'created_at' => $this->created_at,
            'course' => new CourseResource($this->Course),
            'questions' => QuestionResource::collection($this->RandomQuestions(2)),
        ];
    }
}
