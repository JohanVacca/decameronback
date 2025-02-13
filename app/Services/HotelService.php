<?php

namespace App\Services;

use App\Http\Controllers\Controller;
use App\Interfaces\Repositories\IHabitacionRepository;
use App\Interfaces\Repositories\IHotelRepository;
use App\Interfaces\Services\IHotelService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     * Constructor del servicio de hoteles.
     *
     * @param IHotelRepository $hotelRepository       Repositorio de hoteles.
     * @param IHabitacionRepository $habitacionRepository Repositorio de habitaciones.
     */
    public function __construct(IHotelRepository $hotelRepository, IHabitacionRepository $habitacionRepository)
    {
        $this->hotelRepository = $hotelRepository;
        $this->habitacionRepository = $habitacionRepository;
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
     * Obtiene un hotel por su id junto con las relaciones definidas.
     *
     * @param mixed $id Identificador del hotel.
     *
     * @return mixed Retorna el hotel encontrado.
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

        return $hotel;
    }

    /**
     * Obtiene todos los hoteles con sus relaciones definidas.
     *
     * @return mixed Retorna la colección de hoteles.
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
