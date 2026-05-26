// ===============================
// 📦 CARRITO LOCALSTORAGE + API (usuario logueado)
// ===============================

function isCartServerEnabled() {
    return window.CART_CONFIG
        && window.CART_CONFIG.isAuthenticated === true;
}

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function cartItemUrl(idProducto) {
    return window.CART_CONFIG.routes.itemBase + '/' + idProducto;
}

async function cartApiRequest(url, method, body) {
    const options = {
        method: method,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    };

    if (body !== undefined) {
        options.body = JSON.stringify(body);
    }

    const response = await fetch(url, options);
    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(data.message || 'Error al actualizar el carrito');
    }

    return data;
}

/** Solo una vez por sesión: fusionar carrito de invitado (localStorage) con la BD. */
const CART_MERGE_SESSION_KEY = 'tiendaonline_cart_guest_merged';
const WISHLIST_MERGE_SESSION_KEY = 'tiendaonline_wishlist_guest_merged';

/**
 * Al cerrar sesión: evita que al volver a entrar se vuelva a sumar
 * el mismo carrito que quedó copiado en localStorage.
 */
function clearClientShopStorage() {
    localStorage.removeItem('carrito');
    localStorage.removeItem('wishlist');
    sessionStorage.removeItem(CART_MERGE_SESSION_KEY);
    sessionStorage.removeItem(WISHLIST_MERGE_SESSION_KEY);
}

async function loadCartFromServer() {
    const data = await cartApiRequest(
        window.CART_CONFIG.routes.items,
        'GET'
    );

    saveCart(data.items || []);
}

async function mergeGuestCartWithServer() {
    const data = await cartApiRequest(
        window.CART_CONFIG.routes.sync,
        'POST',
        { items: getCart() }
    );

    saveCart(data.items || []);
}

/**
 * Logueado: la BD manda. Solo suma cantidades si el invitado tenía ítems
 * y en BD aún no hay carrito (primer login tras comprar como invitado).
 */
async function syncCartWithServer() {
    const alreadyMerged =
        sessionStorage.getItem(CART_MERGE_SESSION_KEY) === '1';

    if (alreadyMerged) {
        await loadCartFromServer();
        return;
    }

    const localItems = getCart();

    if (localItems.length > 0) {
        const serverData = await cartApiRequest(
            window.CART_CONFIG.routes.items,
            'GET'
        );
        const serverItems = serverData.items || [];

        if (serverItems.length === 0) {
            await mergeGuestCartWithServer();
        } else {
            saveCart(serverItems);
        }
    } else {
        await loadCartFromServer();
    }

    sessionStorage.setItem(CART_MERGE_SESSION_KEY, '1');
}

function applyServerCartAndRefresh(items) {
    saveCart(items || []);
    updateCartCounter();
    renderCart();
    renderMiniCart();
}

function getCart() {

    try {

        const raw = localStorage.getItem('carrito');

        return raw ? JSON.parse(raw) : [];

    } catch (error) {

        console.warn('Carrito inválido en localStorage, se reinicia.', error);

        localStorage.removeItem('carrito');

        return [];
    }
}

function saveCart(cart) {

    localStorage.setItem('carrito', JSON.stringify(cart));
}

// ===============================
// ➕ AGREGAR AL CARRITO
// ===============================

async function addToCart(id, imagen, url, precio, nombre) {

    id = parseInt(id, 10);

    if (Number.isNaN(id)) {

        console.warn('addToCart: id inválido');

        return;
    }

    const input = document.getElementById('qty-' + id);

    const cantidadSeleccionada =
        input ? parseInt(input.value, 10) || 1 : 1;

    if (isCartServerEnabled()) {

        try {

            const data = await cartApiRequest(
                window.CART_CONFIG.routes.store,
                'POST',
                {
                    id_producto: id,
                    cantidad: cantidadSeleccionada,
                    precio: parseFloat(precio),
                }
            );

            applyServerCartAndRefresh(data.items);

        } catch (error) {

            console.error(error);

            showToast('No se pudo guardar el producto en el carrito');

            return;
        }

    } else {

        let cart = getCart();

        let existing = cart.find(
            (item) => parseInt(item.id, 10) === id
        );

        if (existing) {

            existing.cantidad += cantidadSeleccionada;

        } else {

            cart.push({
                id: id,
                nombre: nombre,
                precio: parseFloat(precio),
                imagen: imagen,
                url: url,
                cantidad: cantidadSeleccionada
            });
        }

        saveCart(cart);

        updateCartCounter();

        renderCart();

        renderMiniCart();
    }

    removeFromWishlist(id);

    showToast("Producto agregado al carrito 🛒");
}

