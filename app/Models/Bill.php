<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = ['user_id', 'total_price', 'bought_items'];
    protected $casts = [
        'bought_items' => 'array',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
