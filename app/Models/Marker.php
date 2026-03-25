<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    use HasFactory;

    public const CREATED_AT = 'added';
    public const UPDATED_AT = 'edited';

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'added' => 'datetime',
            'edited' => 'datetime',
        ];
    }
}
