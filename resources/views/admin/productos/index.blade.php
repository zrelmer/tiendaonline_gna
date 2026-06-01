@extends('layouts.appadmin')

@section('title', 'Listado de productos')

@section('content')
    {{-- ============================================================
         ZONA 1: Encabezado de página (título, breadcrumb)
         Pega aquí el bloque de tu plantilla Fastkart
    ============================================================ --}}
    <div class="title-header option-title d-sm-flex d-block">
        <h5>Products List</h5>
        <div class="right-options">
            <ul>
                <li>
                    <a href="javascript:void(0)">import</a>
                </li>
                <li>
                    <a href="javascript:void(0)">Export</a>
                </li>
                <li>
                    <a class="btn btn-solid" href="add-new-product.html">Add Product</a>
                </li>
            </ul>
        </div>
    </div>

    {{-- ============================================================
         ZONA 2: Tarjeta principal del listado
    ============================================================ --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table">
                <div class="card-body">

                    {{-- ZONA 2a: Barra superior (búsqueda + exportar) --}}
                    <div class="table-header row align-items-center g-2 mb-3">
                        <div class="col-md-6">
                            <form action="{{ route('admin.productos.index') }}" method="GET" class="d-flex gap-2" role="search">
                                <input
                                    type="search"
                                    name="q"
                                    value="{{ request('q') }}"
                                    class="form-control"
                                    placeholder="Buscar por nombre, SKU, categoría…"
                                    aria-label="Buscar productos"
                                >
                                <button type="submit" class="btn btn-primary">Buscar</button>
                                @if (request()->filled('q'))
                                    <a href="{{ route('admin.productos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                                @endif
                            </form>
                        </div>
                    </div>

                    {{-- ZONA 2b: Tabla de productos --}}
                    <div class="table-responsive">
                        <table class="table theme-table table-product">
                            <thead>
                                <tr>
                                    <th>Product Image</th>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Current Qty</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Option</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="table-image">
                                            <img src="{{ asset('assets/admin/images/product/5.png') }}" class="img-fluid" alt="">
                                        </div>
                                    </td>

                                    <td>Aata Buscuit</td>

                                    <td>Buscuit</td>

                                    <td>12</td>

                                    <td class="td-price">$95.97</td>

                                    <td class="status-danger">
                                        <span>Pending</span>
                                    </td>

                                    <td>
                                        <ul>
                                            <li>
                                                <a href="order-detail.html">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0)">
                                                    <i class="ri-pencil-line"></i>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModalToggle">
                                                    <i class="ri-delete-bin-line"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="table-image">
                                            <img src="{{ asset('assets/admin/images/product/5.png') }}" class="img-fluid" alt="">
                                        </div>
                                    </td>

                                    <td>Aata Buscuit</td>

                                    <td>Buscuit</td>

                                    <td>12</td>

                                    <td class="td-price">$95.97</td>

                                    <td class="status-danger">
                                        <span>Pending</span>
                                    </td>

                                    <td>
                                        <ul>
                                            <li>
                                                <a href="order-detail.html">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0)">
                                                    <i class="ri-pencil-line"></i>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#exampleModalToggle">
                                                    <i class="ri-delete-bin-line"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- ZONA 2c: Paginación --}}
                    @if (method_exists($productos, 'links'))
                        <div class="d-flex justify-content-end mt-3">
                            {{ $productos->withQueryString()->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
