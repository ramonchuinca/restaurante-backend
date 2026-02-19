<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    // Listar categorias paginadas
    public function index()
    {
        $categories = Category::orderBy('name')->paginate(10);
        return response()->json($categories);
    }

    // Criar categoria
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'status' => 'boolean'
        ]);

        $category = Category::create($validated);

        Log::info('Categoria criada', [
            'user_id' => auth()->id(),
            'category_id' => $category->id,
            'name' => $category->name,
        ]);

        return response()->json($category, 201);
    }

    // Atualizar categoria
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'status' => 'boolean'
        ]);

        $category->update($validated);

        Log::info('Categoria atualizada', [
            'user_id' => auth()->id(),
            'category_id' => $category->id
        ]);

        return response()->json($category);
    }

    // Excluir categoria (soft delete)
    public function destroy(Category $category)
    {
        $category->delete();

        Log::info('Categoria removida', [
            'user_id' => auth()->id(),
            'category_id' => $category->id
        ]);

        return response()->json([
            'message' => 'Categoria removida com sucesso'
        ]);
    }

    // Mostrar categoria específica
    public function show(Category $category)
    {
        return response()->json($category);
    }
}