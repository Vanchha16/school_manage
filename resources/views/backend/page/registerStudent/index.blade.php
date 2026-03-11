<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Registration</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f6f7fb;
        }

        .card {
            border-radius: 16px;
        }

        .btn {
            border-radius: 12px;
        }

        .form-control,
        .form-select {
            height: 48px;
            border-radius: 12px;
        }

        textarea.form-control {
            height: auto;
            min-height: 110px;
        }

        .item-preview-card {
            display: none;
            border: 1px solid #eef0f4;
            border-radius: 14px;
            background: #fff;
            padding: 14px;
        }

        .item-preview-image {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            flex-shrink: 0;
        }

        @media (max-width: 576px) {
            body {
                background: #fff;
            }

            .container.py-4 {
                padding-top: 16px !important;
                padding-bottom: 16px !important;
                padding-left: 12px;
                padding-right: 12px;
            }

            .card {
                border-radius: 14px;
            }

            .card-body {
                padding: 16px !important;
            }

            h3.mb-1 {
                font-size: 1.8rem;
            }

            .text-center.mb-3 {
                margin-bottom: 16px !important;
            }

            .form-control,
            .form-select {
                height: 44px;
                font-size: 16px;
            }

            textarea.form-control {
                min-height: 96px;
            }

            .btn {
                width: 100%;
                padding-top: 10px;
                padding-bottom: 10px;
            }

            .item-preview-card {
                padding: 12px;
            }

            .item-preview-card .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 12px !important;
            }

            .item-preview-image {
                width: 100%;
                max-width: 160px;
                height: auto;
                aspect-ratio: 1 / 1;
            }

            small.text-muted,
            #student_help {
                display: block;
                margin-top: 6px;
                font-size: 13px;
            }

            hr {
                margin: 20px 0;
            }

            h5 {
                font-size: 1.35rem;
                margin-bottom: 14px;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4" style="max-width: 700px;">
        <div class="text-center mb-3">
            <h3 class="mb-1">Student Registration</h3>
            <div class="text-muted">Fill in your info</div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('submissions.store') }}" id="registerForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Student Name *</label>
                        <input
                            type="text"
                            id="student_name"
                            name="student_name"
                            class="form-control @error('student_name') is-invalid @enderror"
                            list="students_list"
                            value="{{ old('student_name') }}"
                            autocomplete="off"
                            required>
                        <datalist id="students_list">
                            @foreach ($students as $student)
                                <option value="{{ $student->student_name }}"></option>
                            @endforeach
                        </datalist>
                        <small id="student_help" class="text-muted"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gender *</label>
                        <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                            <option value="">-- Select Gender --</option>
                            <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number *</label>
                        <input
                            type="text"
                            id="phone_number"
                            name="phone_number"
                            class="form-control @error('phone_number') is-invalid @enderror"
                            value="{{ old('phone_number') }}"
                            required>
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Group *</label>
                        <input
                            type="text"
                            id="group_search"
                            name="group_search"
                            class="form-control @error('group_id') is-invalid @enderror"
                            list="groups_list"
                            value="{{ old('group_search') }}"
                            placeholder="Type group name"
                            autocomplete="off"
                            required>
                        <datalist id="groups_list">
                            @foreach ($groups as $g)
                                <option value="{{ $g->group_name }}" data-id="{{ $g->group_id }}"></option>
                            @endforeach
                        </datalist>
                        <input type="hidden" name="group_id" id="group_id" value="{{ old('group_id') }}">
                        @error('group_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Start typing and choose group from suggestion.</small>
                    </div>

                    <hr>

                    <h5>Borrow Item</h5>

                    <div class="mb-3">
                        <label class="form-label">Item *</label>
                        <select name="item_id" id="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                            <option value="">-- Select Item --</option>
                            @foreach ($items as $item)
                                <option
                                    value="{{ $item->Itemid }}"
                                    data-image="{{ $item->image ? asset('storage/' . $item->image) : '' }}"
                                    data-name="{{ $item->name }}"
                                    {{ old('item_id') == $item->Itemid ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="itemPreview" class="item-preview-card mb-3">
                        <div class="d-flex align-items-center gap-3">
                            <img id="previewImage" src="" alt="Item Image" class="item-preview-image">

                            <div>
                                <div class="fw-bold" id="previewName"></div>
                                <div class="text-muted small">Selected item preview</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Qty *</label>
                        <input
                            type="number"
                            name="qty"
                            class="form-control @error('qty') is-invalid @enderror"
                            min="1"
                            value="{{ old('qty', 1) }}"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <input
                            type="text"
                            name="status"
                            class="form-control"
                            value="{{ old('status', 'BORROWED') }}"
                            readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-dark" id="submitBtn">Submit</button>
                </form>
            </div>
        </div>

        <div class="text-center text-muted mt-3" style="font-size: 13px;">
            Don School • Admin Panel
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemSelect = document.getElementById('item_id');
            const itemPreview = document.getElementById('itemPreview');
            const previewImage = document.getElementById('previewImage');
            const previewName = document.getElementById('previewName');

            const studentNameInput = document.getElementById('student_name');
            const genderSelect = document.getElementById('gender');
            const phoneInput = document.getElementById('phone_number');
            const groupSearchInput = document.getElementById('group_search');
            const groupIdInput = document.getElementById('group_id');
            const groupsList = document.getElementById('groups_list');
            const studentHelp = document.getElementById('student_help');

            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');

            function updateItemPreview() {
                const selectedOption = itemSelect.options[itemSelect.selectedIndex];

                if (!selectedOption || !selectedOption.value) {
                    previewImage.src = '';
                    previewName.textContent = '';
                    itemPreview.style.display = 'none';
                    return;
                }

                const image = selectedOption.getAttribute('data-image');
                const name = selectedOption.getAttribute('data-name');

                if (image) {
                    previewImage.src = image;
                    previewName.textContent = name || '';
                    itemPreview.style.display = 'block';
                } else {
                    previewImage.src = '';
                    previewName.textContent = name || '';
                    itemPreview.style.display = 'none';
                }
            }

            function syncGroupId() {
                const value = groupSearchInput.value.trim();
                groupIdInput.value = '';

                const options = groupsList.querySelectorAll('option');
                for (const opt of options) {
                    if (opt.value === value) {
                        groupIdInput.value = opt.dataset.id;
                        break;
                    }
                }
            }

            function clearAutoFilledFields() {
                genderSelect.value = '';
                phoneInput.value = '';
                groupSearchInput.value = '';
                groupIdInput.value = '';
            }

            let studentTimer = null;

            studentNameInput.addEventListener('input', function() {
                clearTimeout(studentTimer);

                const studentName = this.value.trim();
                studentHelp.textContent = '';

                if (studentName.length < 2) {
                    clearAutoFilledFields();
                    return;
                }

                studentTimer = setTimeout(() => {
                    fetch(`{{ route('register.checkStudentName') }}?student_name=${encodeURIComponent(studentName)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                genderSelect.value = data.gender || '';
                                phoneInput.value = data.phone_number || '';
                                groupSearchInput.value = data.group_name || '';
                                syncGroupId();

                                studentHelp.textContent = 'Student found in database.';
                                studentHelp.className = 'text-success';
                            } else {
                                clearAutoFilledFields();
                                studentHelp.textContent = '';
                            }
                        })
                        .catch(error => {
                            clearAutoFilledFields();
                            studentHelp.textContent = '';
                            console.error('Student name check error:', error);
                        });
                }, 300);
            });

            groupSearchInput.addEventListener('input', syncGroupId);
            groupSearchInput.addEventListener('blur', function() {
                syncGroupId();

                if (!groupIdInput.value) {
                    groupSearchInput.setCustomValidity('Please choose a group from the suggestion list.');
                } else {
                    groupSearchInput.setCustomValidity('');
                }
            });

            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Submitting...';
            });

            itemSelect.addEventListener('change', updateItemPreview);
            updateItemPreview();
            syncGroupId();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>