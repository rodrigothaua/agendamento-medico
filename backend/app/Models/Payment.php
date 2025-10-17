<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     */
    protected $fillable = [
        'appointment_id',
        'amount',
        'currency',
        'method',
        'transaction_id',
        'status',
        'paid_at',
        'transaction_date',
        'stripe_payment_intent_id',
        'stripe_payment_method_id',
        'stripe_charge_id',
        'metadata',
    ];

    /**
     * Os atributos que devem ser convertidos para datas.
     */
    protected $dates = [
        'paid_at',
        'transaction_date',
        'created_at',
        'updated_at',
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     */
    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'transaction_date' => 'datetime',
    ];
    
    /**
     * Um pagamento pertence a um agendamento.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}