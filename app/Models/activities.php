<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class activities extends Model
{
    use HasFactory;
    protected $table="activities";
    protected $fillable = [
        'title',
        'level'
    ];

    public function Students()
    {
    return $this->belongsToMany(Students::class, 'activities_students');
    }
}
