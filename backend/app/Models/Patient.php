<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa (mass assignable).
     * Incluímos 'cpf' aqui porque é usado no firstOrCreate do Controller.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'cpf',
    ];

    /**
     * Um paciente pode ter muitos agendamentos.
     */
    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}