<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShippingExtra;

class ShippingExtraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $extras = [
            ['shipping_company_id' => 1, 'city_id' => 1, 'extra_price' => 5.00],
            ['shipping_company_id' => 1, 'city_id' => 2, 'extra_price' => 7.50],
            ['shipping_company_id' => 2, 'city_id' => 1, 'extra_price' => 3.00],
            ['shipping_company_id' => 2, 'city_id' => 3, 'extra_price' => 4.00],
            ['shipping_company_id' => 3, 'city_id' => 2, 'extra_price' => 2.50],
            ['shipping_company_id' => 3, 'city_id' => 3, 'extra_price' => 6.00],
            ['shipping_company_id' => 4, 'city_id' => 1, 'extra_price' => 1.00],
            ['shipping_company_id' => 4, 'city_id' => 4, 'extra_price' => 8.00],

        ];

        foreach ($extras as $extra) {
            if (!ShippingExtra::where('shipping_company_id', $extra['shipping_company_id'])
                ->where('city_id', $extra['city_id'])
                ->exists()) {

             logger('Creating shipping extra: ' . json_encode($extra));
                ShippingExtra::create($extra);
            }
        }
    }
}