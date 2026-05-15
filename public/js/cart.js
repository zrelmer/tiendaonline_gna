// ===============================
// 📦 CARRITO LOCALSTORAGE
// ===============================

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

function addToCart(id, imagen, url, precio, nombre) {

    id = parseInt(id, 10);

    if (Number.isNaN(id)) {

        console.warn('addToCart: id inválido');

        return;
    }

    let cart = getCart();

    const input = document.getElementById('qty-' + id);

    const cantidadSeleccionada =
        input ? parseInt(input.value, 10) || 1 : 1;

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

    removeFromWishlist(id);

    showToast("Producto agregado al carrito 🛒");

    updateCartCounter();

    renderCart();

    renderMiniCart();
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

function removeFromCart(id) {

    id = parseInt(id, 10);

    if (Number.isNaN(id)) return;

    let cart = getCart();

    cart = cart.filter(
        (item) => parseInt(item.id, 10) !== id
    );

    saveCart(cart);

    renderCart();

    renderMiniCart();

    updateCartCounter();

    showToast("Producto eliminado 🗑️");
}

// ===============================
// 🔄 ACTUALIZAR CANTIDAD EN CARRITO
// ===============================

function changeQty(id, action) {

    id = parseInt(id, 10);

    if (Number.isNaN(id)) return;

    let cart = getCart();

    cart = cart.map(item => {

        if (parseInt(item.id, 10) === id) {

            if (action === 'plus') {

                item.cantidad++;

            } else if (
                action === 'minus' &&
                item.cantidad > 1
            ) {

                item.cantidad--;
            }
        }

        return item;
    });

    saveCart(cart);

    renderCart();

    renderMiniCart();

    updateCartCounter();
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
// ❤️ WISHLIST
// ===============================

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

function removeFromWishlist(id) {

    const pid = parseInt(id, 10);

    if (Number.isNaN(pid)) return;

    const list = getWishlist().filter(
        (item) => parseInt(item.id, 10) !== pid
    );

    saveWishlist(list);

    updateWishlistCounter();

    if (typeof window.refreshWishlistPage === 'function') {

        window.refreshWishlistPage();
    }
}

function addToWishlist(id, imagen, url, precio, nombre) {

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

    list.push({
        id: id,
        nombre: nombre,
        precio: precio,
        imagen: imagen,
        url: url
    });

    saveWishlist(list);

    updateWishlistCounter();

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

document.addEventListener('DOMContentLoaded', function () {

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
