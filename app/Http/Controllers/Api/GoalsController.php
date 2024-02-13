<?php

namespace App\Http\Controllers\Api;

use App\Events\GoalCreated;
use App\Events\GoalDeleted;
use App\Events\GoalUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\DeleteGoalRequest;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class GoalsController extends Controller
{

    protected function validateBaselineAndDeadline($request)
    {
        if (Carbon::parse($request->deadline) < Carbon::parse($request->baseline)) {
            return \response()->json([
                'message' => 'Deadline date should be greater than the baseline date'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (Carbon::parse($request->deadline)->endOfDay()->diffInDays(Carbon::parse($request->baseline)->startOfDay()) < 7) {
            return \response()->json([
                'message' => 'There should be a minimum of 1 week gap between the baseline and deadline',
                'errors' => [
                    'baseline' => 'There should be a minimum of 1 week gap between the baseline and deadline',
                    'deadline' => 'There should be a minimum of 1 week gap between the baseline and deadline',
                ]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    protected function validateTargetValue($request, $goal)
    {
        if ($request->progress > $goal->target_value) {
            return \response()->json([
                'message' => 'Progress should not exceed the target value',
                'errors' => [
                    'progress' => 'Progress should not exceed the target value',
                ],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function list(Request $request)
    {
        $data = $request->user()->goals()->orderBy('id', 'desc')->get();

        return response()->json([
            'goals' => [
                'data' => $data
            ]
        ], Response::HTTP_OK);
    }

    public function store(StoreGoalRequest $request)
    {

        $this->validateBaselineAndDeadline($request);

        $goal = Goal::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'baseline_date' => Carbon::parse($request->baseline)->format('Y-m-d h:i:s'),
            'deadline_date' => Carbon::parse($request->deadline)->format('Y-m-d h:i:s'),
            'target_value' => $request->target,
            'unit' => $request->unit
        ]);

        event(new GoalCreated($goal));

        return response()->json([
            'message' => "Goal created successfully"
        ], Response::HTTP_CREATED);

    }

    public function update(UpdateGoalRequest $request)
    {
        try {

            $goal = Goal::find($request->id);
            if (!$goal) {
                return \response()->json([
                    'message' => 'Goal not found'
                ], Response::HTTP_NOT_FOUND);
            }

            $this->validateBaselineAndDeadline($request);

            $this->validateTargetValue($request, $goal);

            $goal->update([
                'name' => $request->name,
                'baseline_date' => Carbon::parse($request->baseline)->format('Y-m-d'),
                'deadline_date' => Carbon::parse($request->deadline)->format('Y-m-d'),
                'target' => $request->target,
                'unit' => $request->unit,
                'current_progress' => $request->progress,
                'status' => $request->status,
            ]);

            event(new GoalUpdated($goal));

            return \response()->json([
                'message' => "Goal updated successfully"
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            return \response()->json([
                'errors' => $e->errors()
            ]);
        }
    }

    public function edit(Goal $goal)
    {
        try {
            return response()->json([
                'goal' => [
                    'id' => $goal->id,
                    'name' => $goal->name,
                    'baseline' => Carbon::parse($goal->baseline_date)->format('Y-m-d h:i'),
                    'deadline' => Carbon::parse($goal->deadline_date)->format('Y-m-d h:i'),
                    'status' => $goal->status,
                    'target' => $goal->target_value,
                    'current_progress' => $goal->current_progress,
                    'unit' => $goal->unit,
                    'goal_progress' => $goal->calculateGoalProgress()
                ]
            ], Response::HTTP_OK);
        } catch (ResourceNotFoundException $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function delete(DeleteGoalRequest $request)
    {

        $goal = Goal::find($request->goal);

        if(!$goal){
            return \response()->json([
                'message' => 'Goal not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $goal->delete();
        event(new GoalDeleted());

        return \response()->json([
            'message' => 'Goal deleted successfully'
        ], Response::HTTP_OK);
    }
}
