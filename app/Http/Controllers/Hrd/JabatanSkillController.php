<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Jabatan;
use App\Models\JabatanSkill;
use App\Models\Skill;
use Illuminate\Http\Request;

class JabatanSkillController extends Controller
{
    public function store(
        Request $request,
        Jabatan $jabatan
    ) {
        $validated = $request->validate([
            'skill_id' => [
                'required',
                'exists:skills,id',
            ],

            'level_dibutuhkan' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],
        ]);

        $sudahAda = JabatanSkill::where(
            'jabatan_id',
            $jabatan->id
        )
            ->where(
                'skill_id',
                $validated['skill_id']
            )
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors([
                    'skill_id' =>
                        'Skill tersebut sudah dikaitkan dengan jabatan ini.',
                ])
                ->withInput();
        }

        JabatanSkill::create([
            'jabatan_id' => $jabatan->id,
            'skill_id' => $validated['skill_id'],
            'level_dibutuhkan' => $validated['level_dibutuhkan'],
        ]);

        return back()->with(
            'success',
            'Skill berhasil ditambahkan ke jabatan.'
        );
    }

    public function destroy(
        Jabatan $jabatan,
        JabatanSkill $jabatanSkill
    ) {
        if ($jabatanSkill->jabatan_id !== $jabatan->id) {
            abort(404);
        }

        $jabatanSkill->delete();

        return back()->with(
            'success',
            'Skill dari jabatan berhasil dihapus.'
        );
    }
}