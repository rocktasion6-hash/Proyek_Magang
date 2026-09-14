<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\HasilAssessment;
use App\Models\Jabatan;
use App\Models\PengajuanPengembangan;
use App\Models\RiwayatJabatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemindahanJabatanController extends Controller
{
    public function index(Request $request)
    {
        $query = PengajuanPengembangan::with([
            'karyawan.departemen',
            'jabatanAsal',
            'jabatanTujuan',
            'hasilAssessment.assessmentPeserta.assessment',
        ])->where('jenis_pengajuan', 'pemindahan_jabatan');

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
            'hrd.pemindahan-jabatan.index',
            compact('pengajuans')
        );
    }

    public function create()
    {
        // Hanya hasil assessment yang lulus
        $hasilAssessments = HasilAssessment::with([
            'assessmentPeserta.karyawan.jabatan',
            'assessmentPeserta.assessment.jabatan',
        ])
            ->where('status', 'lulus')
            ->latest('tanggal_ujian')
            ->get();

        $jabatans = Jabatan::orderBy('level_jabatan')
            ->orderBy('nama_jabatan')
            ->get();

        return view(
            'hrd.pemindahan-jabatan.create',
            compact('hasilAssessments', 'jabatans')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hasil_assessment_id' => [
                'required',
                'exists:hasil_assessments,id',
            ],
            'jabatan_tujuan_id' => [
                'required',
                'exists:jabatans,id',
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
            'assessmentPeserta.karyawan.jabatan',
        ])->findOrFail($validated['hasil_assessment_id']);

        // Wajib lulus
        if ($hasilAssessment->status !== 'lulus') {
            return back()
                ->withErrors([
                    'hasil_assessment_id' =>
                        'Karyawan harus memiliki hasil assessment dengan status lulus.',
                ])
                ->withInput();
        }

        $karyawan = $hasilAssessment->assessmentPeserta->karyawan;
        $jabatanAsal = $karyawan->jabatan;

        $jabatanTujuan = Jabatan::findOrFail(
            $validated['jabatan_tujuan_id']
        );

        // Tidak boleh pindah ke jabatan yang sama
        if ($jabatanTujuan->id === $jabatanAsal->id) {
            return back()
                ->withErrors([
                    'jabatan_tujuan_id' =>
                        'Jabatan tujuan tidak boleh sama dengan jabatan saat ini.',
                ])
                ->withInput();
        }

        // Cegah pengajuan ganda yang masih aktif
        $sudahAda = PengajuanPengembangan::where(
            'karyawan_id',
            $karyawan->id
        )
            ->where(
                'jenis_pengajuan',
                'pemindahan_jabatan'
            )
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
                        'Karyawan ini sudah memiliki pengajuan pemindahan jabatan yang aktif.',
                ])
                ->withInput();
        }

        PengajuanPengembangan::create([
            'karyawan_id' => $karyawan->id,
            'hasil_assessment_id' => $hasilAssessment->id,
            'jenis_pengajuan' => 'pemindahan_jabatan',
            'jabatan_asal_id' => $jabatanAsal->id,
            'jabatan_tujuan_id' => $jabatanTujuan->id,
            'skill_id' => null,
            'status' => 'diajukan',
            'tanggal_pengajuan' => $validated['tanggal_pengajuan'],
            'tanggal_keputusan' => null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()
            ->route('hrd.pemindahan-jabatan.index')
            ->with(
                'success',
                'Pengajuan pemindahan jabatan berhasil dibuat.'
            );
    }

    public function show(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'pemindahan_jabatan',
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
            'hrd.pemindahan-jabatan.show',
            compact('pengajuan')
        );
    }

    public function process(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'pemindahan_jabatan',
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
            'Pengajuan pemindahan jabatan sedang diproses.'
        );
    }

    public function approve(PengajuanPengembangan $pengajuan)
    {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'pemindahan_jabatan',
            404
        );

        if (!in_array($pengajuan->status, ['diajukan', 'diproses'])) {
            return back()->with(
                'error',
                'Pengajuan ini tidak dapat disetujui.'
            );
        }

        $hasilAssessment = HasilAssessment::findOrFail(
            $pengajuan->hasil_assessment_id
        );

        // Cek ulang sebelum approval
        if ($hasilAssessment->status !== 'lulus') {
            return back()->with(
                'error',
                'Pengajuan tidak dapat disetujui karena hasil assessment tidak lulus.'
            );
        }

        DB::transaction(function () use ($pengajuan) {

            $karyawan = $pengajuan->karyawan()
                ->lockForUpdate()
                ->firstOrFail();

            $tanggal = Carbon::now()->toDateString();

            // Tutup riwayat jabatan sebelumnya
            RiwayatJabatan::where('karyawan_id', $karyawan->id)
                ->whereNull('tanggal_selesai')
                ->update([
                    'tanggal_selesai' => $tanggal,
                ]);

            // Ubah jabatan karyawan
            $karyawan->update([
                'jabatan_id' => $pengajuan->jabatan_tujuan_id,
            ]);

            // Simpan riwayat jabatan baru
            RiwayatJabatan::create([
                'karyawan_id' => $karyawan->id,
                'jabatan_id' => $pengajuan->jabatan_tujuan_id,
                'jenis_perubahan' => 'pemindahan',
                'pengajuan_pengembangan_id' => $pengajuan->id,
                'hasil_assessment_id' => $pengajuan->hasil_assessment_id,
                'tanggal_mulai' => $tanggal,
                'tanggal_selesai' => null,
                'keterangan' =>
                    'Pemindahan jabatan berdasarkan hasil assessment.',
            ]);

            // Update pengajuan
            $pengajuan->update([
                'status' => 'disetujui',
                'tanggal_keputusan' => $tanggal,
            ]);
        });

        return back()->with(
            'success',
            'Pemindahan jabatan berhasil disetujui dan jabatan karyawan telah diperbarui.'
        );
    }

    public function reject(
        Request $request,
        PengajuanPengembangan $pengajuan
    ) {
        abort_if(
            $pengajuan->jenis_pengajuan !== 'pemindahan_jabatan',
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

        $pengajuan->update([
            'status' => 'ditolak',
            'tanggal_keputusan' => Carbon::now()->toDateString(),
            'catatan' => $validated['catatan'],
        ]);

        return back()->with(
            'success',
            'Pengajuan pemindahan jabatan telah ditolak.'
        );
    }
}