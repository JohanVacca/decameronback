<?php

namespace App\Http\Requests\Hotel;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Helpers\ResponseHelper;
use Symfony\Component\HttpFoundation\Response;
use App\Models\TipoHabitacion;
use App\Models\Acomodacion;

class CrearHotelRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta solicitud.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene las reglas de validación que se aplican a la solicitud.
     */
    public function rules(): array
    {
        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('hoteles', 'nombre')
            ],
            'direccion' => ['required', 'string', 'max:255'],
            'ciudad' => ['required', 'string', 'max:100'],
            'nit' => [
                'required',
                'string',
                'max:20',
                Rule::unique('hoteles', 'nit')
            ],
            'numeroHabitaciones' => ['required', 'integer', 'min:1', 'max:300'],

            'habitaciones' => ['required', 'array', 'min:1'],
            'habitaciones.*.cantidad' => ['required', 'integer', 'min:1'],
            'habitaciones.*.tipoHabitacionCodigo' => 'required|string|exists:tipos_habitacion,codigo',
            'habitaciones.*.tipoAcomodacionCodigo' => 'required|string|exists:acomodaciones,codigo',
        ];
    }

    /**
     * Mensajes de error personalizados para la validación.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => __('validation.required', ['attribute' => 'nombre']),
            'nombre.unique' => __('validation.unique', ['attribute' => 'nombre']),
            'nit.required' => __('validation.required', ['attribute' => 'NIT']),
            'nit.unique' => __('validation.unique', ['attribute' => 'NIT']),
            'numeroHabitaciones.required' => __(
                'validation.required',
                ['attribute' => 'número de habitaciones']
            ),
            'habitaciones.required' => __('validation.required', ['attribute' => 'habitaciones']),
            'habitaciones.*.tipoHabitacionCodigo.required' => __(
                'validation.required',
                ['attribute' => 'tipo de habitación']
            ),
            'habitaciones.*.tipoHabitacionCodigo.integer' => __(
                'validation.integer',
                ['attribute' => 'tipo de habitación']
            ),
            'habitaciones.*.tipoHabitacionCodigo.exists' => __(
                'validation.exists',
                ['attribute' => 'tipo de habitación']
            ),
            'habitaciones.*.tipoAcomodacionCodigo.required' => __(
                'validation.required',
                ['attribute' => 'tipo de acomodación']
            ),
            'habitaciones.*.tipoAcomodacionCodigo.integer' => __(
                'validation.integer',
                ['attribute' => 'tipo de acomodación']
            ),
            'habitaciones.*.tipoAcomodacionCodigo.exists' => __(
                'validation.exists',
                ['attribute' => 'tipo de acomodación']
            ),
        ];
    }

    /**
     * Maneja un intento fallido de validación.
     */
    protected function failedValidation(Validator $validator): void
    {
        $errors = (new ValidationException($validator))->errors();

        $response = ResponseHelper::sendResponse(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            __('validation.requestError'),
            ['errors' => $errors]
        );

        throw new ValidationException($validator, $response);
    }

    /**
     * Validaciones adicionales de reglas de negocio.
     */
    protected function passedValidation()
    {
        $totalHabitaciones = 0;
        $habitacionKeys = [];

        foreach ($this->habitaciones as $habitacion) {
            $tipoCodigo = $habitacion['tipoHabitacionCodigo'];
            $acomodacionCodigo = $habitacion['tipoAcomodacionCodigo'];
            $cantidad = $habitacion['cantidad'];

            // 1. Evitar tipos de habitaciones y acomodaciones duplicadas
            $key = $tipoCodigo . '-' . $acomodacionCodigo;
            if (isset($habitacionKeys[$key])) {
                throw ValidationException::withMessages([
                    'habitaciones' => "No pueden existir tipos de habitaciones y acomodaciones repetidas."
                ]);
            }
            $habitacionKeys[$key] = true;

            // 2. Validar que la acomodación pertenece al tipo de habitación
            $tipoHabitacion = TipoHabitacion::find($tipoCodigo);
            $acomodacion = Acomodacion::find($acomodacionCodigo);

            if (!$tipoHabitacion || !$acomodacion) {
                throw ValidationException::withMessages([
                    'habitaciones' => "El tipo de habitación o acomodación especificada no existe."
                ]);
            }

            if (!$tipoHabitacion->acomodaciones->contains('codigo', $acomodacionCodigo)) {
                throw ValidationException::withMessages([
                    'habitaciones' => "La acomodación '{$acomodacion->descripcion}' no es
                        válida para la habitación '{$tipoHabitacion->descripcion}'."
                ]);
            }

            $totalHabitaciones += $cantidad;
        }

        // 3. Validar que la cantidad de habitaciones no supere el máximo
        if ($totalHabitaciones > $this->numeroHabitaciones) {
            throw ValidationException::withMessages([
                'numeroHabitaciones' => "El total de habitaciones asignadas ({$totalHabitaciones}) no
                    puede superar el máximo permitido ({$this->numeroHabitaciones})."
            ]);
        }
    }
}
