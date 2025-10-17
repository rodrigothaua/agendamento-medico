<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\ScheduleBlock;
use Carbon\Carbon;

class SettingController extends Controller
{
    /**
     * Display the settings dashboard
     */
    public function index()
    {
        $settings = $this->getDefaultSettings();
        $upcomingBlocks = ScheduleBlock::getUpcoming(5);
        
        return view('admin.settings.index', compact('settings', 'upcomingBlocks'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $request->validate([
            'clinic_name' => 'required|string|max:255',
            'doctor_name' => 'required|string|max:255',
            'clinic_phone' => 'nullable|string|max:20',
            'clinic_email' => 'nullable|email|max:255',
            'clinic_address' => 'nullable|string|max:500',
            'default_appointment_price' => 'required|numeric|min:0',
            'appointment_duration' => 'required|integer|min:15|max:480',
        ]);

        Setting::set('clinic_name', $request->clinic_name, 'string', 'general');
        Setting::set('doctor_name', $request->doctor_name, 'string', 'general');
        Setting::set('clinic_phone', $request->clinic_phone, 'string', 'general');
        Setting::set('clinic_email', $request->clinic_email, 'string', 'general');
        Setting::set('clinic_address', $request->clinic_address, 'string', 'general');
        Setting::set('default_appointment_price', $request->default_appointment_price, 'number', 'general');
        Setting::set('appointment_duration', $request->appointment_duration, 'number', 'general');

        return redirect()->back()->with('success', 'Configurações gerais atualizadas com sucesso!');
    }

    /**
     * Update schedule settings
     */
    public function updateSchedule(Request $request)
    {
        $request->validate([
            'work_start_time' => 'required|date_format:H:i',
            'work_end_time' => 'required|date_format:H:i|after:work_start_time',
            'lunch_start_time' => 'nullable|date_format:H:i',
            'lunch_end_time' => 'nullable|date_format:H:i|after:lunch_start_time',
            'work_days' => 'required|array|min:1',
            'work_days.*' => 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'advance_booking_days' => 'required|integer|min:1|max:365',
            'cancellation_hours' => 'required|integer|min:1|max:72',
        ]);

        Setting::set('work_start_time', $request->work_start_time, 'string', 'schedule');
        Setting::set('work_end_time', $request->work_end_time, 'string', 'schedule');
        Setting::set('lunch_start_time', $request->lunch_start_time, 'string', 'schedule');
        Setting::set('lunch_end_time', $request->lunch_end_time, 'string', 'schedule');
        Setting::set('work_days', json_encode($request->work_days), 'json', 'schedule');
        Setting::set('advance_booking_days', $request->advance_booking_days, 'number', 'schedule');
        Setting::set('cancellation_hours', $request->cancellation_hours, 'number', 'schedule');

        return redirect()->back()->with('success', 'Configurações de horário atualizadas com sucesso!');
    }

    /**
     * Display schedule blocks management
     */
    public function scheduleBlocks()
    {
        $blocks = ScheduleBlock::where('is_active', true)
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(20);

        return view('admin.settings.schedule-blocks', compact('blocks'));
    }

    /**
     * Store a new schedule block
     */
    public function storeScheduleBlock(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'type' => 'required|in:full_day,time_range',
            'start_time' => 'required_if:type,time_range|nullable|date_format:H:i',
            'end_time' => 'required_if:type,time_range|nullable|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
        ]);

        ScheduleBlock::create([
            'date' => $request->date,
            'type' => $request->type,
            'start_time' => $request->type === 'time_range' ? $request->start_time : null,
            'end_time' => $request->type === 'time_range' ? $request->end_time : null,
            'reason' => $request->reason,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Bloqueio de horário criado com sucesso!');
    }

    /**
     * Update a schedule block
     */
    public function updateScheduleBlock(Request $request, ScheduleBlock $block)
    {
        $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:full_day,time_range',
            'start_time' => 'required_if:type,time_range|nullable|date_format:H:i',
            'end_time' => 'required_if:type,time_range|nullable|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
        ]);

        $block->update([
            'date' => $request->date,
            'type' => $request->type,
            'start_time' => $request->type === 'time_range' ? $request->start_time : null,
            'end_time' => $request->type === 'time_range' ? $request->end_time : null,
            'reason' => $request->reason,
        ]);

        return redirect()->back()->with('success', 'Bloqueio de horário atualizado com sucesso!');
    }

    /**
     * Delete a schedule block
     */
    public function destroyScheduleBlock(ScheduleBlock $block)
    {
        $block->delete();
        return redirect()->back()->with('success', 'Bloqueio de horário removido com sucesso!');
    }

    /**
     * Get default settings with fallback values
     */
    private function getDefaultSettings()
    {
        return [
            'general' => [
                'clinic_name' => Setting::get('clinic_name', 'Clínica Saúde'),
                'doctor_name' => Setting::get('doctor_name', 'Dr. João Silva'),
                'clinic_phone' => Setting::get('clinic_phone', ''),
                'clinic_email' => Setting::get('clinic_email', ''),
                'clinic_address' => Setting::get('clinic_address', ''),
                'default_appointment_price' => Setting::get('default_appointment_price', 150.00),
                'appointment_duration' => Setting::get('appointment_duration', 60),
            ],
            'schedule' => [
                'work_start_time' => Setting::get('work_start_time', '08:00'),
                'work_end_time' => Setting::get('work_end_time', '18:00'),
                'lunch_start_time' => Setting::get('lunch_start_time', '12:00'),
                'lunch_end_time' => Setting::get('lunch_end_time', '13:00'),
                'work_days' => Setting::get('work_days', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
                'advance_booking_days' => Setting::get('advance_booking_days', 30),
                'cancellation_hours' => Setting::get('cancellation_hours', 24),
            ],
            'notification' => [
                'email_notifications' => Setting::get('email_notifications', true),
                'sms_notifications' => Setting::get('sms_notifications', false),
                'reminder_hours' => Setting::get('reminder_hours', 24),
            ]
        ];
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean', 
            'reminder_hours' => 'required|integer|min:1|max:168',
        ]);

        Setting::set('email_notifications', $request->boolean('email_notifications'), 'boolean', 'notification');
        Setting::set('sms_notifications', $request->boolean('sms_notifications'), 'boolean', 'notification');
        Setting::set('reminder_hours', $request->reminder_hours, 'number', 'notification');

        return redirect()->back()->with('success', 'Configurações de notificação atualizadas com sucesso!');
    }

    /**
     * Export settings as JSON
     */
    public function exportSettings()
    {
        $settings = Setting::all();
        
        $filename = 'settings_backup_' . date('Y-m-d_H-i-s') . '.json';
        
        return response()->json($settings)->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import settings from JSON
     */
    public function importSettings(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json'
        ]);

        $file = $request->file('settings_file');
        $content = file_get_contents($file->getRealPath());
        $settings = json_decode($content, true);

        if (!$settings) {
            return redirect()->back()->with('error', 'Arquivo de configurações inválido!');
        }

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'group' => $setting['group'],
                    'description' => $setting['description'] ?? null
                ]
            );
        }

        return redirect()->back()->with('success', 'Configurações importadas com sucesso!');
    }
}
