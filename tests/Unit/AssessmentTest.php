<?php

use App\Models\Assessment;
use App\Models\Jabatan;
use App\Models\Soal;

it('uses the operational assessment target level to set question points', function (
    int $level,
    float $multiPoint,
) {
    $assessment = new Assessment;
    $assessment->setRelation('jabatan', new Jabatan([
        'tipe_jabatan' => 'operasional',
        'level_jabatan' => $level,
    ]));
    $soalMulti = new Soal([
        'tipe_soal' => 'multi_jawaban',
        'nilai_point' => 3,
    ]);
    $soalPilihanGanda = new Soal([
        'tipe_soal' => 'pilihan_ganda',
        'nilai_point' => 7,
    ]);

    expect($assessment->nilaiPointUntukSoal($soalMulti))->toBe($multiPoint);
    expect($assessment->nilaiPointUntukSoal($soalPilihanGanda))->toBe(1.0);
})->with([
    'level 1' => [1, 3.0],
    'level 2' => [2, 2.0],
    'level 3' => [3, 2.0],
    'level 4' => [4, 1.0],
    'level 5' => [5, 1.0],
]);

it('keeps stored points for assessments outside operational levels 1 to 5', function () {
    $assessment = new Assessment;
    $assessment->setRelation('jabatan', new Jabatan([
        'tipe_jabatan' => 'struktural',
        'level_jabatan' => 1,
    ]));
    $soal = new Soal([
        'tipe_soal' => 'multi_jawaban',
        'nilai_point' => 5,
    ]);

    expect($assessment->nilaiPointUntukSoal($soal))->toBe(5.0);
});
