<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lpenroll extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id', 'group_id', 'learner_docebo_id', 'lp_docebo_id',
        'status', 'session_time',  'enrollment_completion_percentage', 'cmi_time', 'calculated_time', 'recommended_time',
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

    public function lp()
    {
        return $this->belongsTo(Lp::class, 'lp_docebo_id', 'docebo_id');
    }
}
