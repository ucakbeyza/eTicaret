<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShippingCompany;
use App\Helpers\ResponseBuilder;

class ShippingCompanyController extends Controller
{
    public function index()
    {
        $companies = ShippingCompany::all();
        return ResponseBuilder::success([
            'shipping_companies' => $companies
        ]);
    }
    
}
