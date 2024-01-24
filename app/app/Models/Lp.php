<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lp extends Model
{
    use HasFactory;

    protected $fillable = [
        'docebo_id', 'code', 'name', 'courses',
    ];

    protected $casts = [
        'courses' => 'json',
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
