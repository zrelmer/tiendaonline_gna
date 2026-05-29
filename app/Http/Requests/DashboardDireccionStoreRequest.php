<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DashboardDireccionStoreRequest extends DireccionStoreRequest
{
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            redirect()
                ->route('dashboard')
                ->withErrors($validator, 'direccion')
                ->withInput()
                ->with('tab', 'addresses')
                ->with('abrir_modal_direccion', 'nueva')
        );
    }
}
