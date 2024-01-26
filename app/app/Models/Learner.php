<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Learner extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'docebo_id', 'speex_id', 'lastname', 'firstname', 'email', 'username',
        'last_access_date', 'creation_date', 'statut', 'categorie', 'group_id', 'project_id'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function enrollModules()
    {
        return $this->hasMany(EnrollModule::class, 'learner_docebo_id', 'docebo_id');
    }

    public function enrollLangues()
    {
        return $this->hasMany(Langenroll::class, 'learner_docebo_id', 'docebo_id');
    }

    public function enrollLps()
    {
        return $this->hasMany(Lpenroll::class, 'learner_docebo_id', 'docebo_id');
    }
}
