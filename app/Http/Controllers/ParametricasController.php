<?php

namespace App\Http\Controllers;

use App\Models\Acomodacion;
use App\Models\TipoHabitacion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Exception;

class ParametricasController extends Controller
{
    /**
     * Clear parameters cache.
     *
     * @return JsonResponse
     */
    public function clearParametersCache(): JsonResponse
    {
        //Cuando haya cambios en el método y queramos verlos reflejados en postman
        // habrá que limpiar caché solicitando dentro de 'forget()'
        // lo que queremos limpiar si no, el Postman nos arrojará los mismos datos.
        Cache::forget('parameters');
        return $this->sendResponse(
            Response::HTTP_OK,
            __('validation.genericSuccess'),
            []
        );
    }

    /**
     * Este controlador cumple la función de proveer al Frontend de todos los 'Selects'
     * que pueda necesitar para construir vistas, nosotros le brindamos las paramétricas empaquetadas
     * en forma de Colección las cuales se guardarán igualmente en Caché. Y puede haber más de un método aquí
     *
     * @return JsonResponse
     */
    public function obtenerParametricas(): JsonResponse
    {
        try {
            $parameters = Cache::remember('parameters', 300, function () {
                return [
                    'tiposAcomodacion' => Acomodacion::all(),
                    'tiposHabitacion' => TipoHabitacion::all(),
                ];
            });

            $parameters = collect($parameters);

            return $this->sendResponse(
                Response::HTTP_OK,
                __('validation.getAllSuccess'),
                ['data' => $parameters]
            );
        } catch (Exception $e) {
            return $this->sendResponse(
                Response::HTTP_INTERNAL_SERVER_ERROR,
                $e->getMessage(),
                $e
            );
        }
    }
}
