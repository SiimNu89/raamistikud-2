<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasFormattedDate;

class Post extends Model
{
    use HasFactory, HasFormattedDate;

    protected $fillable = [
        'title',
        'content',
        'author_id',
        'published',
    ];

    // Relationships
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
