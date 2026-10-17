@extends('layouts.app')

@section('title', 'Tambah Alat')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-4"><i class="bi bi-plus-circle text-success"></i> Tambah Alat Baru</h4>

                <form action="{{ route('equipments.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    @include('equipments._form', ['equipment' => null])

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                        <a href="{{ route('equipments.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
