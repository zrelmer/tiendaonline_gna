@php
    $esEdicion = isset($categoria) && $categoria !== null;
    $valor = function (string $campo, mixed $default = '') use ($esEdicion, $categoria) {
        $anterior = old($campo);
        if ($anterior !== null) {
            return $anterior;
        }
        if ($esEdicion) {
            return $categoria->{$campo} ?? $default;
        }

        return $default;
    };
    $imagenActual = $esEdicion && $categoria->Cate_Imagen
        ? asset($categoria->Cate_Imagen)
        : null;
    $iconoPreview = \App\Support\CategoriaIcon::remixClass(
        (string) $valor('Cate_Slug'),
        (string) $valor('Cate_Nombre')
    );
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
                                    <h5>Información de la categoría</h5>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0" for="Cate_Nombre">Nombre</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('Cate_Nombre') is-invalid @enderror"
                                               type="text"
                                               id="Cate_Nombre"
                                               name="Cate_Nombre"
                                               value="{{ $valor('Cate_Nombre') }}"
                                               placeholder="Ej. Laptops"
                                               maxlength="200"
                                               required>
                                        @error('Cate_Nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="col-sm-3 col-form-label form-label-title" for="Cate_Slug">URL (slug)</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('Cate_Slug') is-invalid @enderror"
                                               type="text"
                                               id="Cate_Slug"
                                               name="Cate_Slug"
                                               value="{{ $valor('Cate_Slug') }}"
                                               placeholder="ej. laptops"
                                               maxlength="200"
                                               required>
                                        <small class="text-muted">Solo minúsculas, números y guiones. Se usa en filtros de la tienda.</small>
                                        @error('Cate_Slug')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-start">
                                    <label class="col-sm-3 col-form-label form-label-title" for="Cate_Descripcion">Descripción</label>
                                    <div class="col-sm-9">
                                        <textarea id="Cate_Descripcion"
                                                  name="Cate_Descripcion"
                                                  rows="4"
                                                  class="form-control @error('Cate_Descripcion') is-invalid @enderror"
                                                  placeholder="Describe la categoría…"
                                                  required>{{ $valor('Cate_Descripcion') }}</textarea>
                                        @error('Cate_Descripcion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-start">
                                    <label class="col-sm-3 col-form-label form-label-title" for="imagen">Imagen</label>
                                    <div class="col-sm-9">
                                        @if ($imagenActual)
                                            <p class="text-muted small mb-2">Imagen actual:</p>
                                            <div class="mb-3">
                                                <img src="{{ $imagenActual }}"
                                                     alt="{{ $valor('Cate_Nombre') }}"
                                                     class="rounded border"
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            </div>
                                        @endif

                                        <div class="custom-dropzone border rounded p-4 text-center mb-2">
                                            <i class="ri-upload-cloud-2-line fs-3 text-muted"></i>
                                            <p class="text-muted small mb-2">Selecciona una imagen para la categoría</p>
                                            <input class="form-control form-choose @error('imagen') is-invalid @enderror"
                                                   type="file"
                                                   id="imagen"
                                                   name="imagen"
                                                   accept="image/jpeg,image/png,image/webp,image/gif">
                                        </div>
                                        <small class="text-muted d-block">Opcional al crear. JPG, PNG, WEBP o GIF. Máx. 5 MB.</small>
                                        @error('imagen')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                        <div id="categoria-imagen-preview" class="mt-3"></div>
                                    </div>
                                </div>

                                <div class="row align-items-center">
                                    <div class="col-sm-3 form-label-title">Icono en panel</div>
                                    <div class="col-sm-9">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle border bg-light"
                                                  style="width: 56px; height: 56px;">
                                                <i id="categoria-icono-preview"
                                                   class="{{ $iconoPreview }} fs-4"
                                                   style="color: #0da487;"
                                                   aria-hidden="true"></i>
                                            </span>
                                            <small class="text-muted mb-0">
                                                Vista previa del icono Remix según slug y nombre.
                                                En el dashboard se calcula automáticamente (no se guarda en la base de datos).
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <a href="{{ route('admin.categorias.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
            const inputImagen = document.getElementById('imagen');
            const previewImagen = document.getElementById('categoria-imagen-preview');
            const inputNombre = document.getElementById('Cate_Nombre');
            const inputSlug = document.getElementById('Cate_Slug');
            const iconoPreview = document.getElementById('categoria-icono-preview');

            if (inputImagen && previewImagen) {
                inputImagen.addEventListener('change', function () {
                    previewImagen.innerHTML = '';
                    const archivo = inputImagen.files && inputImagen.files[0];
                    if (!archivo) {
                        return;
                    }
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(archivo);
                    img.className = 'rounded border';
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    previewImagen.appendChild(img);
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

            if (iconoPreview && inputNombre && inputSlug) {
                const iconos = {
                    laptop: 'ri-macbook-line',
                    portatil: 'ri-macbook-line',
                    celular: 'ri-smartphone-line',
                    telefono: 'ri-smartphone-line',
                    accesorio: 'ri-plug-line',
                    tablet: 'ri-tablet-line',
                    monitor: 'ri-computer-line',
                    gaming: 'ri-gamepad-line',
                    audio: 'ri-speaker-line',
                };

                function actualizarIconoPreview() {
                    const texto = (inputSlug.value + ' ' + inputNombre.value).toLowerCase();
                    let clase = 'ri-price-tag-3-line';
                    for (const [clave, icono] of Object.entries(iconos)) {
                        if (texto.includes(clave)) {
                            clase = icono;
                            break;
                        }
                    }
                    iconoPreview.className = clase + ' fs-4';
                    iconoPreview.style.color = '#0da487';
                }

                inputNombre.addEventListener('input', actualizarIconoPreview);
                inputSlug.addEventListener('input', actualizarIconoPreview);
            }
        });
    </script>
@endpush
