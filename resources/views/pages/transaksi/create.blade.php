@extends('layouts.app')

@section('title', 'Kasir')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Menu Items Section -->
        <div class="col-md-8">
            <div class="card menu-card x-ovfl-hid">
                <div class="card-header my-bg text-white text-center">
                    <span class="my-0 fw-bold">Menu</span>
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
                                <div class="card-img-top d-flex align-items-center justify-content-center mt-3">
                                    @if($item->image)
                                        <!-- Display the actual image -->
                                        <img 
                                            src="{{ asset($item->image) }}" 
                                            alt="Item Image" 
                                            class="img-thumbnail" 
                                            style="width: 200px; height: 150px; object-fit: cover;">
                                    @else
                                        <!-- Display a placeholder when no image is available -->
                                        <div 
                                            class="img-thumbnail d-flex align-items-center justify-content-center" 
                                            style="width: 200px; height: 150px; background-color: #f8f9fa; border: 1px dashed #dee2e6; color: #6c757d;">
                                            No Image
                                        </div>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column" style="flex: 1;">
                                    <div class="mb-2" style="max-height: 2.5rem;">
                                        <div class="d-flex justify-content-center" style="height: 200px;">
                                            <h5 class="card-title fw-medium mb-0 text-center">{{ $item->nama_menu }}</h5>
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-center">
                                            <span class="card-text text-center badge badge-dark mb-0">Rp {{ number_format($item->harga_menu, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-center">
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
            <div class="card cart-card x-ovfl-hid">
                <div class="card-header my-bg text-white text-center">
                    <strong class="my-0 fw-bold">Keranjang</strong>
                </div>
                <div class="card-body scrollable-card py-2">
                        <div id="cart-items"></div>
                </div>
                <div id="cart-separator" class="separator" style="display: none;"></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Sub Total</span>
                        <span>Rp <span id="subtotal">0</span></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong>Rp <span id="total">0</span></strong>
                    </div>
                    <button class="btn btn-success btn-block mt-2">Bayar Rp <span id="pay-total">0</span></button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="card card-outline shadow-sm" style="pointer-events: all">
            <div class="card-header my-bg text-white">
                <label class="my-0 fw-bold">Preview</label>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="card-body scrollable-card p-1" id="previewContent">
                <!-- Preview content will be injected here -->
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="printBtn">Cetak</button>
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
                id_outlet: '{{ $outletId }}'
                kode_transaksi: 'ORD-' + new Date().getTime(), // Generate a unique transaction code
                total_transaksi: document.getElementById('total').innerText.replace('.', '').replace(',', ''),
                details,
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Transaction successful!');
                localStorage.removeItem('cart'); // Clear the cart from local storage
                openPreviewModal(data.transaction_id);
                $('#previewModal').on('hidden.bs.modal', function () {
                    // Delay the page reload until after the modal is closed
                    setTimeout(function() {
                        location.reload(); // Reload the page after the modal is closed
                    }, 1000); // Adjust the delay if needed
                });
            } else {
                alert('Transaction failed:\n' + data.message);
            }
        });
    });
</script>
@endsection
