<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\Repositories\IAcomodacionRepository;
use App\Interfaces\Repositories\IHabitacionRepository;
use App\Interfaces\Repositories\IHotelRepository;
use App\Interfaces\Repositories\ITipoHabitacionRepository;
use App\Models\Hotel;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Repositorio para gestionar operaciones relacionadas con hoteles.
 *
 * @package App\Repositories
 */
class HotelRepository extends BaseRepository implements IHotelRepository
{
    /**
     * Repositorio de habitaciones.
     *
     * @var IHabitacionRepository
     */
    protected IHabitacionRepository $habitacionRepository;

    /**
     * Repositorio de Tipos de habitación.
     *
     * @var ITipoHabitacionRepository
     */
    protected ITipoHabitacionRepository $tipoHabitacionRepository;

    /**
     * Repositorio de Acomodaciones.
     *
     * @var IAcomodacionRepository
     */
    protected IAcomodacionRepository $acomodacionRepository;

    /**
     * Constructor del repositorio de hoteles.
     */
    public function __construct(
        Hotel $model,
        IHabitacionRepository $habitacionRepository,
        ITipoHabitacionRepository $tipoHabitacionRepository,
        IAcomodacionRepository $acomodacionRepository
    ) {
        parent::__construct($model);
        $this->habitacionRepository = $habitacionRepository;
        $this->tipoHabitacionRepository = $tipoHabitacionRepository;
        $this->acomodacionRepository = $acomodacionRepository;
    }

    /**
     * Almacena un hotel junto con sus habitaciones asociadas. Puede ser usado para crear o actualizar un hotel.
     *
     * Realiza la transacción de creación del hotel y sus habitaciones, validando que:
     * - No se repitan combinaciones de tipos de habitación y acomodación.
     * - El total de habitaciones asignadas no supere el límite definido en el hotel.
     * - Los códigos de tipo de habitación y acomodación existan y sean compatibles.
     *
     * El arreglo de datos debe incluir:
     * - 'nombre' (string)
     * - 'direccion' (string)
     * - 'ciudad' (string)
     * - 'nit' (string)
     * - 'numeroHabitaciones' (int)
     * - 'habitaciones' (array): Cada elemento debe contener:
     *     - 'tipoHabitacionCodigo' (string)
     *     - 'tipoAcomodacionCodigo' (string)
     *     - 'cantidad' (int)
     *
     * @param array $data Datos del hotel y sus habitaciones.
     * @param bool  $actualizar Indica si se está actualizando un hotel existente.
     *
     * @return Hotel Hotel creado con sus habitaciones asociadas.
     *
     * @throws ValidationException Si alguna validación falla.
     * @throws \Exception          Si ocurre un error inesperado durante la transacción.
     */
    public function guardarConHabitaciones(array $data, bool $actualizar = false): Hotel
    {
        return DB::transaction(function () use ($data, $actualizar): Hotel {
            if ($actualizar) {
                $hotel = $this->findById($data['id']);
                $hotel->update([
                    'nombre'             => $data['nombre'],
                    'direccion'          => $data['direccion'],
                    'ciudad'             => $data['ciudad'],
                    'nit'                => $data['nit'],
                    'numeroHabitaciones' => $data['numeroHabitaciones'],
                ]);

                $this->habitacionRepository->borrarTodasPorHotel($hotel->id);
            } else {
                $hotel = $this->store([
                    'nombre'             => $data['nombre'],
                    'direccion'          => $data['direccion'],
                    'ciudad'             => $data['ciudad'],
                    'nit'                => $data['nit'],
                    'numeroHabitaciones' => $data['numeroHabitaciones'],
                ]);
            }

            $totalHotelHabitaciones = (int) $hotel->numeroHabitaciones;
            $totalHabitaciones       = 0;
            $habitacionKeys        = [];

            foreach ($data['habitaciones'] as $habitacion) {
                $this->procesarHabitacion(
                    $hotel,
                    $habitacion,
                    $totalHabitaciones,
                    $totalHotelHabitaciones,
                    $habitacionKeys
                );
            }

            return $hotel->load('habitaciones');
        });
    }

    /**
     * Procesa cada habitación, realizando las validaciones pertinentes y
     * almacenando la habitación mediante el repositorio correspondiente.
     *
     * @param Hotel $hotel                  Hotel al que se asocia la habitación.
     * @param array $habitacion             Datos de la habitación.
     * @param int   &$totalHabitaciones     Total acumulado de habitaciones asignadas.
     * @param int   $totalHotelHabitaciones Número máximo de habitaciones permitidas.
     * @param array &$habitacionKeys        Claves para detectar duplicados.
     *
     * @return void
     *
     * @throws ValidationException
     */
    private function procesarHabitacion(
        Hotel $hotel,
        array $habitacion,
        int &$totalHabitaciones,
        int $totalHotelHabitaciones,
        array &$habitacionKeys
    ): void {
        $tipoCodigo        = (string) $habitacion['tipoHabitacionCodigo'];
        $acomodacionCodigo = (string) $habitacion['tipoAcomodacionCodigo'];
        $cantidad          = (int) $habitacion['cantidad'];

        // Evitar combinaciones duplicadas de tipo y acomodación
        $key = $tipoCodigo . '-' . $acomodacionCodigo;
        if (array_key_exists($key, $habitacionKeys)) {
            throw ValidationException::withMessages([
                'habitaciones' => "No pueden existir tipos de habitaciones y acomodaciones " .
                    "repetidas en el mismo hotel.",
            ]);
        }
        $habitacionKeys[$key] = true;

        // Validar que el total asignado no supere el máximo permitido
        $totalHabitaciones += $cantidad;
        if ($totalHabitaciones > $totalHotelHabitaciones) {
            throw ValidationException::withMessages([
                'numeroHabitaciones' => "El total de habitaciones asignadas " .
                    "({$totalHabitaciones}) no puede superar el máximo permitido " .
                    "({$hotel->numeroHabitaciones}).",
            ]);
        }

        // Verificar que existan el tipo de habitación y la acomodación
        $tipoHabitacion = $this->tipoHabitacionRepository->obtenerPorCodigo($tipoCodigo);
        $acomodacion    = $this->acomodacionRepository->obtenerPorCodigo($acomodacionCodigo);

        if (!$tipoHabitacion || !$acomodacion) {
            throw ValidationException::withMessages([
                'tipoHabitacionCodigo' => "El tipo de habitación '{$tipoCodigo}' o la acomodación " .
                    "'{$acomodacionCodigo}' no existe.",
            ]);
        }

        // Validar que la acomodación sea compatible con el tipo de habitación
        if (!$tipoHabitacion->acomodaciones->contains('codigo', $acomodacionCodigo)) {
            throw ValidationException::withMessages([
                'tipoAcomodacionCodigo' => "La acomodación '{$acomodacion->descripcion}' no es válida " .
                    "para el tipo de habitación '{$tipoHabitacion->descripcion}'.",
            ]);
        }

        for ($i = 0; $i < $cantidad; $i++) {
            $this->habitacionRepository->store([
                'hotelId'               => $hotel->id,
                'tipoHabitacionCodigo'  => $tipoCodigo,
                'tipoAcomodacionCodigo' => $acomodacionCodigo,
            ]);
        }
    }
}
