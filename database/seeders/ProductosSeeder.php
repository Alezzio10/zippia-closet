<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductosSeeder extends Seeder
{
    public function run(): void
    {

        /*
        |--------------------------------------------------------------------------
        | CATEGORIAS
        |--------------------------------------------------------------------------
        */

        DB::table('categorias')->updateOrInsert(
            ['id' => 1],
            [
                'nombre_categoria' => 'Camisetas',
                'descripcion_categoria' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('categorias')->updateOrInsert(
            ['id' => 2],
            [
                'nombre_categoria' => 'Pantalones',
                'descripcion_categoria' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('categorias')->updateOrInsert(
            ['id' => 3],
            [
                'nombre_categoria' => 'Vestidos',
                'descripcion_categoria' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | MARCAS
        |--------------------------------------------------------------------------
        */

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


        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS
        |--------------------------------------------------------------------------
        */

        $productos = [

            ['id'=>1,'categoria_id'=>1,'marca_id'=>1,'nombre'=>'Camiseta Oversize Negra','precio'=>29.99,'talla'=>'M','imagen'=>null,'stock'=>20],

            ['id'=>2,'categoria_id'=>1,'marca_id'=>1,'nombre'=>'Sudadera Blanca','precio'=>49.99,'talla'=>'L','imagen'=>null,'stock'=>12],

            ['id'=>3,'categoria_id'=>1,'marca_id'=>1,'nombre'=>'Pantalón Cargo Beige','precio'=>59.99,'talla'=>'M','imagen'=>null,'stock'=>8],

            ['id'=>4,'categoria_id'=>1,'marca_id'=>1,'nombre'=>'Camisa blanca','precio'=>15.99,'talla'=>'M','imagen'=>null,'stock'=>50],

            ['id'=>6,'categoria_id'=>1,'marca_id'=>2,'nombre'=>'Camisa Roja','precio'=>19.99,'talla'=>'M','imagen'=>null,'stock'=>30],

            ['id'=>7,'categoria_id'=>1,'marca_id'=>1,'nombre'=>'Camisa Azul','precio'=>9.99,'talla'=>'M','imagen'=>null,'stock'=>30],

            ['id'=>8,'categoria_id'=>1,'marca_id'=>2,'nombre'=>'Camisa Verde','precio'=>29.99,'talla'=>'M','imagen'=>null,'stock'=>30],

            ['id'=>9,'categoria_id'=>1,'marca_id'=>1,'nombre'=>'Camisa Negra','precio'=>19.99,'talla'=>'M','imagen'=>null,'stock'=>30],

            ['id'=>10,'categoria_id'=>1,'marca_id'=>1,'nombre'=>'Camisa Beige','precio'=>5.99,'talla'=>'M','imagen'=>null,'stock'=>30],

            ['id'=>11,'categoria_id'=>2,'marca_id'=>1,'nombre'=>'Pantalon azul','precio'=>19.99,'talla'=>'M','imagen'=>null,'stock'=>30],

            ['id'=>12,'categoria_id'=>2,'marca_id'=>1,'nombre'=>'Pantalon negro','precio'=>39.99,'talla'=>'M','imagen'=>null,'stock'=>30],

            ['id'=>13,'categoria_id'=>2,'marca_id'=>2,'nombre'=>'Pantalon vaquero verde','precio'=>29.99,'talla'=>'M','imagen'=>null,'stock'=>30],

            ['id'=>14,'categoria_id'=>3,'marca_id'=>2,'nombre'=>'Vestido azul','precio'=>29.99,'talla'=>'M','imagen'=>null,'stock'=>30],

            ['id'=>15,'categoria_id'=>3,'marca_id'=>2,'nombre'=>'Vestido negro','precio'=>29.99,'talla'=>'M','imagen'=>null,'stock'=>30],
        ];

        foreach ($productos as $p) {

            DB::table('productos')->updateOrInsert(
                ['id' => $p['id']],
                array_merge($p,[
                    'created_at'=>now(),
                    'updated_at'=>now()
                ])
            );

        }


        /*
        |--------------------------------------------------------------------------
        | IMAGENES
        |--------------------------------------------------------------------------
        */

        $imagenes = [

            ['id'=>1,'nombre'=>'1773441006_camisa.jpg','producto_id'=>4],
            ['id'=>2,'nombre'=>'1773445633_camisa-muy-roja.png','producto_id'=>5],
            ['id'=>3,'nombre'=>'1773445774_camisa-super-roja.png','producto_id'=>6],
            ['id'=>4,'nombre'=>'1773445950_camisa-azul.png','producto_id'=>7],
            ['id'=>5,'nombre'=>'1773446194_camisa-verde.jpg','producto_id'=>8],
            ['id'=>6,'nombre'=>'1773446447_camisa-verde.jpg','producto_id'=>8],
            ['id'=>7,'nombre'=>'1773446485_camisa-verde.jpg','producto_id'=>8],
            ['id'=>8,'nombre'=>'1773446774_camisa-negra.png','producto_id'=>9],
            ['id'=>9,'nombre'=>'1773446842_camisa-beige.png','producto_id'=>10],
            ['id'=>10,'nombre'=>'1773535736_pantalon-azul.png','producto_id'=>11],
            ['id'=>11,'nombre'=>'1773803398_pantalon-negro.png','producto_id'=>12],
            ['id'=>12,'nombre'=>'1773803613_pantalon-vaquero-verde.png','producto_id'=>13],
            ['id'=>13,'nombre'=>'1773804161_vestido-azul.png','producto_id'=>14],
            ['id'=>14,'nombre'=>'1773804283_vestido-negro.png','producto_id'=>15],

        ];

        foreach ($imagenes as $img) {

            DB::table('imagenes')->updateOrInsert(
                ['id'=>$img['id']],
                array_merge($img,[
                    'created_at'=>now(),
                    'updated_at'=>now()
                ])
            );

        }

    }
}