<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\ScheduleBlock;
use Carbon\Carbon;

class SettingApiController extends Controller
{
    /**
     * Get all settings or specific group
     */
    public function index(Request $request)
    {
        $group = $request->query('group');
        $key = $request->query('key');

        if ($key) {
            $setting = Setting::where('key', $key)->first();
            return response()->json([
                'success' => true,
                'data' => $setting ? [
                    'key' => $setting->key,
                    'value' => $this->castSettingValue($setting->value, $setting->type),
                    'type' => $setting->type,
                    'group' => $setting->group
                ] : null
            ]);
        }

        $query = Setting::query();
        
        if ($group) {
            $query->where('group', $group);
        }

        $settings = $query->get();
        
        $formattedSettings = $settings->mapWithKeys(function ($setting) {
            return [$setting->key => $this->castSettingValue($setting->value, $setting->type)];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedSettings
        ]);
    }

    /**
     * Get settings grouped by category
     */
    public function getGrouped()
    {
        $settings = Setting::all();
        
        $grouped = $settings->groupBy('group')->mapWithKeys(function ($groupSettings, $group) {
            return [$group => $groupSettings->mapWithKeys(function ($setting) {
                return [$setting->key => $this->castSettingValue($setting->value, $setting->type)];
            })];
        });

        return response()->json([
            'success' => true,
            'data' => $grouped
        ]);
    }

