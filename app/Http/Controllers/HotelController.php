<?php

namespace App\Http\Controllers;

use App\Http\Requests\Hotel\ActualizarHotelRequest;
use App\Http\Requests\Hotel\CrearHotelRequest;
use App\Interfaces\Services\IHotelService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class HotelController extends Controller
{
    protected $hotelService;
    public function __construct(IHotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    /**
     * Crear un Hotel
     *
     * @param CrearHotelRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function crearHotel(CrearHotelRequest $request): JsonResponse
    {
        try {
            $hotel = $this->hotelService->crearHotel($request->all());

            return $this->sendResponse(
                Response::HTTP_OK,
                __('validation.genericSuccess'),
                ['data' => $hotel]
            );
        } catch (Exception $e) {
            return $this->sendResponse(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Mostrar el hotel especificado
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function obtenerHotel(Request $request, int $id): JsonResponse
    {
        try {
            $hotel = $this->hotelService->obtenerHotel($id);

            return $this->sendResponse(
                Response::HTTP_OK,
                __('validation.showSuccess'),
                ['data' => $hotel]
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendResponse(
                Response::HTTP_NOT_FOUND,
                __('validation.notFound')
            );
        } catch (Exception $e) {
            return $this->sendResponse(
                $e->getCode(),
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Mostrar todos los Hoteles
     *
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function obtenerHoteles(Request $request): JsonResponse
    {
        try {
            $hoteles = $this->hotelService->obtenerHoteles();

            return $this->sendResponse(
                Response::HTTP_OK,
                __('validation.getAllSuccess'),
                $hoteles
            );
        } catch (Exception $e) {
            return $this->sendResponse(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Borrar un hotel por completo
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function eliminarHotel(Request $request, int $id): JsonResponse
    {
        try {
            $this->hotelService->eliminarHotel($id);
            return $this->sendResponse(
                Response::HTTP_OK,
                __('validation.deleteSuccess')
            );
        } catch (ModelNotFoundException $e) {
            return $this->sendResponse(
                Response::HTTP_NOT_FOUND,
                __('validation.notFound')
            );
        } catch (Exception $e) {
            return $this->sendResponse(
                $e->getCode(),
                $e->getMessage(),
                $e
            );
        }
    }

    /**
     * Actualizar un Hotel
     *
     * @param ActualizarHotelRequest $request
     * @param int $id
     * @return JsonResponse
     * @throws Exception
     */
    public function actualizarHotel(ActualizarHotelRequest $request, int $id): JsonResponse
    {
        try {
            $hotel = $this->hotelService->actualizarHotel($request->all(), $id);

            return $this->sendResponse(
                Response::HTTP_OK,
                __('validation.updateSuccess'),
                ['data' => $hotel]
            );
        } catch (Exception $e) {
            return $this->sendResponse(
                $e->getCode(),
                $e->getMessage(),
                $e
            );
        }
    }
}
