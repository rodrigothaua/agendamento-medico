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
        'start_time',
        'end_time', 
        'reason',
        'type',
        'is_active'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean'
    ];

    /**
     * Check if a datetime is blocked
     */
    public static function isBlocked($datetime)
    {
        $date = Carbon::parse($datetime)->format('Y-m-d');
        $time = Carbon::parse($datetime)->format('H:i:s');

        return static::where('date', $date)
            ->where('is_active', true)
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
        return static::where('date', '>=', now()->format('Y-m-d'))
            ->where('is_active', true)
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
    }
}
