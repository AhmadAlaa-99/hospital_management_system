<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::latest()->paginate(15);
        return view('Dashboard.Blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('Dashboard.Blogs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'author' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        $data['slug'] = Blog::makeSlug($data['title']);
        $data['author'] = $data['author'] ?? auth()->user()->name ?? 'المستشفى';
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? now() : null;

        if ($request->hasFile('image')) {
            $data['image'] = 'storage/' . $request->file('image')->store('blogs', 'public');
        }

        Blog::create($data);
        session()->flash('add');
        return redirect()->route('admin.blogs.index');
    }

    public function edit(Blog $blog)
    {
        return view('Dashboard.Blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string',
            'author' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        if ($blog->title !== $data['title']) {
            $data['slug'] = Blog::makeSlug($data['title']);
        }

        $data['is_published'] = $request->boolean('is_published');
        if ($data['is_published'] && !$blog->published_at) {
            $data['published_at'] = now();
        }

        if ($request->hasFile('image')) {
            $data['image'] = 'storage/' . $request->file('image')->store('blogs', 'public');
        }

        $blog->update($data);
        session()->flash('edit');
        return redirect()->route('admin.blogs.index');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        session()->flash('delete');
        return redirect()->route('admin.blogs.index');
    }
}
