<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Models\Conjunto;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Pagination\LengthAwarePaginator;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * Envía la respuesta
     *
     * @param int               $statusCode Código de estado HTTP
     * @param string            $message    Mensaje de error
     * @param array|Exception   $data       Arreglo de datos o excepción
     *
     * @return JsonResponse
     */
    public function sendResponse($statusCode = 200, $message = null, $data = null): JsonResponse
    {
        return ResponseHelper::sendResponse($statusCode, $message, $data);
    }

    /**
     * Format pagination data.
     *
     * @param LengthAwarePaginator $data
     * @return array
     */
    protected function formatPaginate(LengthAwarePaginator $data): array
    {
        return [
            'data' => $data->items(),
            'links' => [
                'next' => $data->nextPageUrl(),
                'prev' => $data->previousPageUrl(),
            ],
            'current_page' => $data->currentPage(),
            'per_page' => intval($data->perPage()),
            'total' => $data->total(),
            'last_page' => $data->lastPage()
        ];
    }

    /**
     * Get the relationship query for a Conjunto model.
     *
     * @param Conjunto $conjunto
     * @param string $relacion
     * @param array $withRelations
     * @return Builder|null
     */
    public function getRelacionConjunto(Conjunto $conjunto, string $relacion, array $withRelations = [])
    {
        if (method_exists($conjunto, $relacion)) {
            $query = $conjunto->$relacion()->getQuery();

            if (!empty($withRelations)) {
                $query->with($withRelations);
            }

            return $query;
        }

        return null;
    }
}
