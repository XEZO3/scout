<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class activities_users extends Model
{
    use HasFactory;
    protected $table="activities_students";
    protected $fillable = [
        'students_id',
        'activities_id',
    ];

    public function activities(){
        return $this->belongsTo(activities::class);
 
    }
    public function Students(){
        return $this->belongsTo(Students::class);
 
    }
}
