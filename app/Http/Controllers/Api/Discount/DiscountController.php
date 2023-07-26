<?php

namespace App\Http\Controllers\Api\Discount;

use App\Models\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\DiscountResource;

class DiscountController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:admin'])->except('index', 'show');
    }

    public function index()
    {
        $discounts = Discount::with(['product'])->get();

        return DiscountResource::collection($discounts);
    }

    public function show(Discount $discount) 
    {

        return DiscountResource::collection($discount);

    }
}
