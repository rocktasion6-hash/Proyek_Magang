<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\HasilAssessment;
use App\Models\KaryawanSkill;
use App\Models\PengajuanPengembangan;
use App\Models\RiwayatSkill;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\BlockchainRecord;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\DB;

class PeningkatanSkillController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanPengembangan::with([
            'karyawan.departemen',
            'skill',
            'hasilAssessment.assessmentPeserta.assessment',
        ])->where(
            'jenis_pengajuan',
            'peningkatan_skill'
        );

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('karyawan', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        $pengajuans = $query
            ->latest('tanggal_pengajuan')
            ->paginate(10)
            ->withQueryString();

        return view(
            'hrd.peningkatan-skill.index',
            compact('pengajuans')
        );
    }

    public function create()
    {
        // Hanya hasil assessment yang lulus
        $hasilAssessments = HasilAssessment::with([
            'assessmentPeserta.karyawan',
            'assessmentPeserta.assessment.skill',
            'assessmentPeserta.assessment.jabatan',
        ])
            ->where('status', 'lulus')
            ->latest('tanggal_ujian')
            ->get();

        $skills = Skill::orderBy('nama_skill')->get();

        return view(
            'hrd.peningkatan-skill.create',
            compact(
                'hasilAssessments',
                'skills'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hasil_assessment_id' => [
                'required',
                'exists:hasil_assessments,id',
            ],

            'skill_id' => [
                'required',
                'exists:skills,id',
            ],

            'level_sesudah' => [
                'required',
                'integer',
                'min:1',
                'max:5',
            ],

            'tanggal_pengajuan' => [
                'required',
                'date',
            ],

            'catatan' => [
                'nullable',
                'string',
            ],
        ]);

        $hasilAssessment = HasilAssessment::with([
            'assessmentPeserta.karyawan',
            'assessmentPeserta.assessment.skill',
        ])->findOrFail(
            $validated['hasil_assessment_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Wajib lulus
        |--------------------------------------------------------------------------
        */

        if ($hasilAssessment->status !== 'lulus') {
            return back()
                ->withErrors([
                    'hasil_assessment_id' =>
                        'Karyawan harus memiliki hasil assessment dengan status lulus.',
                ])
                ->withInput();
        }

        $karyawan = $hasilAssessment
            ->assessmentPeserta
            ->karyawan;

        $skill = Skill::findOrFail(
            $validated['skill_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Ambil level skill saat ini
        |--------------------------------------------------------------------------
        */

        $karyawanSkill = KaryawanSkill::where(
            'karyawan_id',
            $karyawan->id
        )
            ->where(
                'skill_id',
                $skill->id
            )
            ->first();

        $levelSebelum = $karyawanSkill
            ? (int) $karyawanSkill->level_skill
            : 0;

        $levelSesudah = (int) $validated['level_sesudah'];

        /*
        |--------------------------------------------------------------------------
        | Level baru harus lebih tinggi
        |--------------------------------------------------------------------------
        */

        if ($levelSesudah <= $levelSebelum) {
            return back()
                ->withErrors([
                    'level_sesudah' =>
                        'Level skill baru harus lebih tinggi dari level skill saat ini.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Jika assessment memiliki skill tertentu,
        | skill peningkatan harus sama.
        |--------------------------------------------------------------------------
        */

        $assessmentSkill =
            $hasilAssessment
                ->assessmentPeserta
                ->assessment
                ->skill;

        if (
            $assessmentSkill &&
            $assessmentSkill->id !== $skill->id
        ) {
            return back()
                ->withErrors([
                    'skill_id' =>
                        'Skill yang ditingkatkan harus sesuai dengan skill pada assessment.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Cegah pengajuan aktif ganda
        |--------------------------------------------------------------------------
        */

        $sudahAda = PengajuanPengembangan::where(
            'karyawan_id',
            $karyawan->id
        )
            ->where(
                'skill_id',
                $skill->id
            )
            ->where(
                'jenis_pengajuan',
                'peningkatan_skill'
            )
            ->whereIn('status', [
                'diajukan',
                'diproses',
            ])
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors([
                    'skill_id' =>
                        'Karyawan ini sudah memiliki pengajuan peningkatan skill yang sedang diproses.',
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan pengajuan
        |--------------------------------------------------------------------------
        */

        $pengajuan = PengajuanPengembangan::create([
            'karyawan_id' => $karyawan->id,
            'hasil_assessment_id' => $hasilAssessment->id,
            'jenis_pengajuan' => 'peningkatan_skill',
            'jabatan_asal_id' => null,
            'jabatan_tujuan_id' => null,
            'skill_id' => $skill->id,

            'level_sebelum' => $levelSebelum,
            'level_sesudah' => $levelSesudah,

            'status' => 'diajukan',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'tanggal_keputusan' => null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $blockchain = app(BlockchainService::class);

        $blockchainRecordExists = BlockchainRecord::query()
            ->where('entity_type', 'pengajuan_pengembangan')
            ->where('entity_id', $pengajuan->id)
            ->exists();

        if (!$blockchainRecordExists) {
            $blockchain->addBlock(
            'pengajuan_pengembangan',
            $pengajuan->id,
            [
                'karyawan_id' => $pengajuan->karyawan_id,
                'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
                'jenis_pengajuan' => $pengajuan->jenis_pengajuan,
                'skill_id' => $pengajuan->skill_id,
                'level_sebelum' => $pengajuan->level_sebelum,
                'level_sesudah' => $pengajuan->level_sesudah,
                'status' => $pengajuan->status,
                'tanggal_pengajuan' => $pengajuan->tanggal_pengajuan,
                'tanggal_keputusan' => $pengajuan->tanggal_keputusan,
                'catatan' => $pengajuan->catatan,
            ]
        );
    }

        return redirect()
            ->route('hrd.peningkatan-skill.index')
            ->with(
                'success',
                'Pengajuan peningkatan skill berhasil dibuat.'
            );
    }

    public function show(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'peningkatan_skill',
            404
        );

        $pengajuan->load([
            'karyawan.departemen',
            'karyawan.jabatan',
            'karyawan.skills',
            'skill',
            'hasilAssessment.assessmentPeserta.assessment.skill',
            'hasilAssessment.assessmentPeserta.assessment.jabatan',
        ]);

        return view(
            'hrd.peningkatan-skill.show',
            compact('pengajuan')
        );
    }

    public function process(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'peningkatan_skill',
            404
        );

        if ($pengajuan->status !== 'diajukan') {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat diproses.'
            );
        }

        $pengajuan->update([
            'status' => 'diproses',
        ]);

        return back()->with(
            'success',
            'Pengajuan peningkatan skill sedang diproses.'
        );
    }

    public function approve(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'peningkatan_skill',
            404
        );

        if (!in_array(
            $pengajuan->status,
            ['diajukan', 'diproses']
        )) {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat disetujui.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Cek hasil assessment
        |--------------------------------------------------------------------------
        */

        $hasilAssessment = HasilAssessment::findOrFail(
            $pengajuan->hasil_assessment_id
        );

        if ($hasilAssessment->status !== 'lulus') {
            return back()->with(
                'error',
                'Pengajuan tidak dapat disetujui karena hasil assessment tidak lulus.'
            );
        }

        $levelSebelum = (int) $pengajuan->level_sebelum;
        $levelSesudah = (int) $pengajuan->level_sesudah;

        if ($levelSesudah <= $levelSebelum) {
            return back()->with(
                'error',
                'Level skill baru harus lebih tinggi dari level sebelumnya.'
            );
        }

        DB::transaction(function () use (
            $pengajuan,
            $levelSebelum,
            $levelSesudah
        ) {

        /*
        |--------------------------------------------------------------------------
        | Ambil skill karyawan
        |--------------------------------------------------------------------------
        */

        $karyawanSkill = KaryawanSkill::where(
            'karyawan_id',
            $pengajuan->karyawan_id
        )
            ->where(
                'skill_id',
                $pengajuan->skill_id
            )
            ->lockForUpdate()
            ->first();

        $levelAktual = $karyawanSkill
            ? (int) $karyawanSkill->level_skill
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Pastikan kondisi database belum berubah
        |--------------------------------------------------------------------------
        */

        if ($levelAktual !== $levelSebelum) {
            throw new \Exception(
                'Level skill karyawan telah berubah sejak pengajuan dibuat. Periksa kembali pengajuan ini.'
            );
        }

        $tanggal = Carbon::now()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Update / buat karyawan_skill
        |--------------------------------------------------------------------------
        */

        if ($karyawanSkill) {

            $karyawanSkill->update([
                'level_skill' => $levelSesudah,
                'tanggal_penilaian' => $tanggal,
            ]);

        } else {

            $karyawanSkill = KaryawanSkill::create([
                'karyawan_id' => $pengajuan->karyawan_id,
                'skill_id' => $pengajuan->skill_id,
                'level_skill' => $levelSesudah,
                'tanggal_penilaian' => $tanggal,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan riwayat skill
        |--------------------------------------------------------------------------
        */

        RiwayatSkill::create([
            'karyawan_id' => $pengajuan->karyawan_id,
            'skill_id' => $pengajuan->skill_id,
            'level_sebelum' => $levelSebelum,
            'level_sesudah' => $levelSesudah,
            'pengajuan_pengembangan_id' => $pengajuan->id,
            'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
            'tanggal_perubahan' => $tanggal,
            'keterangan' => 'Peningkatan skill berdasarkan hasil assessment.',

            
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update status pengajuan
        |--------------------------------------------------------------------------
        */

        $pengajuan->update([
            'status' => 'disetujui',
            'tanggal_keputusan' => $tanggal,    
        ]);

        /*
        |--------------------------------------------------------------------------
        | Catat keputusan peningkatan skill ke blockchain
        |--------------------------------------------------------------------------
        */

        $blockchain = app(BlockchainService::class);

        $blockchainExists = BlockchainRecord::query()
            ->where('entity_type', 'keputusan_peningkatan_skill')
            ->where('entity_id', $pengajuan->id)
            ->exists();

        if (!$blockchainExists) {
        $blockchain->addBlock(
            'keputusan_peningkatan_skill',
            $pengajuan->id,
            [
            'pengajuan_pengembangan_id' => $pengajuan->id,
            'karyawan_id' => $pengajuan->karyawan_id,
            'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
            'skill_id' => $pengajuan->skill_id,
            'level_sebelum' => $pengajuan->level_sebelum,
            'level_sesudah' => $pengajuan->level_sesudah,
            'status' => 'disetujui',
            'tanggal_keputusan' => $tanggal,
            ]
        );
    }
    
    });
        return back()->with(
            'success',
            'Peningkatan skill berhasil disetujui dan level skill karyawan telah diperbarui.'
        );
    }

    public function reject(
        Request $request,
        PengajuanPengembangan $pengajuan
    ) {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'peningkatan_skill',
            404
        );

        if (!in_array($pengajuan->status, ['diajukan', 'diproses'])) {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat ditolak.'
            );
        }

        $validated = $request->validate([
            'catatan' => [
                'required',
                'string',
            ],
        ]);

        DB::transaction(function () use (
            $pengajuan,
            $validated
        ) {

            $tanggal = Carbon::now()->toDateString();

            /*
            |--------------------------------------------------------------------------
            | Update status pengajuan
            |--------------------------------------------------------------------------
            */

            $pengajuan->update([
                'status' => 'ditolak',
                'tanggal_keputusan' => $tanggal,
                'catatan' => $validated['catatan'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | Catat keputusan penolakan ke blockchain
            |--------------------------------------------------------------------------
            */

            $blockchain = app(BlockchainService::class);

            $blockchainExists = BlockchainRecord::query()
                ->where(
                    'entity_type',
                    'keputusan_peningkatan_skill'
                )
                ->where(
                    'entity_id',
                    $pengajuan->id
                )
                ->exists();

            if (!$blockchainExists) {
                $blockchain->addBlock(
                    'keputusan_peningkatan_skill',
                    $pengajuan->id,
                    [
                        'pengajuan_pengembangan_id' => $pengajuan->id,
                        'karyawan_id' => $pengajuan->karyawan_id,
                        'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
                        'skill_id' => $pengajuan->skill_id,
                        'level_sebelum' => $pengajuan->level_sebelum,
                        'level_sesudah' => $pengajuan->level_sesudah,
                        'status' => 'ditolak',
                        'tanggal_keputusan' => $tanggal,
                    'catatan' => $validated['catatan'],
                    ]
                );
            }
        });

        return back()->with(
            'success',
            'Pengajuan peningkatan skill telah ditolak dan keputusan telah dicatat ke blockchain.'
        );
    }
}