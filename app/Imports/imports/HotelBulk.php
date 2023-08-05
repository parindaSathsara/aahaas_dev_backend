<?php

namespace App\Imports\imports;

use App\Models\Hotels\Hotel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;

class HotelBulk implements ToModel, WithBatchInserts, WithChunkReading, WithStartRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function model(array $row)
    {
        $DateTime = \Carbon\Carbon::now();

        return new Hotel([
            'hotel_name' => $row[0],
            'hotel_description' => $row[1],
            'hotel_level' => $row[2],
            'category1' => $row[3],
            'longtitude' => $row[4],
            'latitude' => $row[5],
            'provider' => $row[6],
            'hotel_address' => $row[7],
            'trip_advisor_link' => $row[8],
            'hotel_image' => $row[9],
            'city' => $row[10],
            'micro_location' => $row[11],
            'hotel_status' => $row[12],
            'startdate' => $row[13],
            'enddate' => $row[14],
            'vendor_id' => $row[15],
            'created_at' => $DateTime,
            'updated_at' => $DateTime,
            'updated_by' => $row[16]
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
