<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;
    protected $table = 'exams';

    protected $fillable = [
        'title',
        'course_id',
        'exam_time',
        'points'
    ];
    
    protected $casts = [
        'exam_time' => 'integer',
    ];

    public function Questions()
    {
        return $this->morphMany(Question::class, 'model');
    }

    public function Course(){
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function scopeEager($query){
        return $query->with('Questions.Answers', 'Course');
    }
}
