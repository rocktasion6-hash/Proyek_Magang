<?php

use App\Models\Departemen;
use App\Models\Jabatan;
use App\Models\Soal;
use App\Models\SoalUpload;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

uses(RefreshDatabase::class);

it('uploads a file for every level and appends to existing question banks', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-upload-cumulative-banks',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);

    $jabatanPerLevel = collect([
        1 => 'Operator Loader LEV 1',
        2 => 'Operator Loader LEVEL 2',
        3 => 'Operator Loader Lv. 3',
    ])->map(fn (string $namaJabatan, int $level): Jabatan => Jabatan::create([
        'nama_jabatan' => $namaJabatan,
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => $level,
    ]));
    $bankLama = SoalUpload::create([
        'jabatan_id' => $jabatanPerLevel[1]->id,
        'nama_file' => 'operator-loader-level-1-lama.xlsx',
        'jumlah_soal' => 1,
    ]);
    Soal::create([
        'jabatan_id' => $jabatanPerLevel[1]->id,
        'upload_id' => $bankLama->id,
        'pertanyaan' => 'Pertanyaan bank lama level satu',
        'tipe_soal' => 'pilihan_ganda',
        'status' => true,
    ]);

    $response = $this->actingAs($hrd)
        ->post(route('hrd.soal.upload.store'), [
            'departemen_id' => $departemen->id,
            'jabatan_id' => $jabatanPerLevel[3]->id,
            'file_excel' => [
                1 => buatFileExcelBankSoal('operator-level-1.xlsx', 'Pertanyaan level satu'),
                2 => buatFileExcelBankSoal('operator-level-2.xlsx', 'Pertanyaan level dua'),
                3 => buatFileExcelBankSoal('operator-level-3.xlsx', 'Pertanyaan level tiga'),
            ],
        ]);

    $response->assertRedirect(route('hrd.soal.index'))
        ->assertSessionHas('success', '3 soal dari 3 file berhasil diupload untuk jabatan Operator Loader Lv. 3.');

    $this->assertDatabaseCount('soal_uploads', 4);
    $this->assertDatabaseCount('soals', 4);
    $this->assertModelExists($bankLama);
    $this->assertDatabaseHas('soals', [
        'upload_id' => $bankLama->id,
        'pertanyaan' => 'Pertanyaan bank lama level satu',
    ]);

    foreach ($jabatanPerLevel as $level => $jabatan) {
        $this->assertDatabaseHas('soals', [
            'jabatan_id' => $jabatan->id,
            'pertanyaan' => 'Pertanyaan level '.['satu', 'dua', 'tiga'][$level - 1],
        ]);

        $this->assertDatabaseHas('soal_uploads', [
            'jabatan_id' => $jabatan->id,
            'departemen_id' => $departemen->id,
            'jumlah_soal' => 1,
        ]);
    }
});

it('shows department folders before the excel upload form', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-upload-department-folders',
        'role' => 'hrd',
    ]);
    Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.soal.upload'))
        ->assertSee('Pilih folder departemen')
        ->assertSee('Produksi')
        ->assertDontSee('name="jabatan_id"', false);
});

it('opens an upload form inside the selected department folder', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-upload-selected-department',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    Jabatan::create([
        'nama_jabatan' => 'Operator Loader Level 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.soal.upload', ['departemen_id' => $departemen->id]))
        ->assertSee('Folder: Produksi')
        ->assertSee('name="departemen_id"', false)
        ->assertSee('name="jabatan_id"', false);
});

it('uploads one question bank for a structural position', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-upload-structural-bank',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatanStruktural = Jabatan::create([
        'nama_jabatan' => 'Wakil Foreman',
        'tipe_jabatan' => 'struktural',
        'urutan_struktural' => 1,
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.soal.upload', ['departemen_id' => $departemen->id]))
        ->assertSee('Wakil Foreman')
        ->assertSee('Jabatan struktural menggunakan satu file bank soal khusus');

    $this->post(route('hrd.soal.upload.store'), [
        'departemen_id' => $departemen->id,
        'jabatan_id' => $jabatanStruktural->id,
        'file_excel' => [
            1 => buatFileExcelBankSoal('wakil-foreman.xlsx', 'Pertanyaan Wakil Foreman'),
        ],
    ])->assertRedirect(route('hrd.soal.index'))
        ->assertSessionHas('success', '1 soal dari 1 file berhasil diupload untuk jabatan Wakil Foreman.');

    $bankSoal = SoalUpload::query()->where('jabatan_id', $jabatanStruktural->id)->firstOrFail();
    $this->assertDatabaseHas('soals', [
        'upload_id' => $bankSoal->id,
        'jabatan_id' => $jabatanStruktural->id,
        'pertanyaan' => 'Pertanyaan Wakil Foreman',
    ]);
});

