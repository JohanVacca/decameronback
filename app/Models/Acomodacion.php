<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acomodacion extends Model
{
    use HasFactory;

    protected $fillable = ['codigo', 'descripcion'];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'acomodaciones';

    public function tiposHabitacion()
    {
        return $this->belongsToMany(TipoHabitacion::class, 'tipo_habitacion_acomodacion', 'tipoAcomodacionCodigo', 'tipoHabitacionCodigo');
    }

    public function habitaciones()
    {
        return $this->hasMany(Habitacion::class, 'tipoAcomodacionCodigo', 'codigo');
    }
}
