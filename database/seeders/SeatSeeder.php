<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $roomId = 1;
        $rows   = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        foreach ($rows as $row) {
            for ($i = 1; $i <= 14; $i++) {
                DB::table('seats')->updateOrInsert(
                    ['room_id' => $roomId, 'seat_name' => $row . $i],
                    [
                        'seat_type'  => 'standard',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        }

        $this->command->info('✅ Đã cập nhật/thêm ghế cho room_id = ' . $roomId);
    }
}