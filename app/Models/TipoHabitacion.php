<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoHabitacion extends Model
{
    use HasFactory;

    protected $table = 'tipos_habitacion';
    protected $fillable = ['codigo', 'descripcion'];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    public function acomodaciones()
    {
        return $this->belongsToMany(Acomodacion::class, 'tipo_habitacion_acomodacion', 'tipoHabitacionCodigo', 'tipoAcomodacionCodigo');
    }

    public function habitaciones()
    {
        return $this->hasMany(Habitacion::class, 'tipoHabitacionCodigo', 'codigo');
    }
}
