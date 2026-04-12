<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::latest()->paginate(10);
        return view('admin.movies.index', [
            'movies' => $movies,
            'activeTab' => 'management',
            'pageTitle' => 'Quản lý Phim'
        ]);
    }

    public function create()
    {
        return view('admin.movies.create', [
            'activeTab' => 'management',
            'pageTitle' => 'Thêm Phim Mới'
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'release_date' => 'nullable|date',
            'director' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048',
            'actors' => 'nullable|string',
            'age_limit' => 'nullable|integer',
            'trailer_link' => 'nullable|url'
        ]);

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('posters', 'public');
            $validated['poster'] = '/storage/' . $path;
        }

        Movie::create($validated);
        return redirect()->route('admin.movies.index')->with('success', 'Thêm phim thành công!');
    }

    public function edit(Movie $movie)
    {
        return view('admin.movies.edit', [
            'movie' => $movie,
            'activeTab' => 'management',
            'pageTitle' => 'Chỉnh Sửa Phim'
        ]);
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'genre' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'release_date' => 'nullable|date',
            'director' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'poster' => 'nullable|image|max:2048',
            'actors' => 'nullable|string',
            'age_limit' => 'nullable|integer',
            'trailer_link' => 'nullable|url'
        ]);

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('posters', 'public');
            $validated['poster'] = '/storage/' . $path;
        }

        $movie->update($validated);
        return redirect()->route('admin.movies.index')->with('success', 'Cập nhật phim thành công!');
    }

    public function destroy(Movie $movie)
    {
        $movie->delete();
        return redirect()->route('admin.movies.index')->with('success', 'Xóa phim thành công!');
    }
}
