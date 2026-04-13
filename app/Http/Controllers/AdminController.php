<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Str;
class AdminController extends Controller
{
    public function index(Request $request)
    {
        $activeTab = $request->get('tab', 'dashboard');

        // 👉 xử lý form posts
        if ($request->isMethod('post') && $activeTab === 'posts') {

            Post::create([
                'title' => $request->title,
                'content' => $request->content,
                'slug' => Str::slug($request->title),
            ]);

            return redirect()->route('admin.dashboard', [
                'tab' => 'posts'
            ]);
        }

        return view('admin.home', compact('activeTab'));
    }
}