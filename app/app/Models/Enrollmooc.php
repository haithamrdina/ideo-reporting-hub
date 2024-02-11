<?php

namespace App\Models;

use App\Traits\TimeConversionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Enrollmooc extends Model
{
    use HasFactory;
    use TimeConversionTrait;

    protected $fillable = [
        'project_id', 'group_id', 'learner_docebo_id', 'mooc_docebo_id',
        'status',
        'session_time', 'cmi_time', 'calculated_time', 'recommended_time',
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

    public function mooc()
    {
        return $this->belongsTo(Mooc::class, 'mooc_docebo_id', 'docebo_id');
    }

    public static function calculateMoocDataTimes($statDate)
    {
        $moocDataTimes = DB::table('enrollmoocs')
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
        return $moocDataTimes;
    }

    public static function calculateMoocDataTimesBetweenDate($startDate, $endDate)
    {
        $moduleDataTimes = DB::table('enrollmoocs')
                        ->select(
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN session_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN session_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN session_time
                                            ELSE 0
                                END
                            ) as total_session_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN cmi_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN cmi_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN cmi_time
                                            ELSE 0
                                END
                            ) as total_cmi_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN calculated_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN calculated_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN calculated_time
                                            ELSE 0
                                END
                            ) as total_calculated_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN recommended_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN recommended_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN recommended_time
                                            ELSE 0
                                END
                            ) as total_recommended_time'),
                        )
                        ->setBindings([
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate
                        ])
                        ->first();
        return $moduleDataTimes;
    }

    public static function calculateMoocDataTimesPerProject($statDate,$projectId)
    {
        $moocDataTimes = DB::table('enrollmoocs')
                        ->where('project_id','?')
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

                        ->setBindings([$statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate, $statDate,$projectId])
                        ->first();
        return $moocDataTimes;
    }

    public static function calculateMoocDataTimesBetweenDatePerProject($startDate, $endDate, $projectId)
    {
        $moduleDataTimes = DB::table('enrollmoocs')
                        ->where('project_id','?')
                        ->select(
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN session_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN session_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN session_time
                                            ELSE 0
                                END
                            ) as total_session_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN cmi_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN cmi_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN cmi_time
                                            ELSE 0
                                END
                            ) as total_cmi_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN calculated_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN calculated_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN calculated_time
                                            ELSE 0
                                END
                            ) as total_calculated_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN recommended_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN recommended_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN recommended_time
                                            ELSE 0
                                END
                            ) as total_recommended_time'),
                        )
                        ->setBindings([
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $projectId
                        ])
                        ->first();
        return $moduleDataTimes;
    }

    public static function calculateMoocDataTimesPerGroup($statDate,$groupId)
    {
        $moocDataTimes = DB::table('enrollmoocs')
                        ->where('group_id','?')
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
        return $moocDataTimes;
    }

    public static function calculateMoocDataTimesBetweenDatePerGroup($startDate, $endDate, $groupId)
    {
        $moduleDataTimes = DB::table('enrollmoocs')
                        ->where('group_id','?')
                        ->select(
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN session_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN session_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN session_time
                                            ELSE 0
                                END
                            ) as total_session_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN cmi_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN cmi_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN cmi_time
                                            ELSE 0
                                END
                            ) as total_cmi_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN calculated_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN calculated_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN calculated_time
                                            ELSE 0
                                END
                            ) as total_calculated_time'),
                            DB::raw('SUM(
                                CASE
                                            WHEN (status = "enrolled" OR status = "waiting") AND enrollment_created_at BETWEEN ? AND ? THEN recommended_time
                                            WHEN  status = "in_progress" AND enrollment_updated_at BETWEEN ? AND ? THEN recommended_time
                                            WHEN status = "completed" AND enrollment_completed_at BETWEEN ? AND ? THEN recommended_time
                                            ELSE 0
                                END
                            ) as total_recommended_time'),
                        )
                        ->setBindings([
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $startDate, $endDate,
                            $groupId
                        ])
                        ->first();
        return $moduleDataTimes;
    }
}
