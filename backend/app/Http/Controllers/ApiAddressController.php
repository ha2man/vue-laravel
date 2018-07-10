<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Address;
use App\Http\Resources\AddressResource;
use App\Http\Requests\AddressStoreRequest;
use App\Http\Requests\AddressUpdateRequest;
use Illuminate\Support\Facades\Auth;

class ApiAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('address_list'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AddressResource(Auth::user()->addresses);
    }

    public function setAsPrimary(Address $address) {
        abort_if(Gate::denies('address_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        Address::where('user_id', Auth::id())
            ->update(['is_primary' => false]);
        $address->is_primary = true;
        $address->save();

        return (new AddressResource($address))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AddressStoreRequest $request)
    {
        $count = Address::where('user_id', Auth::id())->count();
        $address = $request->all();
        $address['is_primary'] = $count > 0 ? false : true;
        $address['user_id'] = Auth::id();
        $address = Address::create($address);

        return (new AddressResource($address))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Address $address)
    {
        abort_if(Gate::denies('address_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AddressResource($address);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AddressUpdateRequest $request, Address $address)
    {
        $address->update($request->all());

        return (new AddressResource($address))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Address $address)
    {
        abort_if(Gate::denies('address_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $count = Address::where('user_id', Auth::id())->count();
        if ($count <= 1) return response([
            'message' => 'There must be at least one address.'
        ], Response::HTTP_FORBIDDEN);

        if ($address->is_primary) {
            $primary = Address::where('user_id', Auth::id())->first();
            $primary->is_primary = true;
            $primary->save();
        }
        $address->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
