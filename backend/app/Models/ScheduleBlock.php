<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ScheduleBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'end_date',
        'start_time',
        'end_time', 
        'reason',
        'type',
        'block_mode',
        'is_active'
    ];

    protected $casts = [
        'date' => 'date',
        'end_date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
        'is_active' => 'boolean'
    ];

    /**
     * Check if a datetime is blocked
     */
    public static function isBlocked($datetime)
    {
        $date = Carbon::parse($datetime)->format('Y-m-d');
        $time = Carbon::parse($datetime)->format('H:i:s');

        return static::where('is_active', true)
            ->where(function ($query) use ($date) {
                // Single date blocks
                $query->where('block_mode', 'single_date')
                      ->where('date', $date);
            })
            ->orWhere(function ($query) use ($date) {
                // Date range blocks
                $query->where('block_mode', 'date_range')
                      ->where('date', '<=', $date)
                      ->where('end_date', '>=', $date);
            })
            ->where(function ($query) use ($time) {
                $query->where('type', 'full_day')
                      ->orWhere(function ($q) use ($time) {
                          $q->where('type', 'time_range')
                            ->where('start_time', '<=', $time)
                            ->where('end_time', '>=', $time);
                      });
            })
            ->exists();
    }

    /**
     * Get active blocks for a specific date
     */
    public static function getBlocksForDate($date)
    {
        return static::where('date', $date)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Get upcoming blocks
     */
    public static function getUpcoming($limit = 10)
    {
        return static::where(function ($query) {
                $query->where('block_mode', 'single_date')
                      ->where('date', '>=', now()->format('Y-m-d'))
                      ->orWhere(function ($q) {
                          $q->where('block_mode', 'date_range')
                            ->where('end_date', '>=', now()->format('Y-m-d'));
                      });
            })
            ->where('is_active', true)
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
    }

    /**
     * Create multiple blocks for a date range
     */
    public static function createDateRangeBlock($startDate, $endDate, $type, $startTime = null, $endTime = null, $reason = null)
    {
        return static::create([
            'date' => $startDate,
            'end_date' => $endDate,
            'type' => $type,
            'block_mode' => 'date_range',
            'start_time' => $type === 'time_range' ? $startTime : null,
            'end_time' => $type === 'time_range' ? $endTime : null,
            'reason' => $reason,
            'is_active' => true,
        ]);
    }

    /**
     * Get a human-readable description of the block
     */
    public function getDescriptionAttribute()
    {
        $dateFormat = 'd/m/Y';
        
        if ($this->block_mode === 'date_range') {
            $start = $this->date->format($dateFormat);
            $end = $this->end_date->format($dateFormat);
            $dateText = $start === $end ? $start : "{$start} a {$end}";
        } else {
            $dateText = $this->date->format($dateFormat);
        }

        if ($this->type === 'time_range' && $this->start_time && $this->end_time) {
            $timeText = " - {$this->start_time->format('H:i')} às {$this->end_time->format('H:i')}";
        } else {
            $timeText = " - Dia Inteiro";
        }

        return $dateText . $timeText;
    }
}
