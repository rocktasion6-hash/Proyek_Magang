<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\KaryawanSkill;
use App\Models\Skill;
use Illuminate\Http\Request;

class KaryawanSkillController extends Controller
{
    public function store(
        Request $request,
        Karyawan $karyawan
    ) {
        $validated = $request->validate([
            'skill_id' => [
                'required',
                'exists:skills,id',
            ],

            'level_skill' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'tanggal_penilaian' => [
                'required',
                'date',
            ],
        ]);

        $karyawanSkill = KaryawanSkill::where(
            'karyawan_id',
            $karyawan->id
        )
            ->where(
                'skill_id',
                $validated['skill_id']
            )
            ->first();

        if ($karyawanSkill) {

            /*
            |--------------------------------------------------------------------------
            | Jika skill sudah ada, kita update levelnya.
            |--------------------------------------------------------------------------
            */

            $karyawanSkill->update([
                'level_skill' =>
                    $validated['level_skill'],
                'tanggal_penilaian' =>
                    $validated['tanggal_penilaian'],
            ]);

            return back()->with(
                'success',
                'Level skill karyawan berhasil diperbarui.'
            );
        }

        KaryawanSkill::create([
            'karyawan_id' => $karyawan->id,
            'skill_id' => $validated['skill_id'],
            'level_skill' => $validated['level_skill'],
            'tanggal_penilaian' =>
                $validated['tanggal_penilaian'],
        ]);

        return back()->with(
            'success',
            'Skill berhasil ditambahkan ke karyawan.'
        );
    }

    public function destroy(
        Karyawan $karyawan,
        KaryawanSkill $karyawanSkill
    ) {
        if ($karyawanSkill->karyawan_id !== $karyawan->id) {
            abort(404);
        }

        $karyawanSkill->delete();

        return back()->with(
            'success',
            'Skill karyawan berhasil dihapus.'
        );
    }
}