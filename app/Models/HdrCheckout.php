<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HdrCheckout extends Model
{
    use HasFactory;

    protected $table = 'hdr_checkout';

    protected $guarded = [];
}
