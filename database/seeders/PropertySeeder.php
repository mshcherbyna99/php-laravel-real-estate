<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = storage_path('property-data.csv');
        $file = fopen($csvFile, 'rb');

        while (($row = fgetcsv($file)) !== false) {
            Property::create([
                'name' => $row[0],
                'price' => (float)$row[1],
                'bedrooms' => (int)$row[2],
                'bathrooms' => (int)$row[3],
                'storeys' => (int)$row[4],
                'garages' => (int)$row[5],
            ]);
        }

        fclose($file);
    }
}
