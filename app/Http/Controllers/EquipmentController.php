<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EquipmentController extends Controller
{
    // ---------- READ (Admin & Penyewa) ----------
    public function index(Request $request)
    {
        try {
            $query = Equipment::query();

            if ($request->filled('q')) {
                $query->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($request->q) . '%']);
            }

            if ($request->filled('kategori')) {
                $query->where('kategori', $request->kategori);
            }

            $equipments = $query->orderBy('nama')->paginate(9)->withQueryString();
        } catch (\Throwable $e) {
            report($e);
            $equipments = new LengthAwarePaginator([], 0, 9);
            session()->now('error', 'Katalog alat gagal dimuat. Silakan coba lagi.');
        }

        return view('equipments.index', [
            'equipments' => $equipments,
            'kategoriList' => Equipment::KATEGORI,
        ]);
    }

    // ---------- CREATE (Admin) ----------
    public function create()
    {
        return view('equipments.create', ['kategoriList' => Equipment::KATEGORI]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());

        try {
            if ($request->hasFile('foto')) {
                $validated['foto'] = $request->file('foto')->store('equipments', 'public');
            } else {
                unset($validated['foto']);
            }

            $equipment = Equipment::create($validated);

            return redirect()->route('equipments.index')
                ->with('success', "Alat \"{$equipment->nama}\" berhasil ditambahkan.");
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()
                ->with('error', 'Gagal menyimpan data alat. Silakan coba lagi.');
        }
    }

    // ---------- UPDATE (Admin) ----------
    public function edit($id)
    {
        try {
            $equipment = Equipment::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('equipments.index')
                ->with('error', 'Data alat tidak ditemukan.');
        }

        return view('equipments.edit', [
            'equipment' => $equipment,
            'kategoriList' => Equipment::KATEGORI,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate($this->rules(), $this->messages(), $this->attributes());

        try {
            $equipment = Equipment::findOrFail($id);

            if ($request->hasFile('foto')) {
                $fotoBaru = $request->file('foto')->store('equipments', 'public');

                if ($equipment->foto) {
                    Storage::disk('public')->delete($equipment->foto); // hapus foto lama
                }
                $validated['foto'] = $fotoBaru;
            } else {
                unset($validated['foto']); // tidak upload baru = pertahankan foto lama
            }

            $equipment->update($validated);

            return redirect()->route('equipments.index')
                ->with('success', "Alat \"{$equipment->nama}\" berhasil diperbarui.");
        } catch (ModelNotFoundException $e) {
            return redirect()->route('equipments.index')
                ->with('error', 'Data alat tidak ditemukan.');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()
                ->with('error', 'Gagal memperbarui data alat. Silakan coba lagi.');
        }
    }

    // ---------- DELETE (Admin) ----------
    public function destroy($id)
    {
        try {
            $equipment = Equipment::findOrFail($id);

            // Alat yang sudah pernah disewa tidak boleh dihapus (menjaga riwayat transaksi)
            if ($equipment->rentals()->exists()) {
                return redirect()->route('equipments.index')
                    ->with('error', "Alat \"{$equipment->nama}\" tidak dapat dihapus karena sudah memiliki riwayat transaksi sewa.");
            }

            $nama = $equipment->nama;

            if ($equipment->foto) {
                Storage::disk('public')->delete($equipment->foto);
            }
            $equipment->delete();

            return redirect()->route('equipments.index')
                ->with('success', "Alat \"{$nama}\" berhasil dihapus.");
        } catch (ModelNotFoundException $e) {
            return redirect()->route('equipments.index')
                ->with('error', 'Data alat tidak ditemukan atau sudah dihapus.');
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('equipments.index')
                ->with('error', 'Gagal menghapus data alat. Silakan coba lagi.');
        }
    }

    // ---------- Aturan validasi (dipakai store & update) ----------
    private function rules(): array
    {
        return [
            'nama' => 'required|string|max:150',
            'kategori' => ['required', Rule::in(Equipment::KATEGORI)],
            'harga_sewa_per_hari' => 'required|integer|min:1000',
            'stok' => 'required|integer|min:0',
            'deskripsi' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }

    private function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'integer' => ':attribute harus berupa angka bulat.',
            'in' => ':attribute yang dipilih tidak valid.',
            'image' => ':attribute harus berupa file gambar.',
            'mimes' => ':attribute harus berformat jpg, jpeg, png, atau webp.',
            'uploaded' => ':attribute gagal diunggah (maksimal 2 MB).',
            'nama.max' => 'Nama alat maksimal 150 karakter.',
            'harga_sewa_per_hari.min' => 'Harga sewa minimal Rp 1.000.',
            'stok.min' => 'Stok tidak boleh bernilai negatif.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }

    private function attributes(): array
    {
        return [
            'nama' => 'Nama alat',
            'kategori' => 'Kategori',
            'harga_sewa_per_hari' => 'Harga sewa per hari',
            'stok' => 'Stok',
            'deskripsi' => 'Deskripsi',
            'foto' => 'Foto',
        ];
    }
}
