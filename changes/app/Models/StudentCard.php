<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCard extends Model
{
    use HasFactory;

    protected $table = 'student_card';

    protected $fillable =
    [
        'name',
        'email',
        'phone_no',
        'address',
        'student_id'
    ];

}
