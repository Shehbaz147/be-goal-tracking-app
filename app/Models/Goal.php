<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'baseline_date',
        'deadline_date',
        'target_value',
        'unit',
        'status',
        'daily_progress',
        'progress_date',
        'current_progress'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function calculateProgressPercentage(): float|int
    {
        if($this->target_value === 0){
            return 0;
        }

        return ($this->current_progress / $this->target_value) * 100;
    }

    public function calculateProgressUnit(): float|int
    {
        if($this->target_value === 0){
            return 0;
        }

        return $this->current_progress;
    }


    public function calculateGoalProgress(): array
    {
        $baseLineDate = Carbon::parse($this->baseline_date);
        $deadLineDate = Carbon::parse($this->deadline_date);

        $totalYears = $deadLineDate->endOfDay()->diffInYears($baseLineDate);

        if($totalYears < 1){

            $totalDays = $deadLineDate->endOfDay()->diffInDays($baseLineDate->startOfDay());

            // Check if baseline and deadline are the same dates
            if ($totalDays <= 1) {
                // Calculate progress for the case where baseline and deadline are the same
                $currentProgress = $this->calculateProgressPercentage();
                $currentProgressUnit = $this->calculateProgressUnit();
                $targetPerDay = $this->target_value;

                $currentDay = Carbon::now()->endOfDay();
                $expectedProgress = $currentProgress < 100 ? 100 : 0; // Adjust as needed
                $expectedProgressUnit = $this->target_value;
                $remainingProgress = ($expectedProgress - $currentProgress);
                $remainingProgressUnit = $expectedProgressUnit - $currentProgressUnit;

                return [
                    'totalDays' => 0,
                    'diff' => 0,
                    'totalYears' => $totalYears,
                    'targetPerYear' => $targetPerDay,
                    'currentProgress' => $currentProgress,
                    'currentProgressUnit' => $currentProgressUnit,
                    'expectedProgress' => $expectedProgress,
                    'expectedProgressUnit' => $expectedProgressUnit,
                    'remainingProgress' => $remainingProgress,
                    'remainingProgressUnit' => $remainingProgressUnit,
                ];
            }
            else{
                $targetPerDays = $this->target_value / $totalDays;
                $currentProgress = $this->calculateProgressPercentage();

                $currentDay = Carbon::now()->endOfDay();
                $currentProgressUnit = $this->calculateProgressUnit();
                $diff = $currentDay->diffInDays($baseLineDate->startOfDay());
                $expectedProgress = ($diff / $totalDays) * 100;
                $expectedProgressUnit = $this->target_value * ($diff / $totalDays);
                $remainingProgress = $currentProgress > 0 ? 100 - ($expectedProgress + $currentProgress) : 100 - $currentProgress;
                $remainingProgressUnit = $currentProgressUnit > 0 ? $this->target_value - ($expectedProgressUnit + $currentProgressUnit) : $this->target_value - $currentProgressUnit;

                return [
                    'totalDays' => $totalDays,
                    'diff' => $diff,
                    'totalYears' => $totalYears,
                    'targetPerYear' => $targetPerDays,
                    'currentProgress' => $currentProgress,
                    'currentProgressUnit' => $currentProgressUnit,
                    'expectedProgress' => $expectedProgress,
                    'expectedProgressUnit' => $expectedProgressUnit,
                    'remainingProgress' => $remainingProgress,
                    'remainingProgressUnit' => $remainingProgressUnit,
                ];
            }
        }
        else{
            $totalDays = $deadLineDate->diffInDays($baseLineDate);
            $targetPerYear = $this->target_value / $totalYears;
            $currentProgress = $this->calculateProgressPercentage();
            $currentProgressUnit = $this->calculateProgressUnit();

            $currentDay = Carbon::now()->endOfDay();
            $progressWithRespectToDay = $currentDay->diffInDays($baseLineDate->startOfDay());
            $expectedProgress = ($progressWithRespectToDay / $totalDays) * 100;
            $expectedProgressUnit = $this->target_value * ($progressWithRespectToDay / $totalDays);
            $remainingProgress = $currentProgress > 0 ? 100 - ($expectedProgress + $currentProgress) : 100 - $currentProgress;
            $remainingProgressUnit = $currentProgressUnit > 0 ? $this->target_value - ($expectedProgressUnit + $currentProgressUnit) : $this->target_value - $currentProgressUnit;

            return [
                'totalDays' => $totalDays,
                'totalYears' => $totalYears,
                'targetPerYear' => $targetPerYear,
                'currentProgress' => $currentProgress,
                'currentProgressUnit' => $currentProgressUnit,
                'expectedProgress' => $expectedProgress,
                'expectedProgressUnit' => $expectedProgressUnit,
                'remainingProgress' => $remainingProgress,
                'remainingProgressUnit' => $remainingProgressUnit,
            ];
        }
    }

}
