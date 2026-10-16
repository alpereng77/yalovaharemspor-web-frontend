<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaArticle;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index()
    {
        $articles = MediaArticle::orderBy('order', 'asc')->paginate(20);
        return view('admin.media.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.media.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:blog,press',
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:200',
            'publish_date' => 'nullable|date',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'source_title' => 'nullable|string|max:100',
            'source_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $data['is_active'] = $request->has('is_active');
        $data['order'] = $data['order'] ?? (MediaArticle::max('order') + 1);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'media_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $data['image_url'] = 'assets/images/' . $filename;
        }

        MediaArticle::create($data);

        return redirect()->route('admin.media.index')->with('success', 'Yazı / Haber başarıyla eklendi.');
    }

    public function edit(MediaArticle $medium)
    {
        return view('admin.media.edit', ['article' => $medium]);
    }

    public function update(Request $request, MediaArticle $medium)
    {
        $data = $request->validate([
            'type' => 'required|in:blog,press',
            'category' => 'required|string|max:100',
            'title' => 'required|string|max:200',
            'publish_date' => 'nullable|date',
            'summary' => 'nullable|string',
            'content' => 'nullable|string',
            'source_title' => 'nullable|string|max:100',
            'source_url' => 'nullable|url|max:255',
            'order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'media_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/images'), $filename);
            $data['image_url'] = 'assets/images/' . $filename;
        }

        $medium->update($data);

        return redirect()->route('admin.media.index')->with('success', 'Yazı / Haber güncellendi.');
    }

    public function destroy(MediaArticle $medium)
    {
        $medium->delete();
        return redirect()->route('admin.media.index')->with('success', 'İçerik silindi.');
    }
}
