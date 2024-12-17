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
                    <form action="{{ route('menu.update', $menu->id_menu) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Kategori Dropdown -->
                        <div class="form-group mb-3">
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
                        <div class="form-group mb-3">
                            <label for="nama_menu">Nama Menu</label>
                            <input type="text" name="nama_menu" id="nama_menu" class="form-control @error('nama_menu') is-invalid @enderror" value="{{ old('nama_menu', $menu->nama_menu) }}">
                            @error('nama_menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Harga Menu -->
                        <div class="form-group mb-3">
                            <label for="harga_menu">Harga Menu</label>
                            <input type="number" name="harga_menu" id="harga_menu" class="form-control @error('harga_menu') is-invalid @enderror" value="{{ old('harga_menu', $menu->harga_menu) }}">
                            @error('harga_menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div class="form-group mb-3">
                            <label for="image">Upload Gambar</label>
                            <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror" style="width: 30%" value="{{ old('image', $menu->image) }}" onchange="previewImage(event)">
                            
                            <!-- Preview Container -->
                            <div id="preview-container" class="mt-2 position-relative">
                                <img id="preview-image" class="img-thumbnail d-none" width="150" onclick="removeImage()">

                                <!-- Show the current image if exists -->
                                @if($menu->image)
                                    <img id="current-image" src="{{ asset($menu->image) }}" alt="Current Image" class="img-thumbnail" width="150" onclick="removeImage()">
                                    <input type="hidden" name="remove_existing_image" value="1" id="remove_existing_image"> <!-- Flag to remove the existing image -->
                                @endif
                            </div>

                            <label class="text-danger mb-0">Click image to remove</label>

                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="active" {{ $menu->status === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $menu->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock Items Selection -->
                        <div class="form-group mb-3">
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
                        <div id="quantity-inputs" class="form-group mb-3">
                            @foreach ($menu->stok as $item)
                                <div class="form-group">
                                    <label>Jumlah Bahan ({{ $item->nama_barang }})</label>
                                    <input type="number" name="jumlah[]" class="form-control" value="{{ old('jumlah')[$loop->index] ?? $item->pivot->jumlah }}" required>
                                </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('menu.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrop" tabindex="-1" aria-labelledby="modalCropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="card card-outline shadow-sm" style="pointer-events: all">
            <div class="card-header">
                <h5 class="card-title" id="modalCropLabel">Crop Image</h5>
            </div>
            <div class="modal-body d-flex justify-content-center align-items-center" style="max-height: 80vh"> <!-- Vertically and horizontally center -->
                <img id="cropper-image" class="img-fluid" alt="Crop this image" style="width: 100%">
            </div>
            <div class="card-footer text-end">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="cancelButton" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="cropButton">Crop</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Synchronize checkboxes and generate quantity inputs
        document.querySelectorAll('.stok-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                triggerQuantityInputs(); // Trigger quantity input generation after a checkbox change
            });
        });

        // Trigger change event to populate select initially based on checked checkboxes
        document.querySelectorAll('.stok-checkbox').forEach(function(checkbox) {
            if (checkbox.checked) {
                checkbox.dispatchEvent(new Event('change'));
            }
        });

        // Function to trigger dynamic quantity input generation
        function triggerQuantityInputs() {
            const quantityContainer = document.getElementById('quantity-inputs');
            const selectedValues = Array.from(document.querySelectorAll('.stok-checkbox:checked')).map(checkbox => checkbox.value);
            const existingQuantities = {};

            // If editing, load existing quantities
            @foreach ($menu->stok as $item)
                existingQuantities[{{ $item->id_barang }}] = "{{ $item->pivot->jumlah }}";
            @endforeach

            // Clear previous quantity inputs
            quantityContainer.innerHTML = '';

            // Generate a quantity input field for each selected stock item
            selectedValues.forEach(function(barangId) {
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
                input.dataset.stokId = barangId; // Store the stokId for reference

                // If there's an existing quantity for this stock item, set it
                if (existingQuantities[barangId]) {
                    input.value = existingQuantities[barangId];
                }

                inputGroup.appendChild(label);
                inputGroup.appendChild(input);
                quantityContainer.appendChild(inputGroup);
            });
        }

        // Initial trigger to populate quantity inputs based on already selected checkboxes
        triggerQuantityInputs();
    });
</script>
@endsection
