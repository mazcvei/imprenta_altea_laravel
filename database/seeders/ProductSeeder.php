<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPriceUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
   public function run(): void
    {
        // =========================
        // TARJETAS DE VISITA
        // =========================
        $tarjetas = Product::create([
            'name' => 'TARJETAS DE VISITA',
            'description' => 'Impresas a color 2 caras en cartulina couche de 350 grs.',
            'image'=>'products/6a15da6e981f6_tarjetas.png',
        ]);

        ProductPriceUnit::insert([
            [
                'product_id' => $tarjetas->id,
                'price' => 48.00,
                'units' => 250,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $tarjetas->id,
                'price' => 60.00,
                'units' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $tarjetas->id,
                'price' => 90.00,
                'units' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $tarjetas->id,
                'price' => 160.00,
                'units' => 2500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // =========================
        // SOBRES AMERICANO
        // =========================
        $sobres = Product::create([
            'name' => 'SOBRES AMERICANO',
            'description' => 'Impresión 1 tinta 1 cara. Medida 115x225 mm. Blanco 90 grs. Tira silicona para cierre.',
              'image'=>'products/6a15da7da9bc8_sobres.png',
        ]);

        ProductPriceUnit::insert([
            [
                'product_id' => $sobres->id,
                'price' => 75.00,
                'units' => 250,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $sobres->id,
                'price' => 90.00,
                'units' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $sobres->id,
                'price' => 120.00,
                'units' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $sobres->id,
                'price' => 245.00,
                'units' => 2500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // =========================
        // CARPETAS
        // =========================
        $carpetas = Product::create([
            'name' => 'CARPETAS',
            'description' => 'Impresión a color 1 cara. Medida 310x440 mm abierta. Cartulina couche de 350 grs. Hendido central.',
                'image'=>'products/6a15da8d26016_carpetas.png',
        ]);

        ProductPriceUnit::insert([
            [
                'product_id' => $carpetas->id,
                'price' => 95.00,
                'units' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $carpetas->id,
                'price' => 165.00,
                'units' => 250,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $carpetas->id,
                'price' => 345.00,
                'units' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // =========================
        // FOLIOS
        // =========================
        $folios = Product::create([
            'name' => 'FOLIOS',
            'description' => 'Impresión a color 1 cara. Medida 210x297 mm. Papel offset 90grs.',
            'image'=>'products/6a15dad4ace49_folios.png',
        ]);

        ProductPriceUnit::insert([
            [
                'product_id' => $folios->id,
                'price' => 120.00,
                'units' => 250,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $folios->id,
                'price' => 150.00,
                'units' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $folios->id,
                'price' => 190.00,
                'units' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // =========================
        // FLYERS
        // =========================
        $flyers = Product::create([
            'name' => 'FLYERS',
            'description' => 'Impresión a color 2 caras. Medida 150x210mm. Papel couche de 150 grs.',
            'image'=>'products/6a15daf08a1ea_flayers.png',
        ]);

        ProductPriceUnit::insert([
            [
                'product_id' => $flyers->id,
                'price' => 85.00,
                'units' => 500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $flyers->id,
                'price' => 135.00,
                'units' => 1000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $flyers->id,
                'price' => 195.00,
                'units' => 2500,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'product_id' => $flyers->id,
                'price' => 330.00,
                'units' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
