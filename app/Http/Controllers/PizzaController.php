<?php

namespace App\Http\Controllers;

use App\Pizza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client;

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
        $exchange_value = 1;
        if ($request->has('currency')) {
            $currency = mb_strtoupper($request['currency'], 'UTF-8');
            try {
                $client = new Client();
                $exchange = $client->request(
                    'GET',
                    // env('EXCHANGE_RATES_URL')
                    'https://api.exchangeratesapi.io/latest?base=USD&symbols='.$currency
                );
                $response = json_decode($exchange->getBody()->getContents(), True);
                $exchange_value = $response['rates'][$currency];
                Config::set('cart_manager.currency', $currency);
            } catch(\Exception $e){
                return response()->json(['error'=>$e->getMessage()], 409);
            }
        }

        $qty = 0;
        if ($request->has('qty')) {
            $qty = $request->qty;
        }
        if (cart()->isEmpty()) {
            $qty == 0 ? $qty = 1 : '';
            $cart = Pizza::addToCart($pizza->id, $qty);
        } else {
            cart()->add($pizza, $qty);
        }

        $response = [];
        $response['data'] = cart()->data();
        $response['totals'] = cart()->totals();
        $response['items'] = cart()->items();

        if ($request->headers->has('guest-created')) {
            $token = $request->header('Authorization');
            $response['token'] = str_replace('Bearer ', '', $token);
        }
        return response()->json([$response], 200);
    }
}
