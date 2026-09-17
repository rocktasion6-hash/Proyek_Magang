<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\Request;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        $query = Departemen::withCount('karyawans');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'nama_departemen',
                    'like',
                    '%' . $search . '%'
                )->orWhere(
                    'deskripsi',
                    'like',
                    '%' . $search . '%'
                );
            });
        }

        $departemens = $query
            ->orderBy('nama_departemen')
            ->paginate(10)
            ->withQueryString();

        return view(
            'hrd.departemen.index',
            compact('departemens')
        );
    }

    public function create()
    {
        return view('hrd.departemen.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_departemen' => [
                'required',
                'string',
                'max:255',
                'unique:departemens,nama_departemen',
            ],
            'deskripsi' => [
                'nullable',
                'string',
            ],
        ]);

        Departemen::create($validated);

        return redirect()
            ->route('hrd.departemen.index')
            ->with(
                'success',
                'Departemen berhasil ditambahkan.'
            );
    }

    public function show(Departemen $departemen)
    {
        $departemen->load([
            'karyawans.jabatan',
        ]);

        return view(
            'hrd.departemen.show',
            compact('departemen')
        );
    }

    public function edit(Departemen $departemen)
    {
        return view(
            'hrd.departemen.edit',
            compact('departemen')
        );
    }

    public function update(
        Request $request,
        Departemen $departemen
    ) {
        $validated = $request->validate([
            'nama_departemen' => [
                'required',
                'string',
                'max:255',
                'unique:departemens,nama_departemen,' .
                    $departemen->id,
            ],
            'deskripsi' => [
                'nullable',
                'string',
            ],
        ]);

        $departemen->update($validated);

        return redirect()
            ->route(
                'hrd.departemen.show',
                $departemen
            )
            ->with(
                'success',
                'Departemen berhasil diperbarui.'
            );
    }

    public function destroy(Departemen $departemen)
    {
        /*
        |--------------------------------------------------------------------------
        | Jangan hapus permanen jika masih memiliki karyawan.
        |--------------------------------------------------------------------------
        */

        if ($departemen->karyawans()->exists()) {
            return back()->with(
                'error',
                'Departemen tidak dapat dinonaktifkan karena masih memiliki karyawan.'
            );
        }

        $departemen->delete();

        return redirect()
            ->route('hrd.departemen.index')
            ->with(
                'success',
                'Departemen berhasil dihapus.'
            );
    }
}