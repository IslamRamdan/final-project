<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsRequests extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'item_id',
        'count',
        'name', 'price'
    ];
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
