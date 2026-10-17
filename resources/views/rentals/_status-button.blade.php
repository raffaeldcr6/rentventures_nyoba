{{--
    Tombol kecil untuk mengubah status transaksi (PUT /rentals/{id}).
    Variabel: $rental, $status, $label, $class, $confirm (opsional)
--}}
<form action="{{ route('rentals.update', $rental->id) }}" method="POST" class="d-inline"
      @isset($confirm) onsubmit="return confirm('{{ $confirm }}')" @endisset>
    @csrf
    @method('PUT')
    <input type="hidden" name="status" value="{{ $status }}">
    <button type="submit" class="btn btn-sm {{ $class }}">{{ $label }}</button>
</form>
