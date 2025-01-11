<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;
    protected $table = 'student_answer';
    protected $fillable = [
        'user_id',
        'question_id',
        'is_answer',
        'submit_answer',
        'exam_id'
    ];

    public function Question(){
        return $this->belongsTo(Question::class, 'question_id');
    }
}
