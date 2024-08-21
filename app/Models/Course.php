<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $guarded = [];
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_registrations', 'course_id', 'student_id');
    }
    public function registrations()
    {
        return $this->hasMany(CourseRegistration::class, 'course_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id');
    }
    public function staff()
    {
        return $this->belongsToMany(User::class, 'course_staff', 'course_id', 'staff_id');
    }
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

}
