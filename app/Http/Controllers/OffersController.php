<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\User;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return response()->json(['message' => 'Offer sent successfully', 'offer' => $offer], 201);
    }
}
