<?php

namespace App\Models;

use App\Traits\TimeConversionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Enrollmodule extends Model
{
    use HasFactory;
    use TimeConversionTrait;

    protected $fillable = [
        'project_id', 'group_id', 'learner_docebo_id', 'module_docebo_id',
        'status',
        'session_time',  'cmi_time', 'calculated_time', 'recommended_time',
        'enrollment_created_at', 'enrollment_updated_at', 'enrollment_completed_at',
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

    public static function calculateModuleDataTimes($statDate)
    {
        $moduleDataTimes = DB::table('enrollmodules')
                        ->select(
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN session_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN session_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN session_time
                                            ELSE 0
                                END
                            ) as total_session_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN cmi_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN cmi_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN cmi_time
                                            ELSE 0
                                END
                            ) as total_cmi_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN calculated_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN calculated_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN calculated_time
                                            ELSE 0
                                END
                            ) as total_calculated_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN recommended_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN recommended_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN recommended_time
                                            ELSE 0
                                END
                            ) as total_recommended_time'),
                        )
                        ->setBindings([$statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate])
                        ->first();
        return $moduleDataTimes;
    }

    public static function calculateModuleDataTimesBetweenDate($startDate, $endDate,$learnersIds)
    {
       $moduleDataTimes = DB::table('enrollmodules')
                        ->whereIn('learner_docebo_id', $learnersIds)
                        ->select(
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(session_time, 0)
                                    ELSE 0
                                END
                            ) as total_session_time'),
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(cmi_time, 0)
                                    ELSE 0
                                END
                            ) as total_cmi_time'),
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(calculated_time, 0)
                                    ELSE 0
                                END
                            ) as total_calculated_time'),
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(recommended_time, 0)
                                    ELSE 0
                                END
                            ) as total_recommended_time'),
                        )->first();
        return $moduleDataTimes;

    }

    public static function calculateModuleDataTimesPerProject($statDate,$projectId)
    {
        $moduleDataTimes = DB::table('enrollmodules')
                        ->where('project_id', '?')
                        ->select(
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN session_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN session_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN session_time
                                            ELSE 0
                                END
                            ) as total_session_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN cmi_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN cmi_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN cmi_time
                                            ELSE 0
                                END
                            ) as total_cmi_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN calculated_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN calculated_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN calculated_time
                                            ELSE 0
                                END
                            ) as total_calculated_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN recommended_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN recommended_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN recommended_time
                                            ELSE 0
                                END
                            ) as total_recommended_time'),
                        )
                        ->setBindings([$statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $projectId])
                        ->first();
        return $moduleDataTimes;
    }

    public static function calculateModuleDataTimesBetweenDatePerProject($startDate, $endDate, $projectId, $learnersIds)
    {
        $moduleDataTimes = DB::table('enrollmodules')
                        ->where('project_id', $projectId)
                        ->whereIn('learner_docebo_id', $learnersIds)
                        ->select(
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(session_time, 0)
                                    ELSE 0
                                END
                            ) as total_session_time'),
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(cmi_time, 0)
                                    ELSE 0
                                END
                            ) as total_cmi_time'),
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(calculated_time, 0)
                                    ELSE 0
                                END
                            ) as total_calculated_time'),
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(recommended_time, 0)
                                    ELSE 0
                                END
                            ) as total_recommended_time'),
                        )->first();
        return $moduleDataTimes;
    }

    public static function calculateModuleDataTimesPerGroup($statDate,$groupId)
    {
        $moduleDataTimes = DB::table('enrollmodules')
                        ->where('group_id', '?')
                        ->select(
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN session_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN session_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN session_time
                                            ELSE 0
                                END
                            ) as total_session_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN cmi_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN cmi_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN cmi_time
                                            ELSE 0
                                END
                            ) as total_cmi_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN calculated_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN calculated_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN calculated_time
                                            ELSE 0
                                END
                            ) as total_calculated_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at >= ? THEN recommended_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at >= ? THEN recommended_time
                                            WHEN status = "completed" AND enrollment_completed_at >= ? THEN recommended_time
                                            ELSE 0
                                END
                            ) as total_recommended_time'),
                        )
                        ->setBindings([$statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate,$groupId])
                        ->first();
        return $moduleDataTimes;
    }

    public static function calculateModuleDataTimesBetweenDatePerGroup($startDate, $endDate, $groupId, $learnersIds)
    {
        $moduleDataTimes = DB::table('enrollmodules')
                        ->where('group_id', $groupId)
                        ->whereIn('learner_docebo_id', $learnersIds)
                        ->select(
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(session_time, 0)
                                    ELSE 0
                                END
                            ) as total_session_time'),
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(cmi_time, 0)
                                    ELSE 0
                                END
                            ) as total_cmi_time'),
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(calculated_time, 0)
                                    ELSE 0
                                END
                            ) as total_calculated_time'),
                            DB::raw('SUM(
                                CASE
                                    WHEN
                                        ((status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "in_progress" AND enrollment_updated_at BETWEEN "'. $startDate.'" AND "'.$endDate.'") OR
                                        (status = "completed" AND enrollment_completed_at BETWEEN "'. $startDate.'" AND "'.$endDate.'")
                                    THEN COALESCE(recommended_time, 0)
                                    ELSE 0
                                END
                            ) as total_recommended_time'),
                        )->first();
        return $moduleDataTimes;
    }
}
