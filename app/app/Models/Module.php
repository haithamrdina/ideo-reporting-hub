<?php

namespace App\Models;

use App\Enums\CourseStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'docebo_id',
        'code',
        'name',
        'category',
        'language',
        'article_id',
        'niveau',
        'status',
        'recommended_time',
        'los'
    ];

    protected $casts = [
        'los' => 'array',
        'status' => CourseStatusEnum::class
    ];


    public function projects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();;
    }


}
