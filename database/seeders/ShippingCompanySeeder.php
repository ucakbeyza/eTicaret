<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShippingCompany;

class ShippingCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        if (!ShippingCompany::where('name', 'X Shipping')->exists()) {
            ShippingCompany::create(['name' => 'X Shipping', 'base_price' => 15.00]);
        }
        if (!ShippingCompany::where('name', 'Y Shipping')->exists()) {
            ShippingCompany::create(['name' => 'Y Shipping', 'base_price' => 12.50]);
        }
        if (!ShippingCompany::where('name', 'Z Shipping')->exists()) {
            ShippingCompany::create(['name' => 'Z Shipping', 'base_price' => 10.00]);
        }
        if (!ShippingCompany::where('name', 'W Shipping')->exists()) {
            ShippingCompany::create(['name' => 'W Shipping', 'base_price' => 8.00]);
        }
    }
}
