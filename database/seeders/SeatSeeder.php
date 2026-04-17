<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeatSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = DB::table('rooms')->get();
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        foreach ($rooms as $room) {
            // Phòng 100 ghế → 8 hàng x 13 ghế (104 ≈ 100)
            // Phòng 80 ghế  → 8 hàng x 10 ghế (80)
            $seatsPerRow = $room->seat_count >= 100 ? 13 : 10;

            // Xóa ghế cũ nếu có
            DB::table('seats')->where('room_id', $room->id)->delete();

            $seats = [];
            foreach ($rows as $row) {
                for ($i = 1; $i <= $seatsPerRow; $i++) {
                    $seats[] = [
                        'room_id' => $room->id,
                        'seat_name' => $row . $i,
                        'seat_type' => 'standard',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            DB::table('seats')->insert($seats);
            $total = count($seats);
            $this->command->info("✅ Room {$room->id} ({$room->name}): đã seed {$total} ghế");
        }
    }
}