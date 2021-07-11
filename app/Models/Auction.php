<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'bid_type',
        'reference_no',
        'description',
        'user_id',
        'min_step',
        'start_price',
        'start_time',
        'end_time',
        'duration',
        'tendor_start',
        'tendor_end',
        'winner_bid',
    ];

}
