<?php
// app/Models/Poll.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'allow_multiple_votes',
        'ends_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'allow_multiple_votes' => 'boolean',
        'ends_at' => 'datetime'
    ];

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function hasUserVoted(string $ipAddress): bool
    {
        if ($this->allow_multiple_votes) {
            // Для множественного голосования проверяем, голосовал ли за КАКУЮ-ЛИБО опцию
            // Возвращаем false, так как при множественном голосовании можно голосовать много раз
            return false;
        }

        // Для обычного голосования проверяем, голосовал ли вообще
        return $this->votes()->where('ip_address', $ipAddress)->exists();
    }

    public function isExpired(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function getResultsAttribute(): array
    {
        $results = [];
        $totalVotes = $this->votes()->count(); // Используем прямой подсчет голосов

        foreach ($this->options as $option) {
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

        return $results;
    }

    public function getTotalVotesAttribute(): int
    {
        return $this->votes()->count();
    }
}
