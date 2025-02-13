<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitaciones';

    protected $fillable = ['hotelId', 'tipoHabitacionCodigo', 'tipoAcomodacionCodigo', 'infoAdicional'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotelId');
    }

    public function tipoHabitacion()
    {
        return $this->belongsTo(TipoHabitacion::class, 'tipoHabitacionCodigo', 'codigo');
    }

    public function tipoAcomodacion()
    {
        return $this->belongsTo(Acomodacion::class, 'tipoAcomodacionCodigo', 'codigo');
    }
}
