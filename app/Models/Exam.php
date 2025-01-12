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

    public function RandomQuestions($method = 1){
        $questions = $this->Questions();
        
        if($method == 1){
            // you can controle this algorithm
            if(time() % 3 == 0){
                $questions = $questions->orderBy('question_text', 'desc');
            } else if(time() % 3 == 1){
                $questions = $questions->orderBy('question_text', 'asc');
            } else if(time() % 3 == 2){
                $questions = $questions->orderBy('id', 'desc');
            }
        } else if($method == 2){
            // random questions
            $quetion_ids = (clone $questions)->pluck('id')->toArray();
            shuffle($quetion_ids);
            $questions = $questions->orderByRaw("FIELD(id, " . implode(',', $quetion_ids) . ")");
        }

        return $questions->get();
    }
}
