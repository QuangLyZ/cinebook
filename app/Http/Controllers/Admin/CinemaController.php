<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cinema;
use App\Models\Room;
use Illuminate\Http\Request;

class CinemaController extends Controller
{
    public function index()
    {
        $cinemas = Cinema::withCount('rooms')->latest()->paginate(15);
        return view('admin.cinemas.index', [
            'cinemas'   => $cinemas,
            'activeTab' => 'management',
            'pageTitle' => 'Quản lý Rạp',
        ]);
        $cinemas = Cinema::orderBy('name')->get();
 
        return view('theaters', compact('cinemas'));
    }

    public function create()
    {
        return view('admin.cinemas.create', [
            'activeTab' => 'management',
            'pageTitle' => 'Thêm Rạp Mới',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $cinema = Cinema::create($validated);

        // Create rooms if provided
        if ($request->filled('rooms')) {
            foreach ($request->rooms as $room) {
                if (!empty($room['name'])) {
                    $cinema->rooms()->create([
                        'name'       => $room['name'],
                        'seat_count' => $room['seat_count'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()->route('admin.cinemas.index')
            ->with('success', 'Thêm rạp chiếu thành công!');
    }

    public function show(Cinema $cinema)
    {
        $cinema->load('rooms');
        return view('admin.cinemas.show', [
            'cinema'    => $cinema,
            'activeTab' => 'management',
            'pageTitle' => 'Chi tiết Rạp',
        ]);
    }

    public function edit(Cinema $cinema)
    {
        $cinema->load('rooms');
        return view('admin.cinemas.edit', [
            'cinema'    => $cinema,
            'activeTab' => 'management',
            'pageTitle' => 'Chỉnh Sửa Rạp',
        ]);
    }

    public function update(Request $request, Cinema $cinema)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $cinema->update($validated);

        // Handle room updates
        $existingRoomIds = $cinema->rooms->pluck('id')->toArray();
        $submittedRoomIds = [];

        if ($request->filled('rooms')) {
            foreach ($request->rooms as $room) {
                if (empty($room['name'])) continue;

                if (!empty($room['id'])) {
                    // Update existing room
                    $r = Room::find($room['id']);
                    if ($r && $r->cinema_id === $cinema->id) {
                        $r->update([
                            'name'       => $room['name'],
                            'seat_count' => $room['seat_count'] ?? 0,
                        ]);
                        $submittedRoomIds[] = $r->id;
                    }
                } else {
                    // Create new room
                    $newRoom = $cinema->rooms()->create([
                        'name'       => $room['name'],
                        'seat_count' => $room['seat_count'] ?? 0,
                    ]);
                    $submittedRoomIds[] = $newRoom->id;
                }
            }
        }

        // Delete removed rooms
        $toDelete = array_diff($existingRoomIds, $submittedRoomIds);
        if (!empty($toDelete)) {
            Room::whereIn('id', $toDelete)->delete();
        }

        return redirect()->route('admin.cinemas.index')
            ->with('success', 'Cập nhật rạp chiếu thành công!');
    }

    public function destroy(Cinema $cinema)
    {
        $cinema->delete();
        return redirect()->route('admin.cinemas.index')
            ->with('success', 'Xóa rạp chiếu thành công!');
    }
    
}