// ===============================
// ➖ ACTUALIZAR CANTIDAD UI
// ===============================

function updateQuantity(id, action, event) {

    if (event) {

        event.preventDefault();

        event.stopPropagation();
    }

    const input = document.getElementById('qty-' + id);

    if (!input) return;

    let value = parseInt(input.value, 10);

    if (Number.isNaN(value) || value < 1) {

        value = 1;
    }

    if (action === 'plus') {

        value += 1;

    } else if (action === 'minus' && value > 1) {

        value -= 1;
    }

    input.value = value;
}

// ===============================
// 🗑️ ELIMINAR DEL CARRITO
// ===============================

async function removeFromCart(id) {

    id = parseInt(id, 10);

    if (Number.isNaN(id)) return;

    if (isCartServerEnabled()) {

        try {

            const data = await cartApiRequest(
                cartItemUrl(id),
                'DELETE'
            );

            applyServerCartAndRefresh(data.items);

        } catch (error) {

            console.error(error);

            showToast('No se pudo eliminar el producto');

            return;
        }

    } else {

        let cart = getCart();

        cart = cart.filter(
            (item) => parseInt(item.id, 10) !== id
        );

        saveCart(cart);

        renderCart();

        renderMiniCart();

        updateCartCounter();
    }

    showToast("Producto eliminado 🗑️");
}

// ===============================
// 🔄 ACTUALIZAR CANTIDAD EN CARRITO
// ===============================

async function changeQty(id, action) {

    id = parseInt(id, 10);

    if (Number.isNaN(id)) return;

    let cart = getCart();

    const item = cart.find(
        (row) => parseInt(row.id, 10) === id
    );

    if (!item) return;

    let nuevaCantidad = item.cantidad;

    if (action === 'plus') {

        nuevaCantidad++;

    } else if (action === 'minus' && item.cantidad > 1) {

        nuevaCantidad--;
    }

    if (isCartServerEnabled()) {

        try {

            const data = await cartApiRequest(
                cartItemUrl(id),
                'PATCH',
                { cantidad: nuevaCantidad }
            );

            applyServerCartAndRefresh(data.items);

        } catch (error) {

            console.error(error);

            showToast('No se pudo actualizar la cantidad');

            return;
        }

    } else {

        cart = cart.map((row) => {

            if (parseInt(row.id, 10) === id) {

                row.cantidad = nuevaCantidad;
            }

            return row;
        });

        saveCart(cart);

        renderCart();

        renderMiniCart();

        updateCartCounter();
    }
}

// ===============================
// 💰 TOTAL
// ===============================

function getTotal() {

    let cart = getCart();

    return cart.reduce((total, item) => {

        return total + (item.precio * item.cantidad);

    }, 0);
}

/** Envío según subtotal (misma regla que updateCartSummary). */
function getShippingForSubtotal(subtotal) {

    return subtotal < 300 ? 35 : 0;
}

// ===============================
// 🛒 CONTADOR CARRITO
// ===============================

