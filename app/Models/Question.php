<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $table = 'questions';
    protected $fillable = [
        'type',
        'question_text',
        'model_type',
        'model_id',
        'points'
    ];

    public function Answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }

    public function model()
    {
        return $this->morphTo();
    }

    public function scopeMyRandom($query){
        return $query->inRandomOrder();
    }
}
