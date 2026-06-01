@php
    $esEdicion = isset($producto) && $producto !== null;
    $valor = function (string $campo, mixed $default = '') use ($esEdicion, $producto) {
        $anterior = old($campo);
        if ($anterior !== null) {
            return $anterior;
        }
        if ($esEdicion) {
            return $producto->{$campo} ?? $default;
        }

        return $default;
    };
    $stockReservado = (int) old('Stock_Reservado', $esEdicion ? ($producto->inventario?->Stock_Reservado ?? 0) : 0);
    $stockActual = (int) old('Stock', $esEdicion ? ($producto->inventario?->Stock ?? 0) : 0);
    $imagenesActuales = $esEdicion ? $producto->imagenes->sortBy('orden') : collect();
@endphp

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-sm-8 m-auto">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-header-2">
                                <h5>Información del producto</h5>
                            </div>

                            <form method="POST"
                                  action="{{ $formAction }}"
                                  enctype="multipart/form-data"
                                  class="theme-form theme-form-2 mega-form">
                                @csrf
                                @if (($formMethod ?? 'POST') !== 'POST')
                                    @method($formMethod)
                                @endif

                                <div class="mb-4 row align-items-center">
                                    <label class="form-label-title col-sm-3 mb-0" for="Prod_Nombre">Nombre del producto</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('Prod_Nombre') is-invalid @enderror"
                                               type="text"
                                               id="Prod_Nombre"
                                               name="Prod_Nombre"
                                               value="{{ $valor('Prod_Nombre') }}"
                                               placeholder="Ej. Laptop HP 15"
                                               maxlength="200"
                                               required>
                                        @error('Prod_Nombre')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="col-sm-3 col-form-label form-label-title" for="Prod_Slug">URL (slug)</label>
                                    <div class="col-sm-9">
                                        <input class="form-control @error('Prod_Slug') is-invalid @enderror"
                                               type="text"
                                               id="Prod_Slug"
                                               name="Prod_Slug"
                                               value="{{ $valor('Prod_Slug') }}"
                                               placeholder="ej. laptop-hp-15"
                                               maxlength="200"
                                               required>
                                        <small class="text-muted">Se usa en el enlace del producto en la tienda.</small>
                                        @error('Prod_Slug')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="col-sm-3 col-form-label form-label-title" for="Id_Categoria">Categoría</label>
                                    <div class="col-sm-9">
                                        <select class="js-example-basic-single w-100 @error('Id_Categoria') is-invalid @enderror"
                                                id="Id_Categoria"
                                                name="Id_Categoria"
                                                required>
                                            <option value="" disabled {{ $valor('Id_Categoria') === '' ? 'selected' : '' }}>Seleccione una categoría</option>
                                            @foreach ($categorias as $categoria)
                                                <option value="{{ $categoria->Id_Categoria }}"
                                                    @selected((string) $valor('Id_Categoria') === (string) $categoria->Id_Categoria)>
                                                    {{ $categoria->Cate_Nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('Id_Categoria')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="col-sm-3 col-form-label form-label-title" for="Id_Marca">Marca</label>
                                    <div class="col-sm-9">
                                        <select class="js-example-basic-single w-100 @error('Id_Marca') is-invalid @enderror"
                                                id="Id_Marca"
                                                name="Id_Marca"
                                                required>
                                            <option value="" disabled {{ $valor('Id_Marca') === '' ? 'selected' : '' }}>Seleccione una marca</option>
                                            @foreach ($marcas as $marca)
                                                <option value="{{ $marca->Id_Marca }}"
                                                    @selected((string) $valor('Id_Marca') === (string) $marca->Id_Marca)>
                                                    {{ $marca->Nom_Marca }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('Id_Marca')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-4 row align-items-center">
                                    <label class="col-sm-3 col-form-label form-label-title" for="Id_Estatus">Estatus en catálogo</label>
                                    <div class="col-sm-9">
                                        <select class="js-example-basic-single w-100 @error('Id_Estatus') is-invalid @enderror"
                                                id="Id_Estatus"
                                                name="Id_Estatus"
                                                required>
                                            @foreach ($estatusProducto as $estatus)
                                                <option value="{{ $estatus->Id_Estatus }}"
                                                    @selected((string) $valor('Id_Estatus', $esEdicion ? $producto->Id_Estatus : 1) === (string) $estatus->Id_Estatus)>
                                                    {{ $estatus->Nom_Estatus }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('Id_Estatus')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row align-items-center">
                                    <label class="col-sm-3 col-form-label form-label-title" for="Prod_Activo">Visible en tienda</label>
                                    <div class="col-sm-9">
                                        <label class="switch">
                                            <input type="hidden" name="Prod_Activo" value="0">
                                            <input type="checkbox"
                                                   id="Prod_Activo"
                                                   name="Prod_Activo"
                                                   value="1"
                                                   @checked((string) $valor('Prod_Activo', $esEdicion ? (int) $producto->Prod_Activo : 1) === '1')>
                                            <span class="switch-state"></span>
                                        </label>
                                        <small class="text-muted d-block mt-1">Si está activo, el producto puede mostrarse en la tienda online.</small>
                                        @error('Prod_Activo')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-header-2">
                                <h5>Descripción</h5>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="row">
                                        <label class="form-label-title col-sm-3 mb-0" for="editor">Descripción del producto</label>
                                        <div class="col-sm-9">
                                            <textarea id="editor"
                                                      name="Prod_Descripcion"
                                                      class="form-control @error('Prod_Descripcion') is-invalid @enderror"
                                                      rows="8"
                                                      placeholder="Describe el producto…">{{ $valor('Prod_Descripcion') }}</textarea>
                                            @error('Prod_Descripcion')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-header-2">
                                <h5>Imágenes del producto</h5>
                            </div>

                            <div class="row align-items-start">
                                <label class="col-sm-3 col-form-label form-label-title" for="imagenes">
                                    {{ $esEdicion ? 'Agregar imágenes' : 'Galería' }}
                                </label>
                                <div class="col-sm-9">
                                    @if ($esEdicion && $imagenesActuales->isNotEmpty())
                                        <p class="text-muted small mb-2">Imágenes actuales (orden en galería):</p>
                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            @foreach ($imagenesActuales as $imagen)
                                                <div class="border rounded p-1 text-center" style="width: 88px;">
                                                    <img src="{{ asset($imagen->url) }}"
                                                         class="rounded"
                                                         style="width: 80px; height: 80px; object-fit: cover;"
                                                         alt="Imagen {{ $imagen->orden }}">
                                                    <small class="d-block text-muted mt-1">
                                                        {{ $loop->first ? 'Principal' : 'Orden '.$imagen->orden }}
                                                    </small>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <input class="form-control form-choose @error('imagenes') is-invalid @enderror @error('imagenes.*') is-invalid @enderror"
                                           type="file"
                                           id="imagenes"
                                           name="imagenes[]"
                                           accept="image/jpeg,image/png,image/webp,image/gif"
                                           multiple>
                                    <small class="text-muted d-block mt-2">
                                        @if ($esEdicion)
                                            Las nuevas imágenes se añaden al final de la galería sin eliminar las existentes.
                                        @else
                                            Puedes seleccionar varias imágenes. La primera del orden será la miniatura en listados y tienda.
                                        @endif
                                    </small>
                                    @error('imagenes')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    @error('imagenes.*')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror

                                    <div id="producto-imagenes-preview" class="d-flex flex-wrap gap-2 mt-3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-header-2">
                                <h5>Precios</h5>
                            </div>

                            <div class="mb-4 row align-items-center">
                                <label class="col-sm-3 form-label-title" for="Prod_Precio">Precio de venta (Q)</label>
                                <div class="col-sm-9">
                                    <input class="form-control @error('Prod_Precio') is-invalid @enderror"
                                           type="number"
                                           id="Prod_Precio"
                                           name="Prod_Precio"
                                           value="{{ $valor('Prod_Precio') }}"
                                           placeholder="0.00"
                                           min="0"
                                           step="0.01"
                                           required>
                                    @error('Prod_Precio')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <label class="col-sm-3 form-label-title" for="Prod_PrecioOferta">Precio de oferta (Q)</label>
                                <div class="col-sm-9">
                                    <input class="form-control @error('Prod_PrecioOferta') is-invalid @enderror"
                                           type="number"
                                           id="Prod_PrecioOferta"
                                           name="Prod_PrecioOferta"
                                           value="{{ $valor('Prod_PrecioOferta') }}"
                                           placeholder="0.00"
                                           min="0"
                                           step="0.01">
                                    <small class="text-muted">Opcional. En la tienda se muestra tachado como precio anterior.</small>
                                    @error('Prod_PrecioOferta')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-header-2">
                                <h5>Inventario</h5>
                            </div>

                            <input type="hidden" name="Stock_Reservado" value="{{ $stockReservado }}">

                            <div class="mb-4 row align-items-center">
                                <label class="form-label-title col-sm-3 mb-0" for="Stock">
                                    {{ $esEdicion ? 'Stock actual' : 'Stock inicial' }}
                                </label>
                                <div class="col-sm-9">
                                    <input class="form-control @error('Stock') is-invalid @enderror"
                                           type="number"
                                           id="Stock"
                                           name="Stock"
                                           value="{{ $stockActual }}"
                                           placeholder="0"
                                           min="0"
                                           step="1"
                                           required>
                                    @error('Stock')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-4 row align-items-center">
                                <label class="col-sm-3 col-form-label form-label-title">Stock reservado</label>
                                <div class="col-sm-9">
                                    <input class="form-control"
                                           type="number"
                                           value="{{ $stockReservado }}"
                                           readonly
                                           disabled>
                                    <small class="text-muted d-block mt-1">
                                        Reservado por carritos y pedidos. No se modifica desde este formulario.
                                    </small>
                                </div>
                            </div>

                            <div class="row align-items-center">
                                <label class="col-sm-3 col-form-label form-label-title">Stock disponible</label>
                                <div class="col-sm-9">
                                    <p class="form-control-plaintext mb-0" id="stock-disponible-preview">
                                        {{ max(0, $stockActual - $stockReservado) }}
                                    </p>
                                    <small class="text-muted">Calculado: Stock − Stock reservado.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 justify-content-end">
                                <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
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
    <script src="{{ asset('assets/admin/js/ckeditor.js') }}"></script>
    <script src="{{ asset('assets/admin/js/ckeditor-custom.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stockInput = document.getElementById('Stock');
            const stockDisponiblePreview = document.getElementById('stock-disponible-preview');
            const stockReservado = {{ $stockReservado }};

            if (stockInput && stockDisponiblePreview) {
                stockInput.addEventListener('input', function () {
                    const stock = parseInt(stockInput.value, 10) || 0;
                    stockDisponiblePreview.textContent = String(Math.max(0, stock - stockReservado));
                });
            }

            const input = document.getElementById('imagenes');
            const preview = document.getElementById('producto-imagenes-preview');
            if (!input || !preview) {
                return;
            }

            input.addEventListener('change', function () {
                preview.innerHTML = '';

                Array.from(input.files || []).forEach(function (file, index) {
                    if (!file.type.startsWith('image/')) {
                        return;
                    }

                    const wrap = document.createElement('div');
                    wrap.className = 'border rounded p-1 text-center';
                    wrap.style.width = '88px';

                    const img = document.createElement('img');
                    img.className = 'rounded';
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    img.alt = file.name;
                    img.src = URL.createObjectURL(file);

                    const label = document.createElement('small');
                    label.className = 'd-block text-muted mt-1';
                    label.textContent = 'Nueva ' + (index + 1);

                    wrap.appendChild(img);
                    wrap.appendChild(label);
                    preview.appendChild(wrap);
                });
            });
        });
    </script>
@endpush
