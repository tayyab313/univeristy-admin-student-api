<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['book_name', 'author_name', 'published_year'];
    public function requests()
    {
        return $this->hasMany(BookRequest::class);
    }
}
