<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        Item::insert([
            [
                'name' => 'Socket',
                'available' => 20,
                'image' => 'PRODUCT_1710320476259.jpeg',
                'qty' => 30,
                'status' => 1, // 1=Active, 0=Inactive (adjust to your DB)
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'name' => 'Notebook A5',
            //     'category' => 'Stationery',
            //     'description' => '80 pages ruled notebook.',
            //     'image' => 'https://media.makrocambodiaclick.com/PRODUCT_1710320476259.jpeg',
            //     'price' => 2.25,
            //     'qty' => 200,
            //     'status' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'name' => 'Whiteboard Marker',
            //     'category' => 'Stationery',
            //     'description' => 'Low-odor marker (black).',
            //     'image' => 'https://media.makrocambodiaclick.com/PRODUCT_1710320476259.jpeg',
            //     'price' => 1.50,
            //     'qty' => 120,
            //     'status' => 0,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'name' => 'Geometry Set',
            //     'category' => 'Stationery',
            //     'description' => 'Ruler + compass + protractor.',
            //     'image' => 'https://media.makrocambodiaclick.com/PRODUCT_1710320476259.jpeg',   
            //     'price' => 4.75,
            //     'qty' => 60,
            //     'status' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ]);
    }
}