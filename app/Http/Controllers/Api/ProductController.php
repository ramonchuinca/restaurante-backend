<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Listar produtos com filtros e paginação
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->orderBy('name');

        // 🔎 Filtrar por nome
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // 🏷️ Filtrar por categoria
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // ✅ Filtrar por status (0 ou 1)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(10);

        return ProductResource::collection($products);
    }

    /**
     * Criar produto
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload de imagem
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        Log::info('Produto criado', [
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return new ProductResource($product);
    }

    /**
     * Atualizar produto
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'status' => 'boolean',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Substituir imagem antiga
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        Log::info('Produto atualizado', [
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return new ProductResource($product);
    }

    /**
     * Remover produto (soft delete)
     */
    public function destroy(Product $product)
    {
        $product->delete();

        Log::info('Produto removido', [
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return response()->json([
            'message' => 'Produto removido com sucesso',
        ]);
    }

    /**
     * Mostrar produto específico
     */
    public function show(Product $product)
    {
        return new ProductResource($product->load('category'));
    }
}