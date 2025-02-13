<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Exception;
use Illuminate\Support\Facades\Log;

class ResponseHelper
{
    /**
     * Variables de la clase.
     */
    private static $responseStatus = [
        'status' => [
            'statusCode' => 200,
            'message' => 'Operación realizada con éxito',
        ]
    ];

    /**
     * Constantes de la clase.
     */
    public const HTTP_ERROR_CODES = [
        400, 401, 403, 404, 405, 422, 500, 501, 502, 503
    ];

    /**
     * Asigna el estado de la respuesta
     *
     * @param int    $statusCode Código de estado HTTP
     * @param string $message    Mensaje de error
     * @param array  $errors     Array con errores de validación
     *
     * @return void
     */
    private static function setResponseStatus(
        int $statusCode = 200,
        string $message = 'Operación realizada con éxito',
        $errors = null
    ): void {
        self::$responseStatus['status']['statusCode'] = $statusCode;
        self::$responseStatus['status']['message'] = $message;

        if (is_array($errors)) {
            self::$responseStatus['status']['errors'] = $errors;
        }
    }

    /**
     * Envía la respuesta
     *
     * @param int               $statusCode Código de estado HTTP
     * @param string            $message    Mensaje de error
     * @param array|Exception   $data       Arreglo de datos o excepción
     *
     * @return JsonResponse
     */
    public static function sendResponse($statusCode = 200, $message = null, $data = null): JsonResponse
    {
        $message = $message === null ? __('validation.genericSuccess') : $message;
        $errors = null;
        $environment = env('APP_ENV');

        if (is_array($data) && isset($data['errors'])) {
            $errors = $data['errors'];
            unset($data['errors']);
        }

        if (in_array($statusCode, self::HTTP_ERROR_CODES)) {
            if ($environment != 'production') {
                if ($data instanceof Exception) {
                    $errors = [
                        'line' => $data->getLine(),
                        'message' => $data->getMessage(),
                        'file' => $data->getFile()
                    ];
                }
            } else {
                if (in_array($statusCode, [500, 501, 502, 503])) {
                    $message = __('validation.genericError');
                }
            }
        }

        self::setResponseStatus($statusCode, $message, $errors);

        $json = [
            'status' => self::$responseStatus['status']
        ];

        $json['response'] = [];

        if ($data !== null && $environment != 'production') {
            $json['response'] = $data;
        }

        if ($statusCode == 500) {
            Log::error("Error HTTP 500", [
                'statusCode' => $statusCode,
                'message' => $message,
                'data' => $data
            ]);
        }

        if ($statusCode == 501) {
            Log::error("Error HTTP 501", [
                'statusCode' => $statusCode,
                'message' => $message,
                'data' => $data
            ]);
        }

        return response()->json($json, self::$responseStatus['status']['statusCode']);
    }
}
