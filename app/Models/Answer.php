<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $table = 'answers';

    protected $fillable = [
        'question_id',
        'is_answer',
        'answer_text',
    ];

    protected $casts = [
        'is_answer' => 'integer',
    ];

    public function Question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
