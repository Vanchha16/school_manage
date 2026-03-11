@extends('backend.layout.master')

@section('title', 'Items')
@section('item_active', 'active')

@section('contents')
    <div class="container-fluid py-4">

        {{-- Alerts --}}
        @if (session('success'))
            <div class="alert alert-success rounded-4">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger rounded-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <h2 class="fw-bold mb-1">Items</h2>
                <div class="text-secondary">Add, view, edit, and delete items.</div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Search --}}
                <form method="GET" action="{{ url()->current() }}" class="d-flex">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0"
                            placeholder="Search by name, status..." style="min-width: 320px;">
                    </div>
                </form>

                {{-- Add --}}
                <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Item
                </button>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">Total Items</div>
                        <div class="fs-3 fw-bold">{{ $statTotal ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">Active</div>
                        <div class="fs-3 fw-bold">{{ $statActive ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body">
                        <div class="text-secondary small">Inactive</div>
                        <div class="fs-3 fw-bold">{{ $statInactive ?? 0 }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-secondary small">
                                <th style="width:60px;">#</th>
                                <th style="width:90px;">Image</th>
                                <th>Name</th>
                                <th class="text-end">Borrow</th>
                                <th class="text-end">Available</th>
                                <th class="text-center">Qty</th>
                                <th>Status</th>
                                <th class="text-end" style="width:180px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($items as $key => $it)
                                <tr>
                                    <td class="text-secondary">{{ $key + 1 }}</td>

                                    <td>
                                        <img class="rounded-3 border" style="width:44px;height:44px;object-fit:cover;"
                                            src="{{ $it->image ? asset('storage/' . $it->image) : asset('assets/img/no-image.png') }}"
                                            alt="thumb">
                                    </td>

                                    <td>
                                        <div class="fw-semibold">{{ $it->name }}</div>
                                    </td>

                                    <td class="fw-medium text-end">{{ $it->borrowed_qty ?? 0 }}</td>

                                    <td class="text-end fw-semibold">@php $available = max(0, ($it->qty ?? 0) - ($it->borrowed_qty ?? 0)); @endphp
                                        {{ $available }}</td>

                                    <td class="text-center">{{ $it->qty }}</td>

                                    <td>
                                        @if ($it->status == 1)
                                            <span
                                                class="badge rounded-pill bg-success-subtle text-success px-3 py-2">Active</span>
                                        @else
                                            <span
                                                class="badge rounded-pill bg-secondary-subtle text-secondary px-3 py-2">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="text-end">
                                        <a href="{{ route('items.show', ['itemid' => $it->Itemid]) }}"
                                            class="btn btn-light btn-sm me-1" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        {{-- Edit --}}
                                        <button class="btn btn-light btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#editItemModal{{ $it->Itemid }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        {{-- Delete --}}
                                        <form action="{{ route('items.destroy', ['itemid' => $it->Itemid]) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Delete this item?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-danger py-4">No Data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $items->links() }}
                </div>

            </div>
        </div>

    </div>

    {{-- Add Item Modal --}}
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title fw-semibold">Add Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-4">

                        <div class="col-12 col-md-5">
                            <label class="form-label fw-semibold">Item Image</label>

                            <div class="border border-2 border-secondary-subtle rounded-4 p-3">
                                <div class="d-flex gap-3 align-items-start">
                                    <div class="border rounded-3 d-flex align-items-center justify-content-center"
                                        style="width:54px;height:54px;">
                                        <i class="bi bi-image fs-4 text-secondary"></i>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">Upload image</div>
                                        <div class="text-secondary small">JPG/PNG/WebP. Max 1MB.</div>

                                        <input class="form-control mt-3" type="file" name="image" accept="image/*">
                                        <div class="form-text">Saved to <code>storage/app/public/items</code>.</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-3">
                                    <button type="reset" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-x-circle me-1"></i>Remove
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-7">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Item Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g., School Bag"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Description</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="Write detail about this item...">{{ old('description') }}</textarea>
                                <div class="form-text">Example: USB flash drive 32GB used for lab computers.</div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Quantity <span
                                            class="text-danger">*</span></label>
                                    <input type="number" name="qty" class="form-control" min="0"
                                        step="1" value="0" required>
                                </div>

                                <div class="col-12 col-md-4">
                                    <label class="form-label fw-semibold">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" class="form-select rounded-3 py-2" required>
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-secondary small">
                                Note: <code>available</code> and <code>borrow</code> will be saved as <b>0</b>
                                automatically.
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">
                        <i class="bi bi-check2-circle me-1"></i> Save
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- ✅ Edit Modals (OUTSIDE TABLE, rendered once) --}}
    @foreach ($items as $it)
        <div class="modal fade" id="editItemModal{{ $it->Itemid }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content" method="POST"
                    action="{{ route('items.update', ['itemid' => $it->Itemid]) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title fw-semibold">Edit Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-4">

                            <div class="col-12 col-md-5">
                                <label class="form-label fw-semibold">Item Image</label>

                                <div class="border border-2 border-secondary-subtle rounded-4 p-3">
                                    <div class="mb-3">
                                        <img class="rounded-3 border" style="width:80px;height:80px;object-fit:cover;"
                                            src="{{ $it->image ? asset('storage/' . $it->image) : asset('assets/img/no-image.png') }}">
                                    </div>

                                    <input class="form-control" type="file" name="image" accept="image/*">
                                    <div class="form-text">Leave empty to keep current image.</div>
                                </div>
                            </div>

                            <div class="col-12 col-md-7">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Item Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ $it->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea name="description" class="form-control" rows="4" placeholder="Write detail about this item...">{{ old('description', $it->description) }}</textarea>
                                    <div class="form-text">Example: USB flash drive 32GB used for lab computers.</div>
                                </div>
                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Quantity <span
                                                class="text-danger">*</span></label>
                                        <input type="number" name="qty" class="form-control" min="0"
                                            step="1" value="{{ $it->qty }}" required>
                                    </div>

                                    <div class="col-12 col-md-4">
                                        <label class="form-label fw-semibold">Status <span
                                                class="text-danger">*</span></label>
                                        <select name="status" class="form-select rounded-3 py-2" required>
                                            <option value="1" {{ $it->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="0" {{ $it->status == 0 ? 'selected' : '' }}>Inactive
                                            </option>
                                        </select>
                                    </div>

                                </div>


                                <div class="text-secondary small">
                                    Note: <code>available</code> and <code>borrow</code> are not changed here.
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-check2-circle me-1"></i> Update
                        </button>
                    </div>

                </form>
            </div>
        </div>
    @endforeach

@endsection
