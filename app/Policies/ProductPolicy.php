<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function author(User $user, Product $product)
    {
        // if($user->id == $product->user_id){
        //     return true;
        // }
    }
}