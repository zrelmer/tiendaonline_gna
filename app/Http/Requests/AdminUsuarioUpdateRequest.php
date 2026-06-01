<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AdminUsuarioUpdateRequest extends FormRequest
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
            'Id_Rol' => ['required', 'integer', 'exists:tb_rol,Id_Rol'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'Id_Rol.required' => 'Selecciona un rol.',
            'Id_Rol.exists' => 'El rol seleccionado no es válido.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        /** @var Usuario $usuario */
        $usuario = $this->route('usuario');

        throw new HttpResponseException(
            redirect()
                ->route('admin.usuarios.edit', $usuario)
                ->withErrors($validator)
                ->withInput()
        );
    }
}
