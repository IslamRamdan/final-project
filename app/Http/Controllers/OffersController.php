<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\SendRequest;
use Illuminate\Support\Facades\Notification;
use App\Models\Request as ModelsRequest;


class OffersController extends Controller
{
    public function sendCustomOffer(Request $request)
    {
        $purchaseValue = $request->input('purchase_value');
//        $user = User::findOrFail($userId);

        $offerText = $request->input('offer_text');
        // Find all users who meet the purchase value criteria


      
    // Retrieve users with total purchase value greater than or equal to purchaseValue
    $users = User::whereHas('productRequests.item', function ($query) use ($purchaseValue) {
        $query->whereRaw('items.price * products_requests.count >= ?', [$purchaseValue]);
    }, '>=', 1)->get();
    

        // Store offers for all eligible users
        $offers = [];
        foreach ($users as $user) {
            // Calculate total purchase value for the user
            $totalPurchaseValue = $user->productRequests->sum(function ($request) {
                return $request->count * $request->item->price;
            });
        
            // Check if the total purchase value meets the criteria
            if ($totalPurchaseValue >= $purchaseValue) {
                // Store the offer in the database
                $offer = Offer::create([
                    'user_id' => $user->id,
                    'offer_text' => $offerText,
                    'purchase_value'=>$purchaseValue,
                ]);
                $offers[] = $offer;
            }
        }
        
    
    //  //send notification to users
    //  $dataRequest = ModelsRequest::create([
    //     'user_id' => auth()->user()->id, //when integrate we will replace it with auth->user which logged in 
    //     'title' => "NEW OFFER",
    //     'body' => $request->offerText
    // ]);

    // $user_send = auth()->user()->name; //when integrate we will replace it with auth->user->name which logged in 
    //   //here we send a notfication to only users
    // //   $users = User::whereHas('role', function ($query) {
    // //     $query->where('role', 'user');
    // // })->get();
    // Notification::send($users, new SendRequest($dataRequest->id, $user_send, $dataRequest->title));


        return response()->json(['message' => 'Offer sent successfully', 'offer' => $offers], 201);
    }



    public function makeOffer(Request $request){

        $offerText = $request->input('offer_text');
        $itemName=$request->input('item_name');
        $currentPrice = $request->input('current_price');
        $previousPrice = $request->input('previous_price');
        $imageUrl = $request->input('image_url');
        $item = Item::where('name', 'LIKE', $itemName)->first();

        if (!$item) {
            return response()->json(['message' => 'Item not found', 'offer' => null], 404);
        }

        $updatedItem = $item->update([
            'price' => $currentPrice,
        ]);

        if (!$updatedItem) {
            return response()->json(['message' => 'Update failed', 'offer' => null], 500);
        }

        $offer = new Offer([
            'item_id' => $item->id,
            'offer_text' => $offerText,
            'current_price' => $currentPrice,
            'previous_price' => $previousPrice,
            'image_url' => $imageUrl,
            // Add other offer attributes as needed
        ]);

        $offer->save();

            
    //  //send notification to users
    //  $dataRequest = ModelsRequest::create([
    //     'user_id' => auth()->user()->id, //when integrate we will replace it with auth->user which logged in 
    //     'title' => "NEW OFFER",
    //     'body' => $request->offerText
    // ]);

    // $user_send = auth()->user()->name; //when integrate we will replace it with auth->user->name which logged in 
    //   //here we send a notfication to only users
    //   $users = User::whereHas('role', function ($query) {
    //     $query->where('role', 'user');
    // })->get();
    // Notification::send($users, new SendRequest($dataRequest->id, $user_send, $dataRequest->title));



        return response()->json(['message' => 'Offer sent successfully', 'offer' => $offer], 201);
    }
    public function getOffers()
    {
       
        $offers = Offer::whereNull('user_id')->get();
    
        
        if ($offers->isEmpty()) {
            return response()->json(['message' => 'No offers found where user_id is null', 'offers' => []], 404);
        }
    
        return response()->json(['message' => 'Offers with user_id null retrieved successfully', 'offers' => $offers], 200);
    }

    public function getSpecialOffers()
{
    
    $userId = auth()->user()->id;

    $specialOffers = Offer::where('user_id', $userId)->get();

 
    if ($specialOffers->isEmpty()) {
        return response()->json(['message' => 'No special offers found for the logged-in user', 'offers' => []], 404);
    }

   
    return response()->json(['message' => 'Special offers retrieved successfully', 'offers' => $specialOffers], 200);
}


}
