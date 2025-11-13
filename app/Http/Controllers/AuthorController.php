<?php

namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthorController extends Controller
{
    // Display paginated list of authors
    public function index()
    {
        $authors = Author::paginate(10);

        return Inertia::render('authors/Index', [
            'authors' => $authors,
        ]);
    }

    // Show create form
    public function create()
    {
        return Inertia::render('authors/Create');
    }

    // Store new author
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
        ]);

        Author::create($validated);

        return redirect()->route('authors.index');
    }

    // Show edit form
    public function edit(Author $author)
    {
        return Inertia::render('authors/Edit', [
            'author' => $author,
        ]);
    }

    // Update author
    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
        ]);

        $author->update($validated);

        return redirect()->route('authors.index');
    }

    // Delete author
    public function destroy(Author $author)
    {
        $author->delete();

        return redirect()->route('authors.index');
    }
}
