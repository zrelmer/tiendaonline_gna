// ===============================
// 📦 CARRITO LOCALSTORAGE
// ===============================

function getCart() {
    // Cambio: evita que un JSON inválido en localStorage rompa el carrito completo.
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
    // Cambio: tomar cantidad seleccionada de forma segura para sumar exactamente ese valor.
    const input = document.getElementById('qty-' + id);
    const cantidadSeleccionada = input ? parseInt(input.value, 10) || 1 : 1;

    let existing = cart.find((item) => parseInt(item.id, 10) === id);

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
}

// ===============================
// ➖ ACTUALIZAR CANTIDAD UI
// ===============================

function updateQuantity(id, action, event) {
    // Cambio: detener propagación para que +/- no dispare otro handler y no salte cantidades.
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const input = document.getElementById('qty-' + id);
    if (!input) return;

    // Cambio: normaliza valor inicial para evitar NaN y mantener mínimo 1.
    let value = parseInt(input.value, 10);
    if (Number.isNaN(value) || value < 1) value = 1;

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
    if (Number.isNaN(id)) {
        return;
    }
    let cart = getCart();
    cart = cart.filter((item) => parseInt(item.id, 10) !== id);
    saveCart(cart);
    renderCart();
    updateCartCounter();
}

// ===============================
// 🔄 ACTUALIZAR CANTIDAD EN CARRITO
// ===============================

function changeQty(id, qty) {
    id = parseInt(id, 10);
    if (Number.isNaN(id)) {
        return;
    }
    let cart = getCart();

    cart = cart.map(item => {
        if (parseInt(item.id, 10) === id) {
            item.cantidad = qty;
        }
        return item;
    });

    saveCart(cart);
    renderCart();
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

// ===============================
// 🛒 CONTADOR CARRITO
// ===============================

function updateCartCounter() {
    let cart = getCart();

    let totalItems = cart.reduce((sum, item) => sum + item.cantidad, 0);

    document.querySelectorAll('.cart-count').forEach((el) => {
        el.innerText = totalItems;
    });
}

function updateWishlistCounter() {
    let list = getWishlist();
    let count = list.length;

    document.querySelectorAll('.wishlist-count').forEach((el) => {
        el.innerText = count;
    });
}

// ===============================
// 🧾 RENDER CARRITO (OPCIONAL)
// ===============================

function renderCart() {
    let cart = getCart();
    let container = document.getElementById('cart-items');

    if (!container) return;

    container.innerHTML = '';

    cart.forEach(item => {

        container.innerHTML += `
            <div class="cart-item d-flex align-items-center mb-3">
                <img src="${item.imagen}" width="60" class="me-2">
                <div>
                    <h6>${item.nombre}</h6>
                    <small>Q${item.precio} x ${item.cantidad}</small>
                </div>
                <button onclick="removeFromCart(${item.id})" class="btn btn-sm btn-danger ms-auto">X</button>
            </div>
        `;
    });

    let totalElement = document.getElementById('cart-total');
    if (totalElement) {
        totalElement.innerText = 'Q' + getTotal().toFixed(2);
    }
}

// ===============================
// ❤️ WISHLIST
// ===============================

function getWishlist() {
    try {
        const raw = localStorage.getItem('wishlist');
        return raw ? JSON.parse(raw) : [];
    } catch (error) {
        console.warn('Wishlist inválida en localStorage, se reinicia.', error);
        localStorage.removeItem('wishlist');
        return [];
    }
}

function saveWishlist(list) {
    localStorage.setItem('wishlist', JSON.stringify(list));
}

function removeFromWishlist(id) {
    const pid = parseInt(id, 10);
    if (Number.isNaN(pid)) {
        return;
    }
    const list = getWishlist().filter((item) => parseInt(item.id, 10) !== pid);
    saveWishlist(list);
    updateWishlistCounter();
    if (typeof window.refreshWishlistPage === 'function') {
        window.refreshWishlistPage();
    }
}

function addToWishlist(id, imagen, url, precio, nombre) {
    id = parseInt(id, 10);
    if (Number.isNaN(id)) {
        return;
    }

    let list = getWishlist();

    let exists = list.find((item) => parseInt(item.id, 10) === id);

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
    alert(message); // puedes cambiarlo por Toastify o SweetAlert
}

// ===============================
// 🚀 INIT
// ===============================

document.addEventListener('DOMContentLoaded', function () {
    updateCartCounter();
    updateWishlistCounter();
    renderCart();
});

// Cambio: exponer funciones globales para uso seguro desde onclick inline.
window.updateQuantity = updateQuantity;
window.addToCart = addToCart;
window.addToWishlist = addToWishlist;
window.removeFromWishlist = removeFromWishlist;
window.updateWishlistCounter = updateWishlistCounter;
window.getWishlist = getWishlist;