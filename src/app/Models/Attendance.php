<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in_at',
        'clock_out_at',
        'note',
    ];

    protected $casts = [
        'work_date' => 'date',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes(): HasMany
    {
        return $this->hasMany(BreakTime::class);
    }

    public function correctionRequests(): HasMany
    {
        return $this->hasMany(CorrectionRequest::class);
    }

    public function getBreakMinutesAttribute(): int
    {
        return $this->breakTimes
            ->filter(fn($b) => $b->break_start_at && $b->break_end_at)
            ->sum(fn($b) => max(0, $b->break_end_at->diffInMinutes($b->break_start_at)));
    }

    public function getWorkMinutesAttribute(): ?int
    {
        if (!$this->clock_in_at || !$this->clock_out_at) return null;

        $total = $this->clock_out_at->diffInMinutes($this->clock_in_at);

        return max(0, $total - $this->break_minutes);
    }

    public function getBreakTimeFormattedAttribute(): string
    {
        return $this->formatMinutes($this->break_minutes);
    }

    public function getWorkTimeFormattedAttribute(): string
    {
        return $this->formatMinutes($this->work_minutes);
    }

    private function formatMinutes(?int $minutes): string
    {
        if ($minutes === null) return '';
        $h = intdiv($minutes, 60);
        $m = $minutes % 60;
        return sprintf('%d:%02d', $h, $m);
    }

}
