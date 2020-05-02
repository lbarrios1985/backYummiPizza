<?php

namespace App\Http\Controllers;

use App\Pizza;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class PizzaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginate = request()->get('paginate');
        if ($paginate == null) {
            $paginate = 10;
        }

        if (request()->has('dirDesc')) {
            if (request()->get('dirDesc') === 'true') {
                $dir = 'desc';
            } else {
                $dir = 'asc';
            }
        } else {
            $dir = 'desc';
        }

        $by = 'updated_at';
        if (request()->has('orderBy')) {
            $by = request()->get('orderBy');
        }

        $pizzas = Pizza::orderBy($by, $dir)->paginate($paginate);

        if ($pizzas->total() > 0) {
            return response()->json(['success' => $pizzas], 200);
        } else {
            return response()->json(['error' => 'No Pizzas found',], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response()->json(['error' => 'Not authorized'], 403);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pizza  $pizza
     * @return \Illuminate\Http\Response
     */
    public function show(Pizza $pizza)
    {
        return response()->json(['success' => $pizza], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pizza  $pizza
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pizza $pizza)
    {
        return response()->json(['error' => 'Not authorized'], 403);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Pizza  $pizza
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pizza $pizza)
    {
        // $pizza->delete();
        // return response()->json(['success' => 'Deleted Successfully'], 200);
        return response()->json(['error' => 'Not authorized'], 403);
    }

    /**
     * Manage Pizza orders.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pizza  $pizza
     * @return \Illuminate\Http\Response
     */
    public function order(Request $request, Pizza $pizza)
    {
        // Keep track of original pizza price
        $original_price = $pizza->price;
        // Base exchange rate value 1 to 1 (USD to USD)
        $exchange_value = 1;
        // Parameter present in the request means another currency has been selected
        if ($request->has('currency')) {
            $currency = mb_strtoupper($request['currency'], 'UTF-8');
            // Try to get the convertion rate between the given currency & USD
            try {
                $client = new Client();
                // Free API
                $exchange = $client->request(
                    'GET',
                    // env('EXCHANGE_RATES_URL')
                    'https://api.exchangeratesapi.io/latest?base=USD&symbols='.$currency
                );
                // API response to array
                $response = json_decode($exchange->getBody()->getContents(), True);
                // Keep the exchange value
                $exchange_value = $response['rates'][$currency];
                // Convert the pizza price to the selected currency
                $pizza->price = $pizza->price * $exchange_value;
                // Set the currency in the cart config
                Config::set('cart_manager.currency', $currency);
            } catch(\Exception $e){
                // If any error, throw it
                return response()->json(['error'=>$e->getMessage()], 409);
            }
        }

        $qty = 0;
        if ($request->has('qty')) {
            // The item quantity has been selected
            $qty = $request->qty;
        }
        $qty == 0 ? $qty = 1 : '';

        if (isset($currency)) {
            // When the currency has changed, store the item price temporarily
            $pizza->update();
        }

        if (cart()->isEmpty()) {
            // User hasn't a created cart, create a new one from theselected cartable model item
            $cart = Pizza::addToCart($pizza->id, $qty);
        } else {
            // A cart already exist to the User, add the selected item
            // If the item alreay exist nothing will change, except there's a difference on the qty
            cart()->add($pizza, $qty);
        }
        // Refreshes all items amounts of the Cart
        cart()->refreshAllItemsData();

        if ($pizza->wasChanged()) {
            // Restore the original item's price
            $pizza->price = $original_price;
            $pizza->update();
        }

        // Add the currency selected to the custom field on the cart
        DB::table('carts')->where('auth_user', auth('api')->user()->id)->latest()
                          ->update(['currency' => Config::get('cart_manager.currency'),]);

        $response = [];
        $response['totals'] = cart()->totals();
        $response['items'] = cart()->items($displayCurrency = true);

        if ($request->headers->has('guest-created')) {
            // A new guest user has been created & the header is set
            $token = $request->header('Authorization');
            // Extract the Bearer token & pass it to the frontend to keep track of the User's cart
            $response['token'] = str_replace('Bearer ', '', $token);
        }
        return response()->json([$response], 200);
    }
}
