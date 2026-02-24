<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();
        return view('tasks.index', compact('tasks'));
    }

    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string|max:255']);
        Task::create($request->only('title'));
        return redirect()->route('tasks.index');
    }

    public function update(Request $request, Task $task)
    {
        $request->validate(['title' => 'required|string|max:255']);
        $task->update($request->only('title'));
        return redirect()->route('tasks.index');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index');
    }

    public function complete(Task $task)
    {
        $task->update(['completed' => !$task->completed]);
        return redirect()->route('tasks.index');
    }
}