it('hides operational level labels for structural question banks and questions', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-structural-bank-labels',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatanStruktural = Jabatan::create([
        'nama_jabatan' => 'Foreman',
        'tipe_jabatan' => 'struktural',
        'urutan_struktural' => 2,
        'level_jabatan' => 1,
    ]);
    $jabatanOperasional = Jabatan::create([
        'nama_jabatan' => 'Operator Loader',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 2,
    ]);
    $bankStruktural = SoalUpload::create([
        'jabatan_id' => $jabatanStruktural->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'foreman.xlsx',
        'jumlah_soal' => 1,
    ]);
    SoalUpload::create([
        'jabatan_id' => $jabatanOperasional->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'operator-loader.xlsx',
        'jumlah_soal' => 1,
    ]);
    $soal = Soal::create([
        'jabatan_id' => $jabatanStruktural->id,
        'upload_id' => $bankStruktural->id,
        'pertanyaan' => 'Pertanyaan Foreman',
        'tipe_soal' => 'pilihan_ganda',
        'status' => true,
    ]);

    $this->actingAs($hrd)
        ->get(route('hrd.soal.index'))
        ->assertSee('Foreman')
        ->assertSee('(Level 2)')
        ->assertDontSee('(Level 1)');

    $this->get(route('hrd.soal.upload.show', $bankStruktural))
        ->assertSee('Foreman')
        ->assertDontSee('(Level 1)');

    $this->get(route('hrd.soal.show', $soal))
        ->assertSee('Foreman')
        ->assertDontSee('(Level 1)');
});

it('hides the xlsx extension from bank filenames in the index', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-bank-filename-extension',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);
    $jabatan = Jabatan::create([
        'nama_jabatan' => 'Operator Loader',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);

    $bankSoal = SoalUpload::create([
        'jabatan_id' => $jabatan->id,
        'departemen_id' => $departemen->id,
        'nama_file' => 'bank-soal.xlsx',
        'jumlah_soal' => 1,
    ]);

    expect($bankSoal->nama_file_tampilan)->toBe('bank-soal');
    expect($bankSoal->getRawOriginal('nama_file'))->toBe('bank-soal.xlsx');

    $this->actingAs($hrd)
        ->get(route('hrd.soal.index'))
        ->assertSee('bank-soal')
        ->assertDontSee('bank-soal.xlsx');
});

it('rejects a cumulative bank upload when a level file is missing', function () {
    $hrd = User::factory()->create([
        'username' => 'hrd-upload-incomplete-banks',
        'role' => 'hrd',
    ]);
    $departemen = Departemen::create([
        'nama_departemen' => 'Produksi',
    ]);

    Jabatan::create([
        'nama_jabatan' => 'Operator Loader Level 1',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 1,
    ]);
    $jabatanLevelDua = Jabatan::create([
        'nama_jabatan' => 'Operator Loader Level 2',
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => 2,
    ]);

    $response = $this->actingAs($hrd)
        ->post(route('hrd.soal.upload.store'), [
            'departemen_id' => $departemen->id,
            'jabatan_id' => $jabatanLevelDua->id,
            'file_excel' => [
                2 => buatFileExcelBankSoal('operator-level-2.xlsx', 'Pertanyaan level dua'),
            ],
        ]);

    $response->assertSessionHasErrors([
        'file_excel' => 'Pilih satu file Excel untuk setiap level dari level 1 sampai level tujuan.',
    ]);

    $this->assertDatabaseCount('soal_uploads', 0);
    $this->assertDatabaseCount('soals', 0);
});

function buatFileExcelBankSoal(string $namaFile, string $pertanyaan): UploadedFile
{
    $spreadsheet = new Spreadsheet;
    $spreadsheet->getActiveSheet()->fromArray([
        ['nomor', 'prosedur pengoperasian', 'nilai point', 'pertanyaan', 'a', 'b', 'c', 'd', 'e', 'jawaban benar'],
        [1, 'Prosedur Pengoperasian Jabatan', 1, $pertanyaan, 'Pilihan A', 'Pilihan B', '', '', '', 'A'],
    ]);

    $path = tempnam(sys_get_temp_dir(), 'bank-soal-');

    if ($path === false) {
        throw new RuntimeException('Tidak dapat membuat file sementara untuk pengujian.');
    }

    try {
        (new Xlsx($spreadsheet))->save($path);
        $content = file_get_contents($path);

        if ($content === false) {
            throw new RuntimeException('Tidak dapat membaca file sementara untuk pengujian.');
        }

        return UploadedFile::fake()->createWithContent($namaFile, $content);
    } finally {
        unlink($path);
        $spreadsheet->disconnectWorksheets();
    }
}
