<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{

    private function getContact(User $user, $idContact)
    {
        $contact = Contact::where('user_id', $user->id)->where('id', $idContact)->first();
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["Address not found!"]
                ]
            ])->setStatusCode(404));
        }
        return $contact;
    }

    private function getAddressFunction(Contact $contact, $idAddress)
    {
        $address =  Address::where('contact_id', $contact->id)->where('id', $idAddress)->first();
        if (!$address) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["Address not found!"]
                ]
            ])->setStatusCode(404));
        }
        return $address;
    }

    public function create(AdressRequest $request, $idContact): JsonResponse
    {
        $user = Auth::user();
        $data = $request->validated();
        $contact = $this->getContact($user, $idContact);

        $address = new Address($data);
        $address->contact_id = $contact->id;
        $address->save();

        return (new AddressResource($address))->response()->setStatusCode(201);
    }

    public function getAddress($idContact, $idAddress): AddressResource
    {

        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddressFunction($contact, $idAddress);

        return new AddressResource($address);
    }

    public function update($idContact, $idAddress, UpdateAddressRequest $request): AddressResource
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddressFunction($contact, $idAddress);
        $data = $request->validated();
        $address->fill($data);
        $address->save();

        return new AddressResource($address);
    }
    public function delete($idContact, $idAddress): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $address = $this->getAddressFunction($contact, $idAddress);

        $address->delete();

        return  response()->json([
            "data" => true
        ], 200);
    }

    public function getAllAddress($idContact): JsonResponse
    {
        $user = Auth::user();
        $contact = $this->getContact($user, $idContact);
        $addresses = Address::where('contact_id', $contact->id)->get();

        return (AddressResource::collection($addresses))->response()->setStatusCode(200);
    }
}
