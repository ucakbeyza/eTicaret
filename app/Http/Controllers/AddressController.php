<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Models\Address;
use Illuminate\Http\Request;
use App\Http\Requests\CreateAddressRequest;
use App\Http\Requests\DeleteAddressRequest;
use App\Http\Requests\UpdateAddressRequest;

class AddressController extends Controller
{
    public function create(CreateAddressRequest $request)
    {
        $address = Address::create([
            'user_id' => $request->user()->id,
            'province' => $request->province,
            'district' => $request->district,
            'address' => $request->address,
        ]);
        return ResponseBuilder::success($address);
    }
    public function update(UpdateAddressRequest $request){
    
        
        $address = Address::where('id',$request->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();
        
        $address->update([
            'province' => $request->province,
            'district' => $request->district,
            'address' => $request->address,
        ]);
        return ResponseBuilder::success($address);
    }
    public function delete(DeleteAddressRequest $request){
        
        $address = Address::where('id',$request->id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $address->delete();

        return ResponseBuilder::success(null);
    }
    public function list(Request $request){
        $addresses = Address::where('user_id', $request->user()->id)->get();
        return ResponseBuilder::success($addresses);
    }

}