function updateCartCounter() {

    let cart = getCart();

    let totalItems = cart.reduce(
        (sum, item) => sum + item.cantidad,
        0
    );

    document.querySelectorAll('.cart-count').forEach((el) => {

        el.innerText = totalItems;
    });

    let carritoCount2 =
        document.getElementById('carrito-count2');

    if (carritoCount2) {

        carritoCount2.innerText =
            totalItems + ' Prod';
    }

    let totalCart2 =
        document.getElementById('total-cart2');

    if (totalCart2) {

        // Barra flotante (app.blade): mismo total que mini carrito / resumen (subtotal + envío).
        if (cart.length === 0) {

            totalCart2.innerText = 'Q0.00';

        } else {

            let subtotal = getTotal();

            let shipping = getShippingForSubtotal(subtotal);

            totalCart2.innerText =
                'Q' + (subtotal + shipping).toFixed(2);
        }
    }
}

// ===============================
// ❤️ CONTADOR FAVORITOS
// ===============================

function updateWishlistCounter() {

    let list = getWishlist();

    let count = list.length;

    document.querySelectorAll('.wishlist-count').forEach((el) => {

        el.innerText = count;
    });
}

// ===============================
// 🧾 RENDER CARRITO COMPLETO
// ===============================

function renderCart() {

    let cart = getCart();

    let container =
        document.getElementById('cart-items');

    if (!container) return;

    let emptyCart =
        document.getElementById('cart-empty');

    let cartContent =
        document.getElementById('cart-content');

    container.innerHTML = '';

    if (cart.length === 0) {

        if (emptyCart) {

            emptyCart.style.display = 'block';
        }

        if (cartContent) {

            cartContent.style.display = 'none';
        }

        updateCartSummary();

        return;
    }

    if (emptyCart) {

        emptyCart.style.display = 'none';
    }

    if (cartContent) {

        cartContent.style.display = 'flex';
    }

    cart.forEach(item => {

        let subtotal =
            item.precio * item.cantidad;

        container.innerHTML += `

        <tr class="product-box-contain">

            <td class="product-detail">

                <h4 class="table-title text-content">
                    Producto
                </h4>

                <div class="product border-0">

                    <a href="${item.url}" class="product-image">

                        <img src="${item.imagen}"
                             class="img-fluid blur-up lazyload"
                             style="width:100px"
                             alt="${item.nombre}">

                    </a>

                    <div class="product-detail">

                        <ul>

                            <li class="name">

                                <a href="${item.url}">
                                    ${item.nombre}
                                </a>

                            </li>

                        </ul>

                    </div>

                </div>

            </td>

            <td class="price">

                <h4 class="table-title text-content">
                    Precio
                </h4>

                <h5>
                    Q${parseFloat(item.precio).toFixed(2)}
                </h5>

            </td>

            <td class="quantity">

                <h4 class="table-title text-content text-right">
                    Cantidad
                </h4>

                <div class="quantity-price">

                    <div class="cart_qty">

                        <div class="input-group">

                            <button type="button"
                                    class="btn qty-left-minus"
                                    onclick="changeQty(${item.id}, 'minus')">

                                <i class="fa fa-minus ms-0"></i>

                            </button>

                            <input class="form-control input-number qty-input"
                                   type="text"
                                   value="${item.cantidad}"
                                   readonly>

                            <button type="button"
                                    class="btn qty-right-plus"
                                    onclick="changeQty(${item.id}, 'plus')">

                                <i class="fa fa-plus ms-0"></i>

                            </button>

                        </div>

                    </div>

                </div>

            </td>

            <td class="subtotal">

                <h4 class="table-title text-content">
                    Total
                </h4>

                <h5>
                    Q${subtotal.toFixed(2)}
                </h5>

            </td>

            <td class="save-remove">

                <h4 class="table-title text-content">
                    Acción
                </h4>

                <a class="remove close_button"
                   href="javascript:void(0)"
                   onclick="removeFromCart(${item.id})">

                    Eliminar

                </a>

            </td>

        </tr>
        `;
    });

    updateCartSummary();
}

// ===============================
// 💵 RESUMEN DEL CARRITO
// ===============================

