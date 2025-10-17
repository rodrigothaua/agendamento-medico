<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'patient_id',
        'doctor_id', // Se estiver usando o campo opcional
        'scheduled_at',
        'status',
        'payment_id',
    ];

    /**
     * Garante que 'scheduled_at' seja tratado como um objeto Carbon (Datetime).
     */
    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Um agendamento pertence a um paciente.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Um agendamento tem (ou deve ter) um pagamento.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}