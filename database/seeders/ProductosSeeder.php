<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---------------------------
        // Categorías
        // ---------------------------
        DB::table('categorias')->updateOrInsert(
            ['id' => 1], // condición para buscar
            [
                'nombre_categoria' => 'Camisetas',
                'descripcion_categoria' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // ---------------------------
        // Marcas
        // ---------------------------
        DB::table('marcas')->updateOrInsert(
            ['id' => 1],
            [
                'nombre_marca' => 'Nike',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('marcas')->updateOrInsert(
            ['id' => 2],
            [
                'nombre_marca' => 'Calvin Klein',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // ---------------------------
        // Productos
        // ---------------------------
        $productos = [
            ['id' => 1, 'categoria_id' => 1, 'marca_id' => 1, 'nombre' => 'Camiseta Oversize Negra', 'precio' => 29.99, 'talla' => 'M', 'imagen' => 'oversize_negra.jpg', 'stock' => 20],
            ['id' => 2, 'categoria_id' => 1, 'marca_id' => 1, 'nombre' => 'Sudadera Blanca', 'precio' => 49.99, 'talla' => 'L', 'imagen' => 'sudadera_blanca.jpg', 'stock' => 12],
            ['id' => 3, 'categoria_id' => 1, 'marca_id' => 1, 'nombre' => 'Pantalón Cargo Beige', 'precio' => 59.99, 'talla' => 'M', 'imagen' => 'pantalon_cargo_beige.jpg', 'stock' => 8],
            ['id' => 4, 'categoria_id' => 1, 'marca_id' => 1, 'nombre' => 'Camisa blanca', 'precio' => 15.99, 'talla' => 'M', 'imagen' => 'camisa_blanca.jpg', 'stock' => 50],
            ['id' => 6, 'categoria_id' => 1, 'marca_id' => 2, 'nombre' => 'Camisa Roja', 'precio' => 19.99, 'talla' => 'M', 'imagen' => 'camisa_muy_roja.png', 'stock' => 30],
            ['id' => 7, 'categoria_id' => 1, 'marca_id' => 1, 'nombre' => 'Camisa Azul', 'precio' => 9.99, 'talla' => 'M', 'imagen' => 'camisa_azul.png', 'stock' => 30],
            ['id' => 8, 'categoria_id' => 1, 'marca_id' => 2, 'nombre' => 'Camisa Verde', 'precio' => 29.99, 'talla' => 'M', 'imagen' => 'camisa_verde.jpg', 'stock' => 30],
            ['id' => 9, 'categoria_id' => 1, 'marca_id' => 1, 'nombre' => 'Camisa Negra', 'precio' => 19.99, 'talla' => 'M', 'imagen' => 'camisa_negra.png', 'stock' => 30],
            ['id' => 10, 'categoria_id' => 1, 'marca_id' => 1, 'nombre' => 'Camisa Beige', 'precio' => 5.99, 'talla' => 'M', 'imagen' => 'camisa_beige.png', 'stock' => 30],
        ];

        foreach ($productos as $p) {
            DB::table('productos')->updateOrInsert(
                ['id' => $p['id']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        // ---------------------------
        // Imágenes
        // ---------------------------
        $imagenes = [
            ['id' => 1, 'nombre' => '1773441006_camisa.jpg', 'producto_id' => 4],
            ['id' => 2, 'nombre' => '1773445633_camisa-muy-roja.png', 'producto_id' => 6],
            ['id' => 3, 'nombre' => '1773445774_camisa-super-roja.png', 'producto_id' => 6],
            ['id' => 4, 'nombre' => '1773445950_camisa-azul.png', 'producto_id' => 7],
            ['id' => 5, 'nombre' => '1773446194_camisa-verde.jpg', 'producto_id' => 8],
            ['id' => 6, 'nombre' => '1773446447_camisa-verde.jpg', 'producto_id' => 8],
            ['id' => 7, 'nombre' => '1773446485_camisa-verde.jpg', 'producto_id' => 8],
            ['id' => 8, 'nombre' => '1773446774_camisa-negra.png', 'producto_id' => 9],
            ['id' => 9, 'nombre' => '1773446842_camisa-beige.png', 'producto_id' => 10],
        ];

        foreach ($imagenes as $img) {
            DB::table('imagenes')->updateOrInsert(
                ['id' => $img['id']],
                array_merge($img, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
