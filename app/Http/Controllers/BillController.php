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
        // Validate the request data
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Get user details
        $user = User::findOrFail($request->user_id);

        // Get all items bought by the user with their prices
        $boughtItems = ProductsRequests::where('user_id', $user->id)
            ->with('item') // Assuming a relationship between ProductsRequests and Item models
            ->get();

        // If there are no bought items, handle the case accordingly
        if ($boughtItems->isEmpty()) {
            return response()->json(['message' => 'No bought items found for the user.'], 404);
        }

        // Calculate total price
        $totalPrice = $boughtItems->sum(function ($item) {
            return $item->item->price * $item->count;
        });

        // Create a new bill
        $bill = Bill::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'bought_items' => $boughtItems->pluck('item.name')->toArray(),
        ]);

        return response()->json(['message' => 'Bill created successfully', 'bill' => $bill]);
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
