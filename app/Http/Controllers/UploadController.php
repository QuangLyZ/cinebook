<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        if (!$request->hasFile('upload')) {
            return response()->json([
                'error' => ['message' => 'No file uploaded']
            ], 400);
        }

        $file = $request->file('upload');

        // DEBUG: xem có nhận file không
        if (!$file->isValid()) {
            return response()->json([
                'error' => ['message' => 'File not valid']
            ], 400);
        }

        $filename = time() . '_' . $file->getClientOriginalName();

        // Lưu vào public/uploads
        $file->move(public_path('uploads'), $filename);

        return response()->json([
            'url' => asset('uploads/' . $filename)
        ]);
    }
}