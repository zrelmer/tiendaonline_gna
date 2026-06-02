<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminCotizacionEmitirRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'lineas' => ['required', 'array', 'min:1'],
            'lineas.*.costo_unit' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'terminos' => ['required', 'string', 'max:5000'],
            'vigencia_dias' => ['required', 'integer', 'min:1', 'max:365'],
            'archivo' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'comentario' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'lineas.required' => 'Debes indicar el precio de al menos una línea.',
            'lineas.min' => 'Debes indicar el precio de al menos una línea.',
            'lineas.*.costo_unit.required' => 'Indica el costo unitario de cada línea.',
            'lineas.*.costo_unit.numeric' => 'El costo unitario debe ser un número válido.',
            'lineas.*.costo_unit.min' => 'El costo unitario no puede ser negativo.',
            'terminos.required' => 'Indica los términos y condiciones de la cotización.',
            'vigencia_dias.required' => 'Indica la vigencia en días.',
            'vigencia_dias.min' => 'La vigencia debe ser de al menos 1 día.',
            'archivo.required' => 'Sube el documento PDF de la cotización emitida.',
            'archivo.mimes' => 'El archivo debe ser PDF.',
            'archivo.max' => 'El PDF no puede superar 10 MB.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('admin.cotizaciones.emitir', $this->route('cotizacion'))
                ->withErrors($validator, 'cotizacion')
                ->withInput()
        );
    }
}
