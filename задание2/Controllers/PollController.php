<?php
// app/Http/Controllers/PollController.php

namespace App\Http\Controllers;

use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PollController extends Controller
{
    public function index(): JsonResponse
    {
        $polls = Poll::with('options')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $polls
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'options' => 'required|array|min:2|max:10',
            'options.*' => 'required|string|max:255|distinct',
            'allow_multiple_votes' => 'boolean',
            'ends_at' => 'nullable|date|after:now'
        ]);

        $poll = Poll::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'allow_multiple_votes' => $validated['allow_multiple_votes'] ?? false,
            'ends_at' => $validated['ends_at'] ?? null
        ]);

        foreach ($validated['options'] as $optionText) {
            $poll->options()->create(['text' => $optionText]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Опрос успешно создан',
            'data' => $poll->load('options')
        ], 201);
    }

    public function show(Poll $poll): JsonResponse
    {
        $poll->load('options');

        return response()->json([
            'success' => true,
            'data' => $poll
        ]);
    }

    public function destroy(Poll $poll): JsonResponse
    {
        $poll->delete();

        return response()->json([
            'success' => true,
            'message' => 'Опрос успешно удален'
        ]);
    }
}
