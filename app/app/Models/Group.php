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


    public function modules()
    {
        return $this->belongsToMany(Module::class)->withTimestamps();
    }

    public function lps()
    {
        return $this->belongsToMany(Lp::class)->withTimestamps();
    }

    public function learners()
    {
        return $this->hasMany(Learner::class);
    }

    public function enrollModules()
    {
        return $this->hasMany(EnrollModule::class, 'group_id', 'id');
    }

    public function enrollLangues()
    {
        return $this->hasMany(Langenroll::class, 'group_id', 'id');
    }

    public function enrollLps()
    {
        return $this->hasMany(Lpenroll::class, 'group_id', 'id');
    }

}
