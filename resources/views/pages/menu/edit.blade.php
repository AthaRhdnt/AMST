@extends('layouts.app')

@section('title', 'Edit Menu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card x-ovfl-hid">
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">@yield('title')</label>
                </div>
                <div class="card-body py-2">
                    <form action="{{ route('menu.update', $menu->id_menu) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Kategori Dropdown -->
                        <div class="form-group">
                            <label for="id_kategori">Kategori</label>
                            <select name="id_kategori" id="id_kategori" class="form-control @error('id_kategori') is-invalid @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategori as $item)
                                    <option value="{{ $item->id_kategori }}" {{ $menu->id_kategori == $item->id_kategori ? 'selected' : '' }}>
                                        {{ $item->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_kategori')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nama Menu -->
                        <div class="form-group mt-3">
                            <label for="nama_menu">Nama Menu</label>
                            <input type="text" name="nama_menu" id="nama_menu" class="form-control @error('nama_menu') is-invalid @enderror" value="{{ old('nama_menu', $menu->nama_menu) }}">
                            @error('nama_menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Harga Menu -->
                        <div class="form-group mt-3">
                            <label for="harga_menu">Harga Menu</label>
                            <input type="number" name="harga_menu" id="harga_menu" class="form-control @error('harga_menu') is-invalid @enderror" value="{{ old('harga_menu', $menu->harga_menu) }}">
                            @error('harga_menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock Items Selection -->
                        <div class="form-group mt-3">
                            <label for="stok">Pilih Bahan Stok</label>
                            <div class="card scrollable-card" style="max-height: 300px; overflow-y: auto;">
                                <div class="row">
                                    @foreach ($stok as $data)
                                        <div class="col-6 col-md-4 my-2">
                                            <div class="form-check mx-3">
                                                <input class="form-check-input stok-checkbox" type="checkbox" name="stok[]" value="{{ $data->id_barang }}" id="stok_{{ $data->id_barang }}"
                                                    {{ in_array($data->id_barang, old('stok', $menu->stok->pluck('id_barang')->toArray())) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="stok_{{ $data->id_barang }}">
                                                    {{ $data->nama_barang }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('stok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dynamically Generated Quantity Inputs -->
                        <div id="quantity-inputs" class="form-group mt-3">
                            @foreach ($menu->stok as $item)
                                <div class="form-group">
                                    <label>Jumlah Bahan ({{ $item->nama_barang }})</label>
                                    <input type="number" name="jumlah[]" class="form-control" value="{{ old('jumlah')[$loop->index] ?? $item->pivot->jumlah }}" required>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn my-btn">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                            <a href="{{ route('menu.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.stok-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            triggerQuantityInputs();
        });
    });

    function triggerQuantityInputs() {
        const selectedStok = Array.from(document.querySelectorAll('.stok-checkbox:checked')).map(checkbox => checkbox.value);
        const quantityContainer = document.getElementById('quantity-inputs');
        quantityContainer.innerHTML = '';

        selectedStok.forEach(function(stokId) {
            const checkbox = document.querySelector(`.stok-checkbox[value="${stokId}"]`);
            const itemName = checkbox.closest('.form-check').querySelector('label').textContent.trim();

            const inputGroup = document.createElement('div');
            inputGroup.classList.add('form-group');

            const label = document.createElement('label');
            label.textContent = `Jumlah Bahan (${itemName})`;

            const input = document.createElement('input');
            input.type = 'number';
            input.name = 'jumlah[]';
            input.classList.add('form-control');
            input.placeholder = 'Jumlah bahan';
            input.required = true;

            inputGroup.appendChild(label);
            inputGroup.appendChild(input);
            quantityContainer.appendChild(inputGroup);
        });
    }
</script>
@endsection
