<?php

namespace App\Imports;

use App\Models\ExcelTest;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ExcelImport implements ToModel, WithBatchInserts, WithChunkReading
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        return new ExcelTest([
            'test1' => $row[0],
            'test2' => $row[1],
            'test3' => $row[2],
            'test4' => $row[3],
            'test5' => $row[4],
            'test6' => $row[5],
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
