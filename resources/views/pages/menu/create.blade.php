@extends('layouts.app')

@section('title', 'Tambah Menu')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card x-ovfl-hid">
                <div class="card-header my-bg text-white">
                    <label class="my-0 fw-bold">@yield('title')</label>
                </div>
                <div class="card-body py-2">
                    <form action="{{ route('menu.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Kategori Dropdown -->
                        <div class="form-group">
                            <label for="id_kategori">Kategori</label>
                            <select name="id_kategori" id="id_kategori" class="form-control @error('id_kategori') is-invalid @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategori as $item)
                                    <option value="{{ $item->id_kategori }}" {{ old('id_kategori') == $item->id_kategori ? 'selected' : '' }}>
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
                            <input type="text" name="nama_menu" id="nama_menu" class="form-control @error('nama_menu') is-invalid @enderror" value="{{ old('nama_menu') }}" placeholder="Nama Menu">
                            @error('nama_menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Harga Menu -->
                        <div class="form-group mt-3">
                            <label for="harga_menu">Harga Menu</label>
                            <input type="number" name="harga_menu" id="harga_menu" class="form-control @error('harga_menu') is-invalid @enderror" value="{{ old('harga_menu') }}" placeholder="Harga Menu">
                            @error('harga_menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="form-group mt-3">
                            <label for="image">Upload Gambar</label>
                            <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror">
                            @error('image')
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
                                                    {{ in_array($data->id_barang, old('stok', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="stok_{{ $data->id_barang }}">
                                                    {{ $data->nama_barang }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Hidden Multiple Select Dropdown to hold selected stok values -->
                            <select name="stok[]" id="stok" class="form-control @error('stok') is-invalid @enderror" multiple style="display: none;">
                                @foreach ($stok as $data)
                                    <option value="{{ $data->id_barang }}" {{ in_array($data->id_barang, old('stok', [])) ? 'selected' : '' }}>
                                        {{ $data->nama_barang }}
                                    </option>
                                @endforeach
                            </select>

                            @error('stok')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dynamically Generated Quantity Inputs -->
                        <div id="quantity-inputs" class="form-group mt-3">
                            <!-- The quantity inputs will be dynamically inserted here -->
                        </div>

                        <div class="form-group mt-3">
                            <button type="submit" class="btn my-btn">
                                <i class="fas fa-save mr-2"></i> Simpan Menu
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
    // Synchronize checkboxes and hidden select input
    document.querySelectorAll('.stok-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            // Find all checked checkboxes
            const selectedValues = Array.from(document.querySelectorAll('.stok-checkbox:checked')).map(checkbox => checkbox.value);
            
            // Update the hidden select input with the selected values
            const selectElement = document.getElementById('stok');
            selectElement.innerHTML = ''; // Clear the current options
            selectedValues.forEach(function(value) {
                const option = document.createElement('option');
                option.value = value;
                option.selected = true;

                // Find the label text for this checkbox
                const label = checkbox.closest('.form-check').querySelector('label').textContent.trim();
                option.textContent = label;

                selectElement.appendChild(option);
            });

            // Trigger quantity input generation after updating the hidden select
            triggerQuantityInputs();
        });
    });

    // Trigger change event to populate select initially based on checked checkboxes
    document.querySelectorAll('.stok-checkbox').forEach(function(checkbox) {
        if (checkbox.checked) {
            checkbox.dispatchEvent(new Event('change'));
        }
    });

    // Function to trigger the dynamic quantity input generation
    function triggerQuantityInputs() {
        const selectElement = document.getElementById('stok');
        const selectedStok = Array.from(selectElement.selectedOptions).map(option => option.value);
        const quantityContainer = document.getElementById('quantity-inputs');

        // Clear previous quantity inputs
        quantityContainer.innerHTML = '';

        // Generate a quantity input field for each selected stock item
        selectedStok.forEach(function(barangId) {
            const checkbox = document.querySelector(`.stok-checkbox[value="${barangId}"]`);
            const itemName = checkbox.closest('.form-check').querySelector('label').textContent.trim(); // Get the item name

            const inputGroup = document.createElement('div');
            inputGroup.classList.add('form-group');

            // Create the label for quantity
            const label = document.createElement('label');
            label.textContent = `Jumlah Bahan (${itemName})`;

            // Create the input for quantity
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

    // Listen for changes on the stock selection (hidden select) on initial load and further changes
    document.getElementById('stok').addEventListener('change', triggerQuantityInputs);
</script>

@endsection