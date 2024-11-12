@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Menu Items Section -->
        <div class="col-md-8">
            <div class="card x-ovfl-hid">
                <div class="card-header my-bg text-white text-center">
                    <span class="my-0 fw-bold">Menu Items</span>
                </div>
                <div class="card-body py-2">
                    <form action="{{ route('transaksi.create') }}" method="GET">
                        <input type="search" id="search" name="search"
                            class="form-control w-100"
                            placeholder="Search" value="{{ session('transaksi_search', '') }}" />
                    </form>
                </div>
                <div class="separator"></div>
                <div class="card-body scrollable-card">
                    <div class="row" id="menu-items">
                        @foreach ($menuItems as $item)
                        <div class="col-md-4 col-sm-6 mb-4 menu-item" data-name="{{ strtolower($item->nama_menu) }}">
                            <div class="card h-100 d-flex flex-column menu-item-card">
                                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 100px; background-color: #f8f9fa;">
                                    <img src="{{ asset($item->image) }}" class="logo"/>
                                </div>
                                <div class="card-body d-flex flex-column" style="flex: 1;">
                                    <div class="row mb-2" style="min-height: 3.5em;">
                                        <div class="col d-flex align-items-center">
                                            <h5 class="card-title mb-0">{{ $item->nama_menu }}</h5>
                                        </div>
                                    </div>
                                    <div class="row mb-2" style="min-height: 1.5em;">
                                        <div class="col d-flex align-items-center">
                                            <span class="card-text badge badge-dark mb-0">Rp {{ number_format($item->harga_menu, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="row mt-auto">
                                        <div class="col d-flex align-items-center">
                                            <button class="btn my-btn btn-sm add-to-cart w-100" data-id="{{ $item->id_menu }}" data-name="{{ $item->nama_menu }}" data-price="{{ $item->harga_menu }}">
                                                Tambah
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $menuItems->withQueryString()->links('vendor.pagination.bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
        <!-- Cart Section -->
        <div class="col-md-4">
            <div class="card card-outline">
                <div class="card-header my-bg text-white text-center">
                    <strong class="my-0 fw-bold">Keranjang</strong>
                </div>
                <div class="card-body cart-card py-2">
                    <div class="row scrollable-card" style="max-height: 80% !important">
                        <div id="cart-items"></div>
                    </div>
                    <div id="cart-separator" class="separator" style="display: none;"></div>
                    <div class="row" style="max-height: 20% !important">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <span>Sub Total</span>
                                <span>Rp <span id="subtotal">0</span></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <strong>Total</strong>
                                <strong>Rp <span id="total">0</span></strong>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-success btn-block">Bayar Rp <span id="pay-total">0</span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Load cart data from local storage on page load
    let cart = [];

    window.addEventListener('load', function() {
        const storedCart = localStorage.getItem('cart');
        if (storedCart) {
            cart = JSON.parse(storedCart);
            updateCart();
        }
    });

    // Cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));

            // Add to cart logic
            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ id, name, price, quantity: 1 });
            }

            updateCart();
        });
    });

    function updateCart() {
        const cartItemsContainer = document.getElementById('cart-items');
        const cartSeparator = document.getElementById('cart-separator');
        cartItemsContainer.innerHTML = '';

        let subtotal = 0;

        cart.forEach(item => {
            const itemSubtotal = item.price * item.quantity; // Calculate subtotal for each item
            subtotal += itemSubtotal; // Add to overall subtotal

            cartItemsContainer.innerHTML += `
                <div class="cart-item">
                    <div class="item-details">
                        <strong>${item.name}</strong><br>
                        <small class="text-muted">Rp ${item.price.toLocaleString()}</small><br>
                        <small>Jumlah: <span class="text-success">${item.quantity}</span></small>
                    </div>
                    <div class="quantity-controls">
                        <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('${item.id}', -1)">-</button>
                        <input type="text" value="${item.quantity}" class="form-control text-center mx-2" style="width: 40px;" readonly>
                        <button class="btn btn-sm btn-outline-secondary" onclick="changeQuantity('${item.id}', 1)">+</button>
                    </div>
                    <div class="item-subtotal">
                        <span class="text-success">Rp ${itemSubtotal.toLocaleString()}</span>
                    </div>
                </div>
            `;
        });

        cartSeparator.style.display = cart.length > 0 ? 'block' : 'none';

        // Update overall subtotal and total
        document.getElementById('subtotal').innerText = subtotal.toLocaleString();
        const total = subtotal;
        document.getElementById('total').innerText = total.toLocaleString();
        document.getElementById('pay-total').innerText = total.toLocaleString();

        // Save cart to local storage
        localStorage.setItem('cart', JSON.stringify(cart));
    }

    function changeQuantity(id, change) {
        const item = cart.find(item => item.id === id);
        if (item) {
            item.quantity += change;
            if (item.quantity <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
            updateCart();
        }
    }

    // Payment submission logic
    document.querySelector('.btn-success').addEventListener('click', function() {
        const details = cart.map(item => ({
            id_menu: item.id,
            jumlah: item.quantity,
            subtotal: item.price * item.quantity,
        }));

        fetch('{{ route('transaksi.store') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                id_outlet: '{{ $idOutlet }}', // Dynamically use the outlet ID passed from the controller
                kode_transaksi: 'TRX-' + new Date().getTime(), // Generate a unique transaction code
                total_transaksi: document.getElementById('total').innerText.replace('.', '').replace(',', ''),
                details,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Transaction successful!');
                localStorage.removeItem('cart'); // Clear the cart from local storage
                location.reload(); // Reload to clear the cart and refresh
            } else {
                alert('Transaction failed: ' + data.message);
            }
        });
    });
</script>
@endsection
