<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\RiwayatJabatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karyawan::with([
            'user',
            'departemen',
            'jabatan',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('departemen_id')) {
            $query->where(
                'departemen_id',
                $request->departemen_id
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        $karyawans = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $departemens = Departemen::orderBy(
            'nama_departemen'
        )->get();

        return view(
            'hrd.karyawan.index',
            compact(
                'karyawans',
                'departemens'
            )
        );
    }

    public function create()
    {
        $departemens = Departemen::orderBy(
            'nama_departemen'
        )->get();

        $jabatans = Jabatan::orderBy(
            'level_jabatan'
        )
            ->orderBy('nama_jabatan')
            ->get();

        return view(
            'hrd.karyawan.create',
            compact(
                'departemens',
                'jabatans'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => [
                'required',
                'string',
                'max:50',
                'unique:karyawans,nik',
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'username' => [
                'required',
                'string',
                'max:50',
                'unique:users,username',
            ],

            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
            ],

            'departemen_id' => [
                'required',
                'exists:departemens,id',
            ],

            'jabatan_id' => [
                'required',
                'exists:jabatans,id',
            ],

            'tanggal_masuk' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                    Rule::in([
                        'aktif',
                        'nonaktif',
                    ]),
                ],
            ]);

        DB::transaction(function () use ($validated) {

            /*
            |--------------------------------------------------------------------------
            | 1. Buat akun User
            |--------------------------------------------------------------------------
            */

            $user = User::create([
                'username' => $validated['username'],
                'name' => $validated['nama'],
                'email' => $validated['email'],
                'password' => Hash::make(
                    $validated['password']
                ),
                'role' => 'karyawan',
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2. Buat data Karyawan
            |--------------------------------------------------------------------------
            */

            $karyawan = Karyawan::create([
                'user_id' => $user->id,
                'nik' => $validated['nik'],
                'nama' => $validated['nama'],
                'departemen_id' => $validated['departemen_id'],
                'jabatan_id' => $validated['jabatan_id'],

                // Tidak lagi digunakan karena level
                // sudah berasal dari jabatan.
                'level' => null,

                'tanggal_masuk' => $validated['tanggal_masuk'],
                'status' => $validated['status'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | 3. Buat riwayat jabatan awal
            |--------------------------------------------------------------------------
            */

            RiwayatJabatan::create([
                'karyawan_id' => $karyawan->id,
                'jabatan_id' => $karyawan->jabatan_id,
                'jenis_perubahan' => 'awal',
                'pengajuan_pengembangan_id' => null,
                'hasil_assessment_id' => null,
                'tanggal_mulai' => $karyawan->tanggal_masuk,
                'tanggal_selesai' => null,
                'keterangan' =>
                'Jabatan awal saat karyawan mulai bekerja.',
            ]);
        });

        return redirect()
            ->route('hrd.karyawan.index')
            ->with(
                'success',
                'Karyawan dan akun login berhasil dibuat.'
            );
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load([
            'user',
            'departemen',
            'jabatan',
            'skills',
            'riwayatJabatans.jabatan',
            'riwayatSkills.skill',
        ]);

        return view(
            'hrd.karyawan.show',
            compact('karyawan')
        );
    }

    public function edit(Karyawan $karyawan)
    {
        $departemens = Departemen::orderBy(
            'nama_departemen'
        )->get();

        $jabatans = Jabatan::orderBy(
            'level_jabatan'
        )
            ->orderBy('nama_jabatan')
            ->get();

        return view(
            'hrd.karyawan.edit',
            compact(
                'karyawan',
                'departemens',
                'jabatans'
            )
        );
    }

    public function update(
        Request $request,
        Karyawan $karyawan
    ) {
        $validated = $request->validate([
            'nik' => [
                'required',
                'string',
                'max:50',
            Rule::unique(
                    'karyawans',
                    'nik'
                )->ignore($karyawan->id),
            ],

            'nama' => [
                'required',
                'string',
                'max:255',
            ],

            'departemen_id' => [
                'required',
                'exists:departemens,id',
            ],

            'tanggal_masuk' => [
                'required',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'nonaktif',
                ]),
            ],
        ]);

        $karyawan->update([
            'nik' => $validated['nik'],
            'nama' => $validated['nama'],
            'departemen_id' => $validated['departemen_id'],
            'tanggal_masuk' => $validated['tanggal_masuk'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route(
                'hrd.karyawan.show',
                $karyawan
            )
            ->with(
                'success',
                'Data karyawan berhasil diperbarui.'
            );
    }

    public function destroy(Karyawan $karyawan)
    {
        /*
        |--------------------------------------------------------------------------
        | Jangan sembarang menghapus data karyawan
        |--------------------------------------------------------------------------
        */

        $karyawan->update([
            'status' => 'nonaktif',
        ]);

        return redirect()
            ->route('hrd.karyawan.index')
            ->with(
                'success',
                'Karyawan berhasil dinonaktifkan.'
            );
    }
}