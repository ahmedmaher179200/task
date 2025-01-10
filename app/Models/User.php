<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    public function Courses(){
        return $this->belongsToMany(Course::class, 'user_course', 'user_id', 'course_id');
    }

    public function Exams(){
        return $this->belongsToMany(Exam::class, 'user_exam', 'user_id', 'exam_id');
    }

    public function UserExamTimeLines(){
        return $this->hasMany(UserExamTimeLine::class, 'user_id');
    }

    public function StudentAnswers(){
        return $this->hasMany(StudentAnswer::class, 'user_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