function updateCartSummary() {

    let subtotal = getTotal();

    let shipping = getShippingForSubtotal(subtotal);

    let total = subtotal + shipping;

    let subtotalElement =
        document.getElementById('subtotal');

    let shippingElement =
        document.getElementById('shipping');

    let totalElement =
        document.getElementById('total');

    if (subtotalElement) {

        subtotalElement.innerText =
            'Q' + subtotal.toFixed(2);
    }

    if (shippingElement) {

        shippingElement.innerText =
            'Q' + shipping.toFixed(2);
    }

    if (totalElement) {

        totalElement.innerText =
            'Q' + total.toFixed(2);
    }
}

// ===============================
// 🛒 MINI CART HEADER
// ===============================

function renderMiniCart() {

    let cart = getCart();

    let container =
        document.getElementById('mini-cart-items');

    let totalElement =
        document.getElementById('mini-cart-total');

    if (!container) return;

    container.innerHTML = '';

    if (cart.length === 0) {

        container.innerHTML = `

            <li class="text-center py-3">

                <h6 class="text-content">
                    Tu carrito está vacío
                </h6>

            </li>
        `;

        if (totalElement) {

            totalElement.innerText = 'Q0.00';
        }

        return;
    }

    cart.forEach(item => {

        container.innerHTML += `

        <li>

            <div class="drop-cart">

                <a href="${item.url}" class="drop-image">

                    <img src="${item.imagen}"
                         class="blur-up lazyload"
                         alt="${item.nombre}">

                </a>

                <div class="drop-contain">

                    <a href="${item.url}">
                        <h5>${item.nombre}</h5>
                    </a>

                    <h6>
                        <span>${item.cantidad} x</span>
                        Q${parseFloat(item.precio).toFixed(2)}
                    </h6>

                    <button class="close-button"
                            onclick="removeFromCart(${item.id})">

                        <i class="fa-solid fa-trash-can"></i>

                    </button>

                </div>

            </div>

        </li>
        `;
    });

    if (totalElement) {

        // Mini carrito: el total incluye envío con la misma regla que updateCartSummary()
        // (getShippingForSubtotal: Q35 si subtotal < 300, si no Q0).
        let subtotal = getTotal();

        let shipping = getShippingForSubtotal(subtotal);

        let totalConEnvio = subtotal + shipping;

        totalElement.innerText =
            'Q' + totalConEnvio.toFixed(2);
    }
}

// ===============================
// ❤️ WISHLIST + API (usuario logueado)
// ===============================

function isWishlistServerEnabled() {
    return window.WISHLIST_CONFIG
        && window.WISHLIST_CONFIG.isAuthenticated === true;
}

function wishlistItemUrl(idProducto) {
    return window.WISHLIST_CONFIG.routes.itemBase + '/' + idProducto;
}

async function loadWishlistFromServer() {
    const data = await cartApiRequest(
        window.WISHLIST_CONFIG.routes.items,
        'GET'
    );

    saveWishlist(data.items || []);
}

async function mergeGuestWishlistWithServer() {
    const data = await cartApiRequest(
        window.WISHLIST_CONFIG.routes.sync,
        'POST',
        { items: getWishlist() }
    );

    saveWishlist(data.items || []);
}

async function syncWishlistWithServer() {
    const alreadyMerged =
        sessionStorage.getItem(WISHLIST_MERGE_SESSION_KEY) === '1';

    if (alreadyMerged) {
        await loadWishlistFromServer();
        return;
    }

    const localItems = getWishlist();

    if (localItems.length > 0) {
        const serverData = await cartApiRequest(
            window.WISHLIST_CONFIG.routes.items,
            'GET'
        );
        const serverItems = serverData.items || [];

        if (serverItems.length === 0) {
            await mergeGuestWishlistWithServer();
        } else {
            saveWishlist(serverItems);
        }
    } else {
        await loadWishlistFromServer();
    }

    sessionStorage.setItem(WISHLIST_MERGE_SESSION_KEY, '1');
}

function applyServerWishlistAndRefresh(items) {
    saveWishlist(items || []);
    updateWishlistCounter();

    if (typeof window.refreshWishlistPage === 'function') {
        window.refreshWishlistPage();
    }
}

