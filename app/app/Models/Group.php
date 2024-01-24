<?php

namespace App\Models;

use App\Enums\GroupStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'docebo_id',
        'code',
        'name',
        'status'
    ];


     /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'status' => GroupStatusEnum::class
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withTimestamps();
    }

}
