<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'phone',
        'is_email_verified',
        'student_type',
        'role',
        'is_active',
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'user_id' => $this->id,
            'role'    => $this->role,
            'name'    => $this->first_name . ' ' . $this->last_name,
        ];
    }

    public function grades()
    {
        return $this->belongsToMany(grade::class, 'user_grades', 'user_id', 'grade_id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            //
        ];
    }

    // relation for get package schools



    public function scopeTeachersAndStudents($query)
    {
        return $query->whereIn('role', ['school', 'teacher']);
    }

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'student_subject',
            'student_id',
            'subject_id'
        );
    }
}