function getWishlist() {

    try {

        const raw =
            localStorage.getItem('wishlist');

        return raw ? JSON.parse(raw) : [];

    } catch (error) {

        console.warn('Wishlist inválida en localStorage, se reinicia.', error);

        localStorage.removeItem('wishlist');

        return [];
    }
}

function saveWishlist(list) {

    localStorage.setItem(
        'wishlist',
        JSON.stringify(list)
    );
}

async function removeFromWishlist(id) {

    const pid = parseInt(id, 10);

    if (Number.isNaN(pid)) return;

    if (isWishlistServerEnabled()) {

        try {

            const data = await cartApiRequest(
                wishlistItemUrl(pid),
                'DELETE'
            );

            applyServerWishlistAndRefresh(data.items);

        } catch (error) {

            console.error(error);

            showToast('No se pudo quitar de favoritos');

            return;
        }

    } else {

        const list = getWishlist().filter(
            (item) => parseInt(item.id, 10) !== pid
        );

        saveWishlist(list);

        updateWishlistCounter();

        if (typeof window.refreshWishlistPage === 'function') {

            window.refreshWishlistPage();
        }
    }
}

async function addToWishlist(id, imagen, url, precio, nombre) {

    id = parseInt(id, 10);

    if (Number.isNaN(id)) return;

    let list = getWishlist();

    let exists = list.find(
        (item) => parseInt(item.id, 10) === id
    );

    if (exists) {

        showToast("Ya está en favoritos ❤️");

        return;
    }

    if (isWishlistServerEnabled()) {

        try {

            const data = await cartApiRequest(
                window.WISHLIST_CONFIG.routes.store,
                'POST',
                { id_producto: id }
            );

            applyServerWishlistAndRefresh(data.items);

        } catch (error) {

            console.error(error);

            showToast('No se pudo agregar a favoritos');

            return;
        }

    } else {

        list.push({
            id: id,
            nombre: nombre,
            precio: precio,
            imagen: imagen,
            url: url
        });

        saveWishlist(list);

        updateWishlistCounter();
    }

    showToast("Agregado a favoritos ❤️");
}

// ===============================
// 👁️ QUICK VIEW MODAL
// ===============================

function openQuickView(id, nombre, precio, imagen) {

    let modal = document.getElementById('view');

    if (!modal) return;

    modal.querySelector('.title-name').innerText = nombre;

    modal.querySelector('.price').innerText = 'Q' + precio;

    modal.querySelector('.slider-image img').src = imagen;
}

// ===============================
// 🔔 TOAST SIMPLE
// ===============================

function showToast(message) {

    alert(message);
}

// ===============================
// 🚀 INIT
// ===============================

document.addEventListener('DOMContentLoaded', async function () {

    if (!isCartServerEnabled()) {
        sessionStorage.removeItem(CART_MERGE_SESSION_KEY);
        sessionStorage.removeItem(WISHLIST_MERGE_SESSION_KEY);
    }

    if (isCartServerEnabled()) {

        try {

            await syncCartWithServer();

        } catch (error) {

            console.warn('Sincronización de carrito fallida:', error);
        }
    }

    if (isWishlistServerEnabled()) {

        try {

            await syncWishlistWithServer();

            if (typeof window.refreshWishlistPage === 'function') {

                window.refreshWishlistPage();
            }

        } catch (error) {

            console.warn('Sincronización de lista de deseos fallida:', error);
        }
    }

    updateCartCounter();

    updateWishlistCounter();

    renderCart();

    renderMiniCart();
});

// ===============================
// 🌍 FUNCIONES GLOBALES
// ===============================

window.updateQuantity = updateQuantity;
window.addToCart = addToCart;
window.addToWishlist = addToWishlist;
window.removeFromWishlist = removeFromWishlist;
window.updateWishlistCounter = updateWishlistCounter;
window.getWishlist = getWishlist;
window.removeFromCart = removeFromCart;
window.changeQty = changeQty;
window.renderCart = renderCart;
window.renderMiniCart = renderMiniCart;
window.clearClientShopStorage = clearClientShopStorage;
