<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'content',
        'author',
        'published',
    ];

    protected $appends = [
        'created_at_formated',
        'updated_at_formated',
    ];


    protected function createdAtFormated(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at?->diffForHumans()
        );
    }
    protected function updatedAtFormated(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->updated_at?->diffForHumans()
        );
    }

}