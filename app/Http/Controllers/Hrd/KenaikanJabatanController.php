<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\HasilAssessment;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\PengajuanPengembangan;
use App\Models\RiwayatJabatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\BlockchainRecord;
use App\Services\BlockchainService;

class KenaikanJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanPengembangan::with([
            'karyawan.departemen',
            'jabatanAsal',
            'jabatanTujuan',
            'hasilAssessment.assessmentPeserta.assessment',
        ])->where('jenis_pengajuan', 'kenaikan_jabatan');

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

        return view('hrd.kenaikan-jabatan.index', compact('pengajuans'));
    }

    public function create()
    {
        /*
        |--------------------------------------------------------------------------
        | Ambil hasil assessment yang LULUS
        |--------------------------------------------------------------------------
        | Hanya hasil yang lulus yang boleh digunakan untuk kenaikan jabatan.
        */

        $hasilAssessments = HasilAssessment::with([
            'assessmentPeserta.karyawan.jabatan',
            'assessmentPeserta.assessment.jabatan',
        ])
            ->where('status', 'lulus')
            ->latest('tanggal_ujian')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Ambil semua jabatan
        |--------------------------------------------------------------------------
        */

        $jabatans = Jabatan::orderBy('level_jabatan')
            ->orderBy('nama_jabatan')
            ->get();

        return view(
            'hrd.kenaikan-jabatan.create',
            compact('hasilAssessments', 'jabatans')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hasil_assessment_id' => ['required', 'exists:hasil_assessments,id'],
            'jabatan_tujuan_id' => ['required', 'exists:jabatans,id'],
            'tanggal_pengajuan' => ['required', 'date'],
            'catatan' => ['nullable', 'string'],
        ]);

        $hasilAssessment = HasilAssessment::with([
            'assessmentPeserta.karyawan.jabatan',
        ])->findOrFail($validated['hasil_assessment_id']);

        /*
        |--------------------------------------------------------------------------
        | Pastikan hasil assessment LULUS
        |--------------------------------------------------------------------------
        */

        if ($hasilAssessment->status !== 'lulus') {
            return back()
                ->withErrors([
                    'hasil_assessment_id' =>
                        'Karyawan harus memiliki hasil assessment dengan status lulus.'
                ])
                ->withInput();
        }

        $karyawan = $hasilAssessment->assessmentPeserta->karyawan;

        $jabatanAsal = $karyawan->jabatan;

        $jabatanTujuan = Jabatan::findOrFail(
            $validated['jabatan_tujuan_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Jabatan tujuan harus lebih tinggi
        |--------------------------------------------------------------------------
        */

        if ($jabatanTujuan->level_jabatan <= $jabatanAsal->level_jabatan) {
            return back()
                ->withErrors([
                    'jabatan_tujuan_id' =>
                        'Jabatan tujuan harus memiliki level yang lebih tinggi dari jabatan saat ini.'
                ])
                ->withInput();
        }

        /*
        |--------------------------------------------------------------------------
        | Cegah pengajuan ganda untuk hasil assessment yang sama
        |--------------------------------------------------------------------------
        */

        $sudahAda = PengajuanPengembangan::where(
            'hasil_assessment_id',
            $hasilAssessment->id
        )
            ->where('jenis_pengajuan', 'kenaikan_jabatan')
            ->whereIn('status', [
                'diajukan',
                'diproses',
                'disetujui',
            ])
            ->exists();

        if ($sudahAda) {
            return back()
                ->withErrors([
                    'hasil_assessment_id' =>
                        'Hasil assessment ini sudah digunakan untuk pengajuan kenaikan jabatan.'
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
            'jenis_pengajuan' => 'kenaikan_jabatan',
            'jabatan_asal_id' => $jabatanAsal->id,
            'jabatan_tujuan_id' => $jabatanTujuan->id,
            'skill_id' => null,
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
                'jabatan_asal_id' => $pengajuan->jabatan_asal_id,
                'jabatan_tujuan_id' => $pengajuan->jabatan_tujuan_id,
                'status' => $pengajuan->status,
                'tanggal_pengajuan' => $pengajuan->tanggal_pengajuan,
                'tanggal_keputusan' => $pengajuan->tanggal_keputusan,
                'catatan' => $pengajuan->catatan,
                ]
            );
        }

        return redirect()
            ->route('hrd.kenaikan-jabatan.index')
            ->with(
                'success',
                'Pengajuan kenaikan jabatan berhasil dibuat.'
            );
    }

    public function show(PengajuanPengembangan $pengajuan)
    {
        /*
        |--------------------------------------------------------------------------
        | Pastikan yang dibuka memang kenaikan jabatan
        |--------------------------------------------------------------------------
        */

        abort_if(
            $pengajuan->jenis_pengajuan !== 'kenaikan_jabatan',
            404
        );

        $pengajuan->load([
            'karyawan.departemen',
            'karyawan.jabatan',
            'jabatanAsal',
            'jabatanTujuan',
            'hasilAssessment.assessmentPeserta.assessment.jabatan',
            'hasilAssessment.assessmentPeserta.assessment.skill',
        ]);

        return view(
            'hrd.kenaikan-jabatan.show',
            compact('pengajuan')
        );
    }

    public function process(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'kenaikan_jabatan',
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
            'Pengajuan kenaikan jabatan sedang diproses.'
        );
    }

    public function approve(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'kenaikan_jabatan',
            404
        );

        if (!in_array($pengajuan->status, ['diajukan', 'diproses'])) {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat disetujui.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan hasil assessment masih lulus
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

        DB::transaction(function () use ($pengajuan) {

            $karyawan = Karyawan::lockForUpdate()->findOrFail(
                $pengajuan->karyawan_id
            );

            $tanggal = Carbon::now()->toDateString();

            /*
            |--------------------------------------------------------------------------
            | Tutup riwayat jabatan sebelumnya
            |--------------------------------------------------------------------------
            */

            RiwayatJabatan::where('karyawan_id', $karyawan->id)
                ->whereNull('tanggal_selesai')
                ->update([
                    'tanggal_selesai' => $tanggal,
                ]);

            /*
            |--------------------------------------------------------------------------
            | Ubah jabatan karyawan
            |--------------------------------------------------------------------------
            */

            $karyawan->update([
                'jabatan_id' => $pengajuan->jabatan_tujuan_id,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Simpan riwayat jabatan baru
            |--------------------------------------------------------------------------
            */

            RiwayatJabatan::create([
                'karyawan_id' => $karyawan->id,
                'jabatan_id' => $pengajuan->jabatan_tujuan_id,
                'jenis_perubahan' => 'kenaikan',
                'pengajuan_pengembangan_id' => $pengajuan->id,
                'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
                'tanggal_mulai' => $tanggal,
                'tanggal_selesai' => null,
                'keterangan' => 'Kenaikan jabatan berdasarkan hasil assessment.',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Update pengajuan
            |--------------------------------------------------------------------------
            */

            $pengajuan->update([
                'status' => 'disetujui',
                'tanggal_keputusan' => $tanggal,
            
            
            ]);
            
            $blockchain = app(BlockchainService::class);

            $blockchain->addBlock(
            'keputusan_kenaikan_jabatan',
            $pengajuan->id,
            [
            'pengajuan_pengembangan_id' => $pengajuan->id,
            'karyawan_id' => $karyawan->id,
            'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
            'jabatan_asal_id' => $pengajuan->jabatan_asal_id,
            'jabatan_tujuan_id' => $pengajuan->jabatan_tujuan_id,
            'status' => 'disetujui',
            'tanggal_keputusan' => $tanggal,
            ]
        );

        });

        

        return back()->with(
            'success',
            'Kenaikan jabatan berhasil disetujui dan jabatan karyawan telah diperbarui.'
        );
    }

    public function reject(
        Request $request,
        PengajuanPengembangan $pengajuan
    ) {
    abort_if(
        $pengajuan->jenis_pengajuan !== 'kenaikan_jabatan',
        404
    );

    if (!in_array($pengajuan->status, ['diajukan', 'diproses'])) {
        return back()->with(
            'error',
            'Pengajuan ini tidak dapat ditolak.'
        );
    }

    $validated = $request->validate([
        'catatan' => ['required', 'string'],
    ]);

    DB::transaction(function () use ($pengajuan, $validated) {

        $tanggal = Carbon::now()->toDateString();

        /*
        |--------------------------------------------------------------------------
        | Update pengajuan menjadi ditolak
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

        $blockchain->addBlock(
            'keputusan_kenaikan_jabatan',
            $pengajuan->id,
            [
                'pengajuan_pengembangan_id' => $pengajuan->id,
                'karyawan_id' => $pengajuan->karyawan_id,
                'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
                'jabatan_asal_id' => $pengajuan->jabatan_asal_id,
                'jabatan_tujuan_id' => $pengajuan->jabatan_tujuan_id,
                'status' => 'ditolak',
                'tanggal_keputusan' => $tanggal,
                'catatan' => $validated['catatan'],
            ]
        );
    });

    return back()->with(
        'success',
        'Pengajuan kenaikan jabatan telah ditolak dan keputusan telah dicatat ke blockchain.'
    );
    }
}