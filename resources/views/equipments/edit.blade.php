@extends('layouts.app')

@section('title', 'Edit Alat')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4"><i class="bi bi-pencil-square text-primary"></i> Edit Alat</h4>

                <form action="{{ route('equipments.update', $equipment->id) }}" method="POST"
                      enctype="multipart/form-data" novalidate>
                    @csrf
                    @method('PUT')

                    @include('equipments._form')

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('equipments.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
