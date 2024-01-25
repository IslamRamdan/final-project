<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ProductsRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsRequestsController extends Controller
{
    //All Requests
    public function requests()
    {
        $req = ProductsRequests::all();
        return $req;
    }

    //get Request by id
    public function getReqById($id)
    {
        $req = ProductsRequests::find($id);
        if (!$req) {
            return response()->json([
                'message' => 'request not found',
            ], 400);
        }
        return $req;
    }

    //Add Request
    public function addRequset(Request $request)
    {
        $request->validate([
            'item_id' => 'required',
            'count' => 'required',
        ]);

        $user_id = auth()->user()->id;
        $item = Item::find($request->item_id);
        if (!$item) {
            return response()->json([
                'message' => 'item not found',
            ], 400);
        }

        $newReq = new ProductsRequests();

        $newReq->item_id = $request->item_id;
        $newReq->user_id = $user_id;
        $newReq->count = $request->count;

        $newReq->save();
        return response()->json([
            'message' => 'request added successfully'
        ]);
    }

    //update Request
    public function updateRequset(Request $request, $id)
    {
        $request->validate([
            'count' => 'required',
        ]);

        $oldRequest = ProductsRequests::find($id);
        $user_id = auth()->user()->id;

        if ($oldRequest->user_id != $user_id) {
            return response()->json([
                'message' => 'You do not have editing permissions',
            ], 400);
        }

        if (!$oldRequest) {
            return response()->json([
                'message' => 'request not found',
            ], 400);
        }

        $oldRequest->count = $request->count;

        $oldRequest->save();
        return response()->json([
            'message' => 'request updated successfully'
        ]);
    }

    //delet Request
    public function deleteRequset($id)
    {
        $request = ProductsRequests::find($id);
        $user_id = auth()->user()->id;

        if ($request->user_id != $user_id) {
            return response()->json([
                'message' => 'You do not have editing permissions',
            ], 400);
        }

        if (!$request) {
            return response()->json([
                'message' => 'request not found',
            ], 400);
        }

        $request->delete();
        return response()->json([
            'message' => 'request deleted successfully'
        ]);
    }
}
