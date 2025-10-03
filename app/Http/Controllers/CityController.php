<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\City;
use App\Helpers\ResponseBuilder;


class CityController extends Controller
{
    public function index()
    {
        $cities = City::all();
        return ResponseBuilder::success([
            'cities' => $cities
        ]);
    }
}
