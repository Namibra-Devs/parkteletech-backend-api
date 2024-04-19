<?php

namespace App\Http\Controllers;

use App\Models\TrainingSchedule;
use Illuminate\Http\Request;

class TrainingScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schedules = TrainingSchedule::all();
        return response()->json($schedules, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'training_topic' => 'required',
            'desc' => 'required',
            'time' => 'required',
            'date' => 'required',
            'department' => 'required',
            'individuals' => 'required'
        ]);

        $schedule = TrainingSchedule::create($request->all());
        return response()->json([
            'message' => 'Training schedule created successfully!',
            'data' => $schedule
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $schedule = TrainingSchedule::find($id);
        if (!$schedule) {
            return response()->json(['message' => 'Training schedule not found'], 404);
        }
        return response()->json($schedule, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $schedule = TrainingSchedule::find($id);
        if (!$schedule) {
            return response()->json(['message' => 'Training schedule not found'], 404);
        }
        $schedule->update($request->all());
        return response()->json([
            'message' => 'Training schedule updated successfully!',
            'data' => $schedule
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = TrainingSchedule::destroy($id);
        if ($result) {
            return response()->json(['message' => 'Training schedule deleted successfully'], 200);
        }
        return response()->json(['message' => 'Training schedule not found'], 404);
    }

    /**
     * Search for a name
     *
     * @param  str  $name
     * @return \Illuminate\Http\Response
     */
    public function search($name)
    {
        $result = TrainingSchedule::where('name', 'like', '%'.$name.'%')->get();
        if ($result->isEmpty()) {
            return response()->json(['message' => 'No matching training schedules found'], 404);
        }
        return response()->json($result, 200);
    }
}
