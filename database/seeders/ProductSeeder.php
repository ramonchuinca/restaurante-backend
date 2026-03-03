<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $lanches = Category::where('name', 'Lanches')->first();
        $pratos = Category::where('name', 'Pratos')->first();
        $refrigerantes = Category::where('name', 'Refrigerantes')->first();
        $sucos = Category::where('name', 'Sucos')->first();

        // 🍔 Lanches
        Product::create([
            'category_id' => $lanches->id,
            'name' => 'X-Burger',
            'description' => 'Pão, hambúrguer artesanal, queijo e molho especial',
            'price' => 18.90,
            'status' => true,
        ]);

        Product::create([
            'category_id' => $lanches->id,
            'name' => 'X-Bacon',
            'description' => 'Pão, hambúrguer, bacon crocante e queijo',
            'price' => 22.90,
            'status' => true,
        ]);

        // 🍽️ Pratos
        Product::create([
            'category_id' => $pratos->id,
            'name' => 'Prato Feito',
            'description' => 'Arroz, feijão, bife acebolado e salada',
            'price' => 29.90,
            'status' => true,
        ]);

        // 🥤 Refrigerantes
        Product::create([
            'category_id' => $refrigerantes->id,
            'name' => 'Coca-Cola',
            'description' => 'Lata 350ml',
            'price' => 6.50,
            'status' => true,
        ]);

        // 🧃 Sucos
        Product::create([
            'category_id' => $sucos->id,
            'name' => 'Suco de Laranja',
            'description' => 'Natural 500ml',
            'price' => 7.90,
            'status' => true,
        ]);
    }
}