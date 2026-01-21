<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Topic;
use App\Models\Post;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('topics')->get();
        return view('forum.categories.index', compact('categories'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return redirect()->route('categories.index')->with('error', 'Please enter at least 2 characters to search.');
        }

        // Search in topics (title only - topics don't have content, posts do)
        $topics = Topic::where('title', 'like', '%' . $query . '%')
            ->with(['user', 'category'])
            ->latest()
            ->get();

        // Search in posts (content)
        $posts = Post::where('content', 'like', '%' . $query . '%')
            ->with(['user', 'topic.category'])
            ->latest()
            ->get();

        // Search in categories
        $categories = Category::where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->withCount('topics')
            ->get();

        return view('forum.search', compact('query', 'topics', 'posts', 'categories'));
    }

    public function show(Category $category)
    {
        $topics = $category->topics()->with('user')->latest()->paginate(15);
        return view('forum.categories.show', compact('category', 'topics'));
    }

    // Admin: Create category
    public function create()
    {
        return view('forum.categories.create');
    }

    // Admin: Store category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:1000',
        ]);

        Category::create($request->only('name', 'description'));

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    // Admin: Edit category
    public function edit(Category $category)
    {
        return view('forum.categories.edit', compact('category'));
    }

    // Admin: Update category
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $category->update($request->only('name', 'description'));

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    // Admin: Delete category
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
