<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    // READ: Menampilkan daftar semua kategori
    public function index(): View
    {
        $categories = Category::withCount('artworks')->latest()->paginate(10); 
        return view('admin.categories.index', compact('categories'));
    }

    // CREATE: Menampilkan formulir tambah
    public function create(): View
    {
        return view('admin.categories.create');
    }

    // STORE: Menyimpan kategori baru
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil ditambahkan.');
    }

    // EDIT: Menampilkan formulir edit
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    // UPDATE: Memperbarui kategori
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$category->id,
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    // DELETE: Menghapus kategori
    public function destroy(Category $category): RedirectResponse
    {
        // Pengecekan: Tidak bisa dihapus jika masih ada karya seni yang terkait
        if ($category->artworks()->exists()) {
            return back()->with('error', 'Gagal: Kategori ini masih memiliki karya seni terkait.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
                         ->with('success', 'Kategori berhasil dihapus.');
    }
}