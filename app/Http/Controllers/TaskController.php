<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query();

        if($request->status) {
            $query->where('status', $request->status);
        }

        $tasks = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $tasks->items(),
            'pagination' => [
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        try {
            $task = Task::create($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $task = Task::findorFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, string $id)
    {
        try {
            $task = Task::findorFail($id);

            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
            ], $request->validated());

            $task->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Task updated successfully',
                'data' => $task
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $task = Task::findorFail($id);
            $task->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Task deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Task not found'], 404);
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleStatus(Task $task) {
        try {
            $task->status = $task->status === 'pending' ? 'completed' : 'pending';
            $task->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Task not found'], 404);
        }

    }
}
