<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExamTimeLine extends Model
{
    use HasFactory;
    protected $table = 'user_exam_time_line';
    protected $fillable = [
        'user_id',
        'exam_id',
        'start',
        'end'
    ];
}
