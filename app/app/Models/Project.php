<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class)->withTimestamps();
    }


    public function moocs()
    {
        return $this->belongsToMany(Mooc::class)->withTimestamps();
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
        return $this->hasMany(EnrollModule::class, 'project_id', 'id');
    }

    public function enrollLangues()
    {
        return $this->hasMany(Langenroll::class, 'project_id', 'id');
    }

    public function enrollLps()
    {
        return $this->hasMany(Lpenroll::class, 'project_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }


}
