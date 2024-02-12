<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ProductsRequests;
use Carbon\Carbon;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with(['user',])->get();
        return response()->json($bills);
    }

    public function show($id)
    {
        $bill = Bill::with(['user',])->findOrFail($id);
        return response()->json($bill);
    }


    public function createBill(Request $request)
    {
        try {
            $products_requests_id = $request->input('products_requests_id');

        
            // Get products request details
            $productsRequest = ProductsRequests::findOrFail($request->products_requests_id);
    
            // Get all items bought with their prices
            $boughtItems = ProductsRequests::where('id', $productsRequest->id)
                ->with('item') // Assuming a relationship between ProductsRequests and Item models
                ->get();
    
            if ($boughtItems->isEmpty()) {
                return response()->json(['message' => 'No bought items found for the user.'], 404);
            }
    
            // Calculate total price
            $totalPrice = $boughtItems->sum(function ($item) {
                return $item->item->price * $item->count;
            });
    
            // Create a new bill
            $bill = Bill::create([
                'user_id' => $productsRequest->user_id,
                'total_price' => $totalPrice,
                'bought_items' => $boughtItems->pluck('item.name')->toArray(),
            ]);
    
            return response()->json(['message' => 'Bill created successfully', 'bill' => $bill]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            return response()->json(['message' => 'No request found'], 404);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'An error occurred while processing the request'], 500);
        }
    }
    
    public function getTotalIncome($month)
    {
        // Validate the month input (optional)
        $this->validateMonth($month);

        // Calculate total income for the specified month
        $totalIncome = Bill::whereMonth('created_at', $month)->sum('total_price');

        return response()->json(['products_total_income' => $totalIncome]);
    }

    // booking total income
    public function bookingTotalIncome($month)
    {
        $bookingsReceived = Booking::where('status', 'receive')->get();

        $filteredData = collect($bookingsReceived)->filter(function ($item) use ($month) {
            $carbonDate = Carbon::parse($item['day']);
            return $carbonDate->month == $month;
        })->all();

        $total_price = 0;
        foreach ($filteredData as $book) {
            $priceItem = $book->item->price;
            $price = intval($priceItem) * intval($book['guests']);
            $total_price += $price;
        }
        return response()->json([
            'services_total_income' => $total_price,
        ]);

        // return $total_price;
    }

    protected function validateMonth($month)
    {
        // Validate that $month is a valid month number (1 to 12)
        if (!is_numeric($month) || $month < 1 || $month > 12) {
            abort(400, 'Invalid month. Please provide a valid month number (1 to 12).');
        }
    }
}
