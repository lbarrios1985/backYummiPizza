<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Get Cart
     *
     * @return json
     */
    public function getCart()
    {
        return response()->json([$this->assembleCartData()], 200);
    }

    /**
     * Delete Cart
     *
     * @return json
     */
    public function deleteCart()
    {
        cart()->clear();
        return response()->json(['success' => 'Cart deleted Successfully'], 200);
    }

    /**
     * Increase the item's quantity by 1
     *
     * @param  $item_pos Item position (index) in the array cart()->items()
     * @return json
     */
    public function incrementItem($item_pos)
    {
        try {
            cart()->incrementQuantityAt($item_pos);
            return response()->json([$this->assembleCartData()], 200);
        } catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 409);
        }
    }

    /**
     * Decrease the item's quantity by 1
     *
     * @param  $item_pos Item position (index) in the array cart()->items()
     * @return json
     */
    public function decrementItem($item_pos)
    {
        try {
            cart()->decrementQuantityAt($item_pos);
            return response()->json([$this->assembleCartData()], 200);
        } catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 409);
        }
        //return response()->json(['success' => 'Cart deleted Successfully'], 200);
    }

    /**
     * Delete the item from the cart
     *
     * @param  $item_pos Item position (index) in the array cart()->items()
     * @return json
     */
    public function removeItem($item_pos)
    {
        try {
            cart()->removeAt($item_pos);
            return response()->json([$this->assembleCartData()], 200);
        } catch(\Exception $e){
            return response()->json(['error'=>$e->getMessage()], 409);
        }
    }

    /**
     * Return the Cart's Information (amounts & items)
     *
     * @return $response The Cart's organized Data
     */
    private function assembleCartData() 
    {
        $currency = DB::table('carts')->where('auth_user', auth('api')->user()->id)
                                      ->select('currency')
                                      ->latest()->first();
        $response = [];
        if ($currency) {
            Config::set('cart_manager.currency', $currency->currency);
            $response['totals'] = cart()->totals();
            $response['items'] = cart()->items($displayCurrency = true);
        }
        return $response;
    }
}
