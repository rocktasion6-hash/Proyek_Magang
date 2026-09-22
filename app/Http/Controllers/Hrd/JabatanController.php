<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = Jabatan::withCount([
            'karyawans',
            'skills',
            'assessments',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'nama_jabatan',
                    'like',
                    '%' . $search . '%'
                )->orWhere(
                    'deskripsi',
                    'like',
                    '%' . $search . '%'
                );
            });
        }

        if ($request->filled('level_jabatan')) {
            $query->where(
                'level_jabatan',
                $request->level_jabatan
            );
        }

        $jabatans = $query
            ->orderBy('level_jabatan')
            ->orderBy('nama_jabatan')
            ->paginate(10)
            ->withQueryString();

        $levels = Jabatan::select('level_jabatan')
            ->distinct()
            ->orderBy('level_jabatan')
            ->pluck('level_jabatan');

        return view(
            'hrd.jabatan.index',
            compact(
                'jabatans',
                'levels'
            )
        );
    }

    public function create()
    {
        return view('hrd.jabatan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jabatan' => [
                'required',
                'string',
                'max:255',
                'unique:jabatans,nama_jabatan',
            ],

            'level_jabatan' => [
                'required',
                'integer',
                'min:1',
            ],

            'standar_nilai' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],
        ]);

        Jabatan::create($validated);

        return redirect()
            ->route('hrd.jabatan.index')
            ->with(
                'success',
                'Jabatan berhasil ditambahkan.'
            );
    }

    public function show(Jabatan $jabatan)
    {
        $jabatan->load([
            'karyawans.departemen',
            'skills',
            'assessments',
            'soals',
            'jabatanSkills.skill',
        ]);

        $skillIds = $jabatan->skills
            ->pluck('id');

        $availableSkills = Skill::whereNotIn(
            'id',
            $skillIds
        )
            ->orderBy('nama_skill')
            ->get();

        return view(
            'hrd.jabatan.show',
            compact(
                'jabatan',
                'availableSkills'
            )
        );
    }

    public function edit(Jabatan $jabatan)
    {
        return view(
            'hrd.jabatan.edit',
            compact('jabatan')
        );
    }

    public function update(
        Request $request,
        Jabatan $jabatan
    ) {
        $validated = $request->validate([
            'nama_jabatan' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'jabatans',
                    'nama_jabatan'
                )->ignore($jabatan->id),
            ],

            'level_jabatan' => [
                'required',
                'integer',
                'min:1',
            ],

            'standar_nilai' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],
        ]);

        $jabatan->update($validated);

        return redirect()
            ->route(
                'hrd.jabatan.show',
                $jabatan
            )
            ->with(
                'success',
                'Data jabatan berhasil diperbarui.'
            );
    }

    public function destroy(Jabatan $jabatan)
    {
        /*
        |--------------------------------------------------------------------------
        | Jangan hapus jabatan yang masih digunakan.
        |--------------------------------------------------------------------------
        */

        if ($jabatan->karyawans()->exists()) {
            return back()->with(
                'error',
                'Jabatan tidak dapat dihapus karena masih digunakan oleh karyawan.'
            );
        }

        if ($jabatan->assessments()->exists()) {
            return back()->with(
                'error',
                'Jabatan tidak dapat dihapus karena sudah digunakan dalam assessment.'
            );
        }

        if ($jabatan->soals()->exists()) {
            return back()->with(
                'error',
                'Jabatan tidak dapat dihapus karena sudah digunakan pada bank soal.'
            );
        }

        $jabatan->delete();

        return redirect()
            ->route('hrd.jabatan.index')
            ->with(
                'success',
                'Jabatan berhasil dihapus.'
            );
    }
}