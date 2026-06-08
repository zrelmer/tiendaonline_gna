@php
    $esEdicion = isset($marca) && $marca !== null;
    $valor = function (string $campo, mixed $default = '') use ($esEdicion, $marca) {
        $anterior = old($campo);
        if ($anterior !== null) {
            return $anterior;
        }
        if ($esEdicion) {
            return $marca->{$campo} ?? $default;
        }

        return $default;
    };
    $logoActual = $esEdicion && ! empty($marca->Marc_Logo)
        ? asset($marca->Marc_Logo)
        : null;
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-sm-8 m-auto">
                    <form method="POST"
                          action="{{ $formAction }}"
                          enctype="multipart/form-data"
                          class="theme-form theme-form-2 mega-form">
                        @csrf
                        @if (($formMethod ?? 'POST') !== 'POST')
                            @method($formMethod)
                        @endif

                        <div class="card">
                            <div class="card-body">
                                <div class="card-header-2">
                                    <h5>Información de la marca</h5>
                                </div>

                                @if ($esEdicion)
                                    <div class="mb-4 row align-items-center">
                                        <label class="form-label-title col-sm-3 mb-0">ID</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-plaintext mb-0">{{ $marca->Id_Marca }}</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0" for="Nom_Marca">Nombre</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('Nom_Marca') is-invalid @enderror"
                                               type="text"
                                               id="Nom_Marca"
                                               name="Nom_Marca"
                                               value="{{ $valor('Nom_Marca') }}"
                                               placeholder="Ej. HP"
                                               maxlength="200"
                                               required>
                                        @error('Nom_Marca')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="col-sm-3 col-form-label form-label-title" for="slug_Marca">URL (slug)</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('slug_Marca') is-invalid @enderror"
                                               type="text"
                                               id="slug_Marca"
                                               name="slug_Marca"
                                               value="{{ $valor('slug_Marca') }}"
                                               placeholder="ej. hp"
                                               maxlength="200"
                                               required>
                                        <small class="text-muted">Solo minúsculas, números y guiones. Se usa en filtros de la tienda.</small>
                                        @error('slug_Marca')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-start">
                                    <label class="col-sm-3 col-form-label form-label-title" for="logo">Logo</label>
                                    <div class="col-sm-9">
                                        @if ($logoActual)
                                            <div class="mb-2">
                                                <img src="{{ $logoActual }}"
                                                     alt="Logo actual de {{ $marca->Nom_Marca }}"
                                                     class="rounded border bg-white p-2"
                                                     style="max-height: 72px; max-width: 180px; object-fit: contain;">
                                            </div>
                                        @endif
                                        <p class="text-muted small mb-2">PNG, SVG o WebP con fondo transparente. Se muestra en el carrusel del inicio.</p>
                                        <input class="form-control form-choose @error('logo') is-invalid @enderror"
                                               type="file"
                                               id="logo"
                                               name="logo"
                                               accept="image/png,image/jpeg,image/gif,image/webp,image/svg+xml">
                                        @error('logo')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div id="marca-logo-preview" class="mt-3"></div>
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-start">
                                    <label class="col-sm-3 col-form-label form-label-title" for="Descrip_Marca">Descripción</label>
                                    <div class="col-sm-9">
                                        <textarea id="Descrip_Marca"
                                                  name="Descrip_Marca"
                                                  rows="4"
                                                  class="form-control @error('Descrip_Marca') is-invalid @enderror"
                                                  placeholder="Describe la marca…"
                                                  required>{{ $valor('Descrip_Marca') }}</textarea>
                                        @error('Descrip_Marca')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if ($esEdicion && ($marca->productos_count ?? 0) > 0)
                                    <div class="mb-4 row align-items-center">
                                        <label class="form-label-title col-sm-3 mb-0">Productos</label>
                                        <div class="col-sm-9">
                                            <p class="form-control-plaintext mb-0 text-muted">
                                                {{ $marca->productos_count }} producto{{ $marca->productos_count === 1 ? '' : 's' }} asociado{{ $marca->productos_count === 1 ? '' : 's' }}.
                                                No se puede eliminar la marca mientras tenga productos.
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a href="{{ route('admin.marcas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                                    <button type="submit" class="btn btn-solid">{{ $submitLabel }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputLogo = document.getElementById('logo');
            const previewLogo = document.getElementById('marca-logo-preview');
            const inputNombre = document.getElementById('Nom_Marca');
            const inputSlug = document.getElementById('slug_Marca');

            if (inputLogo && previewLogo) {
                inputLogo.addEventListener('change', function () {
                    previewLogo.innerHTML = '';
                    const archivo = inputLogo.files && inputLogo.files[0];
                    if (!archivo) {
                        return;
                    }
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(archivo);
                    img.className = 'rounded border bg-white p-2';
                    img.style.maxHeight = '72px';
                    img.style.maxWidth = '180px';
                    img.style.objectFit = 'contain';
                    previewLogo.appendChild(img);
                });
            }

            function slugDesdeNombre(texto) {
                return texto
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9\s-]/g, '')
                    .trim()
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');
            }

            if (inputNombre && inputSlug) {
                inputNombre.addEventListener('blur', function () {
                    if (inputSlug.value.trim() === '') {
                        inputSlug.value = slugDesdeNombre(inputNombre.value);
                    }
                });
            }
        });
    </script>
@endpush
