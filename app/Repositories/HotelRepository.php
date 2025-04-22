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
                if (
                    Hotel::where('nombre', $data['nombre'])
                        ->orWhere('nit', $data['nit'])
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'hotel' => 'Ya existe un hotel con este nombre o NIT.',
                    ]);
                }

                $hotel = $this->store([
                    'nombre'             => $data['nombre'],
                    'direccion'          => $data['direccion'],
                    'ciudad'             => $data['ciudad'],
                    'nit'                => $data['nit'],
                    'numeroHabitaciones' => $data['numeroHabitaciones'],
                ]);
            }

            $totalHotelHabitaciones = (int) $hotel->numeroHabitaciones;
            $totalHabitaciones = 0;
            $habitacionKeys = [];

            $habitacionesAInsertar = [];

            foreach ($data['habitaciones'] as $habitacion) {
                $this->validarYPrepararHabitacion(
                    $hotel,
                    $habitacion,
                    $totalHabitaciones,
                    $totalHotelHabitaciones,
                    $habitacionKeys,
                    $habitacionesAInsertar
                );
            }

            $this->habitacionRepository->insertarVarias($habitacionesAInsertar);
            return $hotel->load('habitaciones');
        });
    }

    /**
     * Valida los datos de una habitación y la prepara para ser insertada en bloque.
     *
     * Este método realiza múltiples validaciones, incluyendo:
     * - Comprobación de combinaciones duplicadas de tipo y acomodación.
     * - Validación del total máximo de habitaciones permitidas por hotel.
     * - Verificación de existencia de códigos de tipo y acomodación.
     * - Verificación de compatibilidad entre tipo de habitación y tipo de acomodación.
     *
     * Si todas las validaciones son exitosas, añade la cantidad indicada de habitaciones
     * al array $habitacionesAInsertar, que será usado para hacer un insert masivo a la BD.
     *
     * @param Hotel $hotel Instancia del hotel al que se asociarán las habitaciones.
     * @param array $habitacion Datos de la habitación (tipoHabitacionCodigo, tipoAcomodacionCodigo, cantidad).
     * @param int   &$totalHabitaciones Total acumulado de habitaciones agregadas hasta el momento.
     * @param int   $totalHotelHabitaciones Límite máximo de habitaciones permitidas para el hotel.
     * @param array &$habitacionKeys Claves únicas para prevenir duplicidad (tipo + acomodación).
     * @param array &$habitacionesAInsertar Array que se llenará con los datos para insert masivo.
     *
     * @throws ValidationException Si alguna validación falla.
     */
    private function validarYPrepararHabitacion(
        Hotel $hotel,
        array $habitacion,
        int &$totalHabitaciones,
        int $totalHotelHabitaciones,
        array &$habitacionKeys,
        array &$habitacionesAInsertar
    ): void {
        $tipoCodigo        = (string) $habitacion['tipoHabitacionCodigo'];
        $acomodacionCodigo = (string) $habitacion['tipoAcomodacionCodigo'];
        $cantidad          = (int) $habitacion['cantidad'];

        // Evitar combinaciones duplicadas de tipo y acomodación
        $key = $tipoCodigo . '-' . $acomodacionCodigo;
        if (array_key_exists($key, $habitacionKeys)) {
            throw ValidationException::withMessages([
                'habitaciones' => "No pueden existir tipos de habitaciones y acomodaciones
                repetidas en el mismo hotel.",
            ]);
        }
        $habitacionKeys[$key] = true;

        // Validar que el total asignado no supere el máximo permitido
        $totalHabitaciones += $cantidad;
        if ($totalHabitaciones > $totalHotelHabitaciones) {
            throw ValidationException::withMessages([
                'numeroHabitaciones' => "El total de habitaciones asignadas ({$totalHabitaciones}) no puede superar
                el máximo permitido ({$hotel->numeroHabitaciones}).",
            ]);
        }

        // Verificar que existan el tipo de habitación y la acomodación
        $tipoHabitacion = $this->tipoHabitacionRepository->obtenerPorCodigo($tipoCodigo);
        $acomodacion    = $this->acomodacionRepository->obtenerPorCodigo($acomodacionCodigo);

        if (!$tipoHabitacion || !$acomodacion) {
            throw ValidationException::withMessages([
                'tipoHabitacionCodigo' => "El tipo de habitación '{$tipoCodigo}' o la acomodación
                '{$acomodacionCodigo}' no existe.",
            ]);
        }

        if (!$tipoHabitacion->acomodaciones->contains('codigo', $acomodacionCodigo)) {
            throw ValidationException::withMessages([
                'tipoAcomodacionCodigo' => "La acomodación '{$acomodacion->descripcion}' no es válida para
                el tipo de habitación '{$tipoHabitacion->descripcion}'.",
            ]);
        }

        // Cargamos en el array las habitaciones para hacer insert masivo
        for ($i = 0; $i < $cantidad; $i++) {
            $habitacionesAInsertar[] = [
                'hotelId'               => $hotel->id,
                'tipoHabitacionCodigo'  => $tipoCodigo,
                'tipoAcomodacionCodigo' => $acomodacionCodigo,
                'created_at'            => now(),
                'updated_at'            => now(),
            ];
        }
    }
}
