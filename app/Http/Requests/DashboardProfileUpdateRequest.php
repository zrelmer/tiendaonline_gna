<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DashboardProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Usuario $usuario */
        $usuario = $this->user();

        return [
            'Usu_Nombre' => ['required', 'string', 'max:200'],
            'Usu_Correo' => [
                'required',
                'string',
                'email',
                'max:150',
                Rule::unique('tb_usuario', 'Usu_Correo')->ignore($usuario->Id_Usuario, 'Id_Usuario'),
            ],
            'Usu_Telefono' => ['required', 'string', 'max:20'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Usu_Nombre.required' => 'El nombre es obligatorio.',
            'Usu_Correo.required' => 'El correo es obligatorio.',
            'Usu_Correo.email' => 'Ingresa un correo válido.',
            'Usu_Correo.unique' => 'Este correo ya está registrado.',
            'Usu_Telefono.required' => 'El teléfono es obligatorio.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('dashboard')
                ->withErrors($validator, 'profile')
                ->withInput()
                ->with('tab', 'profile')
        );
    }
}