    /**
     * Update or create a setting
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'required|in:string,number,boolean,json',
            'group' => 'required|string|max:100',
            'description' => 'nullable|string|max:500'
        ]);

        $setting = Setting::updateOrCreate(
            ['key' => $request->key],
            [
                'value' => $this->formatSettingValue($request->value, $request->type),
                'type' => $request->type,
                'group' => $request->group,
                'description' => $request->description
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Configuração salva com sucesso',
            'data' => [
                'key' => $setting->key,
                'value' => $this->castSettingValue($setting->value, $setting->type),
                'type' => $setting->type,
                'group' => $setting->group
            ]
        ]);
    }

    /**
     * Update multiple settings at once
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'required',
            'settings.*.type' => 'required|in:string,number,boolean,json',
            'settings.*.group' => 'required|string'
        ]);

        $updatedSettings = [];

        foreach ($request->settings as $settingData) {
            $setting = Setting::updateOrCreate(
                ['key' => $settingData['key']],
                [
                    'value' => $this->formatSettingValue($settingData['value'], $settingData['type']),
                    'type' => $settingData['type'],
                    'group' => $settingData['group'],
                    'description' => $settingData['description'] ?? null
                ]
            );

            $updatedSettings[] = [
                'key' => $setting->key,
                'value' => $this->castSettingValue($setting->value, $setting->type)
            ];
        }

        return response()->json([
            'success' => true,
            'message' => count($updatedSettings) . ' configurações atualizadas com sucesso',
            'data' => $updatedSettings
        ]);
    }

    /**
     * Delete a setting
     */
    public function destroy($key)
    {
        $setting = Setting::where('key', $key)->first();
        
        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Configuração não encontrada'
            ], 404);
        }

        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Configuração removida com sucesso'
        ]);
    }

    /**
     * Get clinic configuration for public use
     */
    public function getPublicConfig()
    {
        $publicSettings = [
            'clinic_name' => Setting::get('clinic_name', 'Clínica Saúde'),
            'clinic_phone' => Setting::get('clinic_phone', ''),
            'clinic_email' => Setting::get('clinic_email', ''),
            'clinic_address' => Setting::get('clinic_address', ''),
            'work_start_time' => Setting::get('work_start_time', '08:00'),
            'work_end_time' => Setting::get('work_end_time', '18:00'),
            'work_days' => Setting::get('work_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'appointment_duration' => Setting::get('appointment_duration', 60),
            'advance_booking_days' => Setting::get('advance_booking_days', 30)
        ];

        return response()->json([
            'success' => true,
            'data' => $publicSettings
        ]);
    }

    /**
     * Get schedule blocks
     */
    public function getScheduleBlocks(Request $request)
    {
        $query = ScheduleBlock::where('is_active', true);
        
        if ($request->has('date')) {
            $query->where('date', $request->date);
        }
        
        if ($request->has('from_date') && $request->has('to_date')) {
            $query->whereBetween('date', [$request->from_date, $request->to_date]);
        }

        $blocks = $query->orderBy('date')->orderBy('start_time')->get();

        return response()->json([
            'success' => true,
            'data' => $blocks->map(function ($block) {
                return [
                    'id' => $block->id,
                    'date' => $block->date->format('Y-m-d'),
                    'type' => $block->type,
                    'start_time' => $block->start_time ? $block->start_time->format('H:i') : null,
                    'end_time' => $block->end_time ? $block->end_time->format('H:i') : null,
                    'reason' => $block->reason,
                    'is_active' => $block->is_active
                ];
            })
        ]);
    }

    /**
     * Create a schedule block
     */
    public function createScheduleBlock(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'type' => 'required|in:full_day,time_range',
            'start_time' => 'required_if:type,time_range|nullable|date_format:H:i',
            'end_time' => 'required_if:type,time_range|nullable|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255'
        ]);

        $block = ScheduleBlock::create([
            'date' => $request->date,
            'type' => $request->type,
            'start_time' => $request->type === 'time_range' ? $request->start_time : null,
            'end_time' => $request->type === 'time_range' ? $request->end_time : null,
            'reason' => $request->reason,
            'is_active' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bloqueio criado com sucesso',
            'data' => [
                'id' => $block->id,
                'date' => $block->date->format('Y-m-d'),
                'type' => $block->type,
                'start_time' => $block->start_time ? $block->start_time->format('H:i') : null,
                'end_time' => $block->end_time ? $block->end_time->format('H:i') : null,
                'reason' => $block->reason,
                'is_active' => $block->is_active
            ]
        ]);
    }

    /**
     * Update a schedule block
     */
    public function updateScheduleBlock(Request $request, $id)
    {
        $block = ScheduleBlock::find($id);
        
        if (!$block) {
            return response()->json([
                'success' => false,
                'message' => 'Bloqueio não encontrado'
            ], 404);
        }

        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:full_day,time_range',
            'start_time' => 'required_if:type,time_range|nullable|date_format:H:i',
            'end_time' => 'required_if:type,time_range|nullable|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255'
        ]);

        $block->update([
            'date' => $request->date,
            'type' => $request->type,
            'start_time' => $request->type === 'time_range' ? $request->start_time : null,
            'end_time' => $request->type === 'time_range' ? $request->end_time : null,
            'reason' => $request->reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bloqueio atualizado com sucesso',
            'data' => [
                'id' => $block->id,
                'date' => $block->date->format('Y-m-d'),
                'type' => $block->type,
                'start_time' => $block->start_time ? $block->start_time->format('H:i') : null,
                'end_time' => $block->end_time ? $block->end_time->format('H:i') : null,
                'reason' => $block->reason,
                'is_active' => $block->is_active
            ]
        ]);
    }

    /**
     * Delete a schedule block
     */
    public function deleteScheduleBlock($id)
    {
        $block = ScheduleBlock::find($id);
        
        if (!$block) {
            return response()->json([
                'success' => false,
                'message' => 'Bloqueio não encontrado'
            ], 404);
        }

        $block->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bloqueio removido com sucesso'
        ]);
    }

    /**
     * Check if a datetime is blocked
     */
    public function checkBlocked(Request $request)
    {
        $request->validate([
            'datetime' => 'required|date'
        ]);

        $isBlocked = ScheduleBlock::isBlocked($request->datetime);

        return response()->json([
            'success' => true,
            'data' => [
                'datetime' => $request->datetime,
                'is_blocked' => $isBlocked,
                'blocks' => $isBlocked ? ScheduleBlock::getBlocksForDate(Carbon::parse($request->datetime)->format('Y-m-d')) : []
            ]
        ]);
    }

    /**
     * Cast setting value to appropriate type
     */
    private function castSettingValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'number':
                return is_numeric($value) ? (float) $value : $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Format setting value for storage
     */
    private function formatSettingValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'json':
                return json_encode($value);
            default:
                return (string) $value;
        }
    }
}