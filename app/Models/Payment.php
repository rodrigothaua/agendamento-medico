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
        'method',
        'transaction_id',
        'status',
        'paid_at',
    ];

    /**
     * Os atributos que devem ser convertidos para datas.
     */
    protected $dates = [
        'paid_at',
        'created_at',
        'updated_at',
    ];
    
    /**
     * Um pagamento pertence a um agendamento.
     */
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}