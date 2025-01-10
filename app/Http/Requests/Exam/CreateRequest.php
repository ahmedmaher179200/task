<?php

namespace App\Http\Requests\Exam;

use App\Traits\requestApiTrait;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends FormRequest
{
    use requestApiTrait;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'     => 'required|string',
            'exam_time'     => 'required|numeric',
            'course_id' => 'required|exists:courses,id',
            'questions' => 'required|array|min:1',

            'questions.*.question_text' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,fill_blanks,essay',
            'questions.*.points' => 'required|numeric',

            'questions.*.answers' => 'required|array|min:1',
            'questions.*.answers.*.is_answer' => 'nullable|boolean',
            'questions.*.answers.*.answer_text' => 'required|string',

        ];
    }
}
