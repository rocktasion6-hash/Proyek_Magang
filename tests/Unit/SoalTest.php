<?php

use App\Models\Soal;

test('procedure names map to assessment procedure categories', function () {
    expect(Soal::tipeProsedurDariNama('Prosedur Pengoperasian Jabatan'))
        ->toBe('pengoperasian_jabatan');

    expect(Soal::tipeProsedurDariNama('Prosedur Pengoperasian Keselamatan'))
        ->toBe('keselamatan_kerja');

    expect(Soal::tipeProsedurDariNama('Peraturan Perusahaan'))
        ->toBe('peraturan_perusahaan');

    expect(Soal::tipeProsedurDariNama(null))
        ->toBe('pengoperasian_jabatan');
});
