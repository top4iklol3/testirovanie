<?php
// app/Http/Controllers/VoteController.php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Option;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VoteController extends Controller
{
    public function vote(Request $request, Poll $poll): JsonResponse
    {
        // Проверка активности опроса
        if (!$poll->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Этот опрос закрыт для голосования'
            ], 403);
        }

        // Проверка истечения срока
        if ($poll->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Срок голосования истек'
            ], 403);
        }

        $ipAddress = $request->ip();

        // Проверка на повторное голосование (для одиночных голосов)
        if (!$poll->allow_multiple_votes) {
            if ($poll->hasUserVoted($ipAddress)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы уже голосовали в этом опросе'
                ], 403);
            }
        }

        // Получаем ID опции(й)
        $optionIds = $poll->allow_multiple_votes
            ? $request->option_id
            : [$request->option_id];

        // Проверка принадлежности опций к опросу
        foreach ($optionIds as $optionId) {
            $option = Option::find($optionId);
            if (!$option || $option->poll_id != $poll->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Опция не принадлежит этому опросу'
                ], 403);
            }
        }

        // Запись голосов
        foreach ($optionIds as $optionId) {
            if ($poll->allow_multiple_votes) {
                $existingVote = Vote::where('poll_id', $poll->id)
                    ->where('option_id', $optionId)
                    ->where('ip_address', $ipAddress)
                    ->first();

                if ($existingVote) {
                    continue;
                }
            }

            Vote::create([
                'poll_id' => $poll->id,
                'option_id' => $optionId,
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent()
            ]);

            Option::where('id', $optionId)->increment('votes_count');
        }

        return response()->json([
            'success' => true,
            'message' => 'Голос успешно учтен',
            'data' => $poll->load('options')
        ]);
    }

    public function results(Poll $poll): JsonResponse
    {
        $poll->load('options');

        $totalVotes = $poll->votes()->count();

        $results = [];
        foreach ($poll->options as $option) {
            $percentage = $totalVotes > 0
                ? round(($option->votes_count / $totalVotes) * 100, 2)
                : 0;

            $results[] = [
                'option_id' => $option->id,
                'text' => $option->text,
                'votes' => $option->votes_count,
                'percentage' => $percentage
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'poll' => [
                    'id' => $poll->id,
                    'title' => $poll->title,
                    'description' => $poll->description,
                ],
                'results' => $results,
                'total_votes' => $totalVotes
            ]
        ]);
    }
}
