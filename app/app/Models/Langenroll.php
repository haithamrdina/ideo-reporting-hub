<?php

namespace App\Models;

use App\Traits\TimeConversionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Langenroll extends Model
{
    use HasFactory;
    use TimeConversionTrait;

    protected $fillable = [
        'project_id', 'group_id', 'learner_docebo_id', 'module_docebo_id',
        'status', 'language', 'niveau',
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

    public static function calculateSpeexDataTimes($statDate)
    {
        $speexDataTimes = DB::table('langenrolls')
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
        return $speexDataTimes;
    }

    public static function calculateSpeexDataTimesBetweenDate($startDate, $endDate)
    {
        $moduleDataTimes = DB::table('langenrolls')
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

    public static function calculateSpeexDataTimesPerProject($statDate,$projectId)
    {
        $speexDataTimes = DB::table('langenrolls')
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
        return $speexDataTimes;
    }

    public static function calculateSpeexDataTimesPerGroup($statDate,$groupId)
    {
        $speexDataTimes = DB::table('langenrolls')
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
        return $speexDataTimes;
    }
}
