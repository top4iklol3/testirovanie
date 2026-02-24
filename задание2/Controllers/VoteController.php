<?php
// app/Http/Controllers/VoteController.php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\Option;
use App\Models\Vote;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class VoteController extends Controller
{
    public function vote(Request $request, Poll $poll): JsonResponse
    {
        try {
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
                    'success' => false,  // Было: 'success' false,
                    'message' => 'Срок голосования истек'
                ], 403);
            }

            $ipAddress = $request->ip();

            // Валидация в зависимости от типа опроса
            if ($poll->allow_multiple_votes) {
                $request->validate([
                    'option_id' => 'required|array|min:1',
                    'option_id.*' => 'required|integer|exists:options,id'
                ]);
                $optionIds = $request->option_id;
            } else {
                $request->validate([
                    'option_id' => 'required|integer|exists:options,id'
                ]);
                $optionIds = [$request->option_id];

                // Проверка на повторное голосование (только для одиночных голосов)
                if ($poll->hasUserVoted($ipAddress)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Вы уже голосовали в этом опросе'
                    ], 403);
                }
            }


            foreach ($optionIds as $optionId) {
                $option = Option::find($optionId);
                if (!$option || $option->poll_id != $poll->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Опция не принадлежит этому опросу' // Сообщение должно быть именно таким
                    ], 403);
                }
            }

            // Запись голосов
            $votedCount = 0;
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

                // СОЗДАЕМ ЗАПИСЬ ГОЛОСА
                Vote::create([
                    'poll_id' => $poll->id,
                    'option_id' => $optionId,
                    'ip_address' => $ipAddress,
                    'user_agent' => $request->userAgent()
                ]);

                // УВЕЛИЧИВАЕМ СЧЕТЧИК ГОЛОСОВ В ОПЦИИ
                Option::where('id', $optionId)->increment('votes_count');
                $votedCount++;
            }

            if ($votedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Не удалось сохранить голоса'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Голос успешно учтен',
                'data' => $poll->load('options')
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Ошибка в VoteController: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Внутренняя ошибка сервера: ' . $e->getMessage()
            ], 500);
        }
    }

    // МЕТОД RESULTS
    public function results(Poll $poll): JsonResponse
    {
        try {
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
        } catch (\Exception $e) {
            \Log::error('Ошибка в results: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении результатов: ' . $e->getMessage()
            ], 500);
        }
    }
}
