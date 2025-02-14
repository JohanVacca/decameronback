<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Interfaces\Repositories\IAcomodacionRepository;
use App\Interfaces\Repositories\IHabitacionRepository;
use App\Interfaces\Repositories\IHotelRepository;
use App\Interfaces\Repositories\ITipoHabitacionRepository;
use App\Interfaces\Services\IHotelService;
use App\Models\Acomodacion;
use App\Models\TipoHabitacion;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class HotelService extends Controller implements IHotelService
{
    /**
     * Repositorio de hoteles.
     *
     * @var IHotelRepository
     */
    protected $hotelRepository;

    /**
     * Repositorio de habitaciones.
     *
     * @var IHabitacionRepository
     */
    protected $habitacionRepository;

    /**
     * Repositorio de habitaciones.
     *
     * @var IAcomodacionRepository
     */
    protected $acomodacionRepository;

    /**
     * Repositorio de habitaciones.
     *
     * @var ITipoHabitacionRepository
     */
    protected $tipoHabitacionRepository;

    /**
     * Constructor del servicio de hoteles.
     *
     * @param IHotelRepository $hotelRepository       Repositorio de hoteles.
     * @param IHabitacionRepository $habitacionRepository Repositorio de habitaciones.
     */
    public function __construct(
        IHotelRepository $hotelRepository,
        IHabitacionRepository $habitacionRepository,
        ITipoHabitacionRepository $tipoHabitacionRepository,
        IAcomodacionRepository $acomodacionRepository
    ) {
        $this->hotelRepository = $hotelRepository;
        $this->habitacionRepository = $habitacionRepository;
        $this->tipoHabitacionRepository = $tipoHabitacionRepository;
        $this->acomodacionRepository = $acomodacionRepository;
    }

    /**
     * Crea un hotel con sus habitaciones asociadas.
     *
     * @param array $body Datos del hotel y sus habitaciones.
     *
     * @return mixed Retorna el hotel creado.
     *
     * @throws Exception Si ocurre algún error durante el proceso.
     */
    public function crearHotel(array $body)
    {
        try {
            $hotel = $this->hotelRepository->guardarConHabitaciones($body);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        return $hotel;
    }

    /**
     * Calcula el conteo de habitaciones agrupadas por tipo y acomodación.
     *
     * @param Collection $habitaciones Colección de habitaciones.
     *
     * @return Collection Retorna la colección con el conteo de habitaciones.
     */
    private function calcularInfoHabitaciones($habitaciones)
    {
        $tiposCodigos = $habitaciones->pluck('tipoHabitacionCodigo')->unique();
        $acomodacionCodigos = $habitaciones->pluck('tipoAcomodacionCodigo')->unique();

        $tiposDescripcion = $this->tipoHabitacionRepository->obtenerDescripcionesPorCodigos($tiposCodigos->toArray());

        $acomodacionesDescripcion = $this->acomodacionRepository
        ->obtenerDescripcionesPorCodigos($acomodacionCodigos->toArray());

        return $habitaciones->groupBy(function ($habitacion) {
            return $habitacion->tipoHabitacionCodigo . '-' . $habitacion->tipoAcomodacionCodigo;
        })->map(function ($group, $key) use ($tiposDescripcion, $acomodacionesDescripcion) {
            list($tipo, $acomodacion) = explode('-', $key);

            return [
                'tipoHabitacionn'   => $tiposDescripcion[$tipo],
                'tipoAcomodacion' => $acomodacionesDescripcion[$acomodacion],
                'total'           => $group->count()
            ];
        })->values();
    }

    /**
     * Obtiene un hotel por su id junto con las relaciones definidas y el conteo de habitaciones por tipo y acomodación.
     *
     * @param mixed $id Identificador del hotel.
     *
     * @return mixed Retorna el hotel encontrado, incluyendo el atributo `infoHabitaciones`.
     *
     * @throws Exception Si no se encuentra el hotel o si ocurre un error.
     */
    public function obtenerHotel($id)
    {
        try {
            $hotel = $this->hotelRepository->findById(
                $id,
                ['habitaciones', 'habitaciones.tipoHabitacion', 'habitaciones.tipoAcomodacion']
            );
        } catch (ModelNotFoundException $e) {
            throw new Exception(__('validation.notFound'), Response::HTTP_NOT_FOUND, $e);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        $hotel->infoHabitaciones = $this->calcularInfoHabitaciones($hotel->habitaciones);

        return $hotel;
    }

    /**
     * Obtiene todos los hoteles con sus relaciones definidas y con el conteo de habitaciones
     * por tipo y acomodación en cada hotel.
     *
     * @return mixed Retorna la colección paginada de hoteles, cada uno con el atributo `infoHabitaciones`
     *               que contiene el conteo.
     *
     * @throws Exception Si ocurre algún error durante la obtención.
     */
    public function obtenerHoteles()
    {
        try {
            $hoteles = $this->hotelRepository->all(
                ['habitaciones', 'habitaciones.tipoHabitacion', 'habitaciones.tipoAcomodacion']
            );
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        if (isset($hoteles['data']) && count($hoteles['data']) > 0) {
            foreach ($hoteles['data'] as $hotel) {
                $hotel->infoHabitaciones = $this->calcularInfoHabitaciones($hotel->habitaciones);
            }
        }

        return $hoteles;
    }

    /**
     * Actualiza un hotel y reemplaza sus habitaciones actuales por las nuevas.
     *
     * @param array $body Datos para actualizar el hotel.
     * @param mixed $id   Identificador del hotel a actualizar.
     *
     * @return mixed Retorna el hotel actualizado.
     *
     * @throws Exception Si no se encuentra el hotel o si ocurre algún error durante la transacción.
     */
    public function actualizarHotel(array $body, $id)
    {
        try {
            DB::beginTransaction();
            $this->habitacionRepository->borrarTodasPorHotel($id);
            $hotel = $this->hotelRepository->guardarConHabitaciones($body, true);
            DB::commit();
        } catch (ModelNotFoundException $e) {
            throw new Exception(__('validation.notFound'), Response::HTTP_NOT_FOUND, $e);
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }

        return $hotel;
    }

    /**
     * Elimina un hotel a partir de su id.
     *
     * @param mixed $id Identificador del hotel a eliminar.
     *
     * @return mixed Retorna el resultado de la eliminación.
     *
     * @throws Exception Si no se encuentra el hotel o si ocurre algún error durante la eliminación.
     */
    public function eliminarHotel($id)
    {
        try {
            $hotel = $this->hotelRepository->destroy($id);
        } catch (ModelNotFoundException $e) {
            throw new Exception(__('validation.notFound'), Response::HTTP_NOT_FOUND, $e);
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, $e);
        }
        return $hotel;
    }
}
