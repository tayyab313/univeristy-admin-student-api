<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable =
    [
        'title',
        'company',
        'location',
        'description',
        'contact_email',
        'created_by'
    ];

    protected $table = 'jobs';

}
