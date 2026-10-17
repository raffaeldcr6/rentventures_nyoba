<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RentalController extends Controller
{
    // Alur status yang diperbolehkan. Status 'selesai' & 'dibatalkan' adalah status akhir.
    private const TRANSITIONS = [
        'pending' => ['disetujui', 'dibatalkan'],
        'disetujui' => ['selesai', 'dibatalkan'],
    ];

    // ---------- READ ----------
    // Admin: melihat semua transaksi. Penyewa: hanya transaksi miliknya.
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Rental::with(['user', 'equipment'])->latest();

            if (! $user->isAdmin()) {
                $query->where('user_id', $user->id);
            }

            if ($request->filled('status') && in_array($request->status, Rental::STATUS, true)) {
                $query->where('status', $request->status);
            }

            $rentals = $query->paginate(10)->withQueryString();
        } catch (\Throwable $e) {
            report($e);
            $rentals = new LengthAwarePaginator([], 0, 10);
            session()->now('error', 'Data transaksi gagal dimuat. Silakan coba lagi.');
        }

        return view('rentals.index', [
            'rentals' => $rentals,
            'statusList' => Rental::STATUS,
        ]);
    }

    // ---------- CREATE (Penyewa) ----------
    public function create(Request $request)
    {
        try {
            $equipments = Equipment::where('stok', '>', 0)->orderBy('nama')->get();
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('equipments.index')
                ->with('error', 'Form sewa gagal dimuat. Silakan coba lagi.');
        }

        return view('rentals.create', [
            'equipments' => $equipments,
            'selected' => $request->query('equipment_id'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|integer|exists:equipments,id',
            'jumlah' => 'required|integer|min:1',
            'tgl_sewa' => 'required|date|after_or_equal:today',
            'tgl_kembali' => 'required|date|after:tgl_sewa',
        ], [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa angka bulat.',
            'date' => ':attribute bukan tanggal yang valid.',
            'equipment_id.exists' => 'Alat yang dipilih tidak ditemukan.',
            'jumlah.min' => 'Jumlah sewa minimal 1 unit.',
            'tgl_sewa.after_or_equal' => 'Tanggal sewa tidak boleh sebelum hari ini.',
            'tgl_kembali.after' => 'Tanggal kembali harus setelah tanggal sewa (minimal sewa 1 hari).',
        ], [
            'equipment_id' => 'Alat',
            'jumlah' => 'Jumlah',
            'tgl_sewa' => 'Tanggal sewa',
            'tgl_kembali' => 'Tanggal kembali',
        ]);

        try {
            // Transaksi database: stok & data sewa harus berubah bersamaan atau batal semua
            DB::transaction(function () use ($validated) {
                $equipment = Equipment::lockForUpdate()->findOrFail($validated['equipment_id']);

                if ($equipment->stok < $validated['jumlah']) {
                    throw new \DomainException(
                        "Stok \"{$equipment->nama}\" tidak mencukupi. Tersisa {$equipment->stok} unit."
                    );
                }

                $hari = max(1, (int) Carbon::parse($validated['tgl_sewa'])
                    ->diffInDays(Carbon::parse($validated['tgl_kembali'])));

                Rental::create([
                    'user_id' => Auth::id(),
                    'equipment_id' => $equipment->id,
                    'jumlah' => $validated['jumlah'],
                    'tgl_sewa' => $validated['tgl_sewa'],
                    'tgl_kembali' => $validated['tgl_kembali'],
                    'total_harga' => $equipment->harga_sewa_per_hari * $validated['jumlah'] * $hari,
                    'status' => 'pending',
                ]);

                $equipment->decrement('stok', $validated['jumlah']);
            });

            return redirect()->route('rentals.index')
                ->with('success', 'Pengajuan sewa berhasil dibuat. Menunggu persetujuan admin.');
        } catch (ModelNotFoundException $e) {
            return back()->withInput()->with('error', 'Alat yang dipilih tidak ditemukan.');
        } catch (\DomainException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()
                ->with('error', 'Transaksi sewa gagal diproses. Silakan coba lagi.');
        }
    }

    // ---------- UPDATE STATUS ----------
    // Admin : pending -> disetujui / dibatalkan, disetujui -> selesai / dibatalkan
    // Penyewa: hanya boleh membatalkan transaksi miliknya yang masih 'pending'
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(Rental::STATUS)],
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);

        $newStatus = $request->input('status');
        $user = Auth::user();

        try {
            DB::transaction(function () use ($id, $newStatus, $user) {
                $query = Rental::lockForUpdate();

                if (! $user->isAdmin()) {
                    $query->where('user_id', $user->id); // penyewa hanya bisa akses miliknya
                }

                $rental = $query->findOrFail($id);

                if (! $user->isAdmin()) {
                    if ($newStatus !== 'dibatalkan') {
                        throw new \DomainException('Penyewa hanya dapat membatalkan transaksi.');
                    }
                    if ($rental->status !== 'pending') {
                        throw new \DomainException('Transaksi hanya dapat dibatalkan saat berstatus pending.');
                    }
                }

                $boleh = self::TRANSITIONS[$rental->status] ?? [];
                if (! in_array($newStatus, $boleh, true)) {
                    throw new \DomainException(
                        "Status tidak dapat diubah dari \"{$rental->status}\" menjadi \"{$newStatus}\"."
                    );
                }

                // Barang kembali / transaksi batal -> stok dikembalikan
                if (in_array($newStatus, ['selesai', 'dibatalkan'], true)) {
                    Equipment::whereKey($rental->equipment_id)->increment('stok', $rental->jumlah);
                }

                $rental->update(['status' => $newStatus]);
            });

            return back()->with('success', "Status transaksi berhasil diubah menjadi \"{$newStatus}\".");
        } catch (ModelNotFoundException $e) {
            return redirect()->route('rentals.index')
                ->with('error', 'Transaksi tidak ditemukan.');
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'Status transaksi gagal diperbarui. Silakan coba lagi.');
        }
    }

    // ---------- DELETE (Penyewa) ----------
    // Hanya transaksi milik sendiri yang berstatus pending / dibatalkan.
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $rental = Rental::where('user_id', Auth::id())->lockForUpdate()->findOrFail($id);

                if (! in_array($rental->status, ['pending', 'dibatalkan'], true)) {
                    throw new \DomainException('Transaksi yang sudah disetujui atau selesai tidak dapat dihapus.');
                }

                // Menghapus transaksi pending = membatalkan, jadi stok dikembalikan
                if ($rental->status === 'pending') {
                    Equipment::whereKey($rental->equipment_id)->increment('stok', $rental->jumlah);
                }

                $rental->delete();
            });

            return redirect()->route('rentals.index')
                ->with('success', 'Transaksi sewa berhasil dihapus.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('rentals.index')
                ->with('error', 'Transaksi tidak ditemukan atau sudah dihapus.');
        } catch (\DomainException $e) {
            return redirect()->route('rentals.index')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('rentals.index')
                ->with('error', 'Transaksi gagal dihapus. Silakan coba lagi.');
        }
    }
}
