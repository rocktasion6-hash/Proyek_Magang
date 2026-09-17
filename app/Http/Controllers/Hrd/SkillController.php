<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    public function index(Request $request)
    {
        $query = Skill::withCount([
            'jabatanSkills',
            'karyawanSkills',
            'soals',
            'assessments',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'nama_skill',
                    'like',
                    '%' . $search . '%'
                )->orWhere(
                    'deskripsi',
                    'like',
                    '%' . $search . '%'
                );
            });
        }

        $skills = $query
            ->orderBy('nama_skill')
            ->paginate(10)
            ->withQueryString();

        return view(
            'hrd.skill.index',
            compact('skills')
        );
    }

    public function create()
    {
        return view('hrd.skill.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_skill' => [
                'required',
                'string',
                'max:255',
                'unique:skills,nama_skill',
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],
        ]);

        Skill::create($validated);

        return redirect()
            ->route('hrd.skill.index')
            ->with(
                'success',
                'Skill berhasil ditambahkan.'
            );
    }

    public function show(Skill $skill)
    {
        $skill->load([
            'jabatans',
            'karyawans.departemen',
            'karyawans.jabatan',
            'soals.jabatan',
            'soals.pilihanJawabans',
            'assessments.jabatan',
        ]);

        return view(
            'hrd.skill.show',
            compact('skill')
        );
    }

    public function edit(Skill $skill)
    {
        return view(
            'hrd.skill.edit',
            compact('skill')
        );
    }

    public function update(
        Request $request,
        Skill $skill
    ) {
        $validated = $request->validate([
            'nama_skill' => [
                'required',
                'string',
                'max:255',
                Rule::unique(
                    'skills',
                    'nama_skill'
                )->ignore($skill->id),
            ],

            'deskripsi' => [
                'nullable',
                'string',
            ],
        ]);

        $skill->update($validated);

        return redirect()
            ->route(
                'hrd.skill.show',
                $skill
            )
            ->with(
                'success',
                'Data skill berhasil diperbarui.'
            );
    }

    public function destroy(Skill $skill)
    {
        /*
        |--------------------------------------------------------------------------
        | Jangan hapus skill yang sudah dipakai data lain.
        |--------------------------------------------------------------------------
        */

        if ($skill->jabatanSkills()->exists()) {
            return back()->with(
                'error',
                'Skill tidak dapat dihapus karena sudah dikaitkan dengan jabatan.'
            );
        }

        if ($skill->karyawanSkills()->exists()) {
            return back()->with(
                'error',
                'Skill tidak dapat dihapus karena sudah dimiliki karyawan.'
            );
        }

        if ($skill->soals()->exists()) {
            return back()->with(
                'error',
                'Skill tidak dapat dihapus karena sudah digunakan pada bank soal.'
            );
        }

        if ($skill->assessments()->exists()) {
            return back()->with(
                'error',
                'Skill tidak dapat dihapus karena sudah digunakan pada assessment.'
            );
        }

        $skill->delete();

        return redirect()
            ->route('hrd.skill.index')
            ->with(
                'success',
                'Skill berhasil dihapus.'
            );
    }
}