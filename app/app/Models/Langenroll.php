<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Langenroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'group_id', 'learner_docebo_id', 'module_docebo_id',
        'status', 'niveau', 'session_time',  'cmi_time', 'language',
        'enrollment_created_at', 'enrollment_updated_at', 'enrollment_completed_at',
    ];

    // Cast timestamp columns to datetime
    protected $casts = [
        'enrollment_created_at' => 'datetime',
        'enrollment_updated_at' => 'datetime',
        'enrollment_completed_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function learner()
    {
        return $this->belongsTo(Learner::class, 'learner_docebo_id', 'docebo_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_docebo_id', 'docebo_id');
    }
}
