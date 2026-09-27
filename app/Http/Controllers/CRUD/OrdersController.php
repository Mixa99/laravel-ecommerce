<?php

namespace App\Http\Controllers\CRUD;

use App\Http\Controllers\Controller;
use App\Http\Requests\CartRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use function Pest\Laravel\json;

class OrdersController extends Controller
{
    public function apiShow($id){
        $order = Order::with('items.product')->find($id);
        if($order){
            return response()->json(['message' => $order]);
        } else {
            return response()->json(['message' => 'Order not found']);
        }
    }

    public function apiShowOrdersForUser(){
        $orders = Order::with('items.product')->where('user_id', Auth::user()->id)->get();

        return response()->json(['orders' => $orders]);
    }

    public function apiStore(CartRequest $request){
        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->date_of_delivery = Carbon::now("Europe/Belgrade")->addDays(5)->format('Y-m-d');
        $order->save();
        $order = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->first(); 
        $price = 0;
        foreach($request->products as $p){
            $product = Product::find($p['id']);
            if($product->quantity >= $p['quantity']){
                $product->quantity -= $p['quantity'];
            } else {
                $order->delete();
                return response()->json(['message' => 'There is no products left']);
            }
            $product->save(); 
            $price += $product->price * $p['quantity'];
            $order_item = new OrderItem();
            $order_item->order_id = $order->id;
            $order_item->product_id = $product->id;
            $order_item->quantity = $p['quantity'];
            $order_item->save();
        }
        $order->total_price = $price;
        $order->save();
        
    return response()->json(['message' => $request->products]);
    }
    
    // U realnom projektu je potrebna validacija
    public function apiEdit(Request $request, $id){
        $order = Order::find($id);
        if($order->status){
            $order->status = $request->status;
        }
        if($order->date_of_delivery){
            $order->date_of_delivery = $request->date_of_delivery;
        }
        if($order->total_price){
            $order->total_price = $request->total_price;
        }
        $order->save();
        return response()->json(['message' => 'Order Updated']);
    }

    public function apiDestroy($id){
        $order = Order::find($id);
        if($order){
            $order->delete();
            return response()->json(['message' => 'Order deleted']);
        } else {
            return response()->json(['message' => 'Order not found']);
        }
    }

    public function index(){
        $order = Order::paginate(10)->get();
        return view('orders', compact($order));
    }

    public function show($id){
        $order = Order::find($id);
        if($order){
            return view('orders', compact($order));
        } else {
            return back()->withErrors('Order not found');
        }
    }

    public function showOrdersForUser(){
        $orders = Order::with('items.product')->where('user_id', Auth::user()->id)->get();

        return view('orders', compact('orders'));
    }


    public function store(CartRequest $request){
        $order = new Order();
        $order->user_id = Auth::user()->id;
        $order->date_of_delivery = Carbon::now("Europe/Belgrade")->addDays(5)->format('Y-m-d');
        $order->save();
        $order = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->first(); 
        $price = 0;
        foreach($request->products as $p){
            $product = Product::find($p['id']);
            if($product->quantity >= $p['quantity']){
                $product->quantity -= $p['quantity'];
            } else {
                $order->delete();
                return back()->withErrors('There is no products left.');
            }
            $product->save(); 
            $price += $product->price * $p['quantity'];
            $order_item = new OrderItem();
            $order_item->order_id = $order->id;
            $order_item->product_id = $product->id;
            $order_item->quantity = $p['quantity'];
            $order_item->save();
        }
        $order->total_price = $price;
        $order->save();

        return view('orders', compact($request->products));
    }

    public function makeOrder(Request $request){
        $selectedIds = $request->input('selected');

        if(!$selectedIds || !is_array($selectedIds)){
            return back()->withErrors('error', 'Niste izabrali nijedan proizvod');
        }

        $cart = collect(session('cart', []));
        $productsToPay = $cart->whereIn('id', $selectedIds)->values();

        if($productsToPay->isEmpty()){
            return back()->withErrors('Izabrani proizvodi nisu prona]eni u korpi.');
        }

        try{
            DB::transaction(function() use ($productsToPay, $selectedIds) { 
            $order = new Order();
            $order->user_id = Auth::user()->id;
            $order->date_of_delivery = Carbon::now("Europe/Belgrade")->addDays(5)->format('Y-m-d');
            $order->save();

            $totalPrice = 0;

            foreach($productsToPay as $p){
                $product = Product::find($p['id']);

                if($product->quantity < $p['quantity']){
                    throw new \Exception('There is no products left.');
                }

                $product->quantity -= $p['quantity'];
                $product->save();

                $totalPrice += $product->price * $p['quantity'];

                $order_item = new OrderItem();
                $order_item->order_id = $order->id;
                $order_item->product_id = $product->id;
                $order_item->quantity = $p['quantity'];
                $order_item->save();
            }

            $order->total_price = $totalPrice;
            $order->save();
        });
        } catch (Exception $e){
            return back()->withErrors($e->getMessage());
        }

        $updatedCart = $cart->reject(fn($item) => in_array($item['id'], $selectedIds))->values();
        session(['cart' => $updatedCart]);

        return route('orders.show');
    }
    
    // U realnom projektu je potrebna validacija
    public function edit(Request $request, $id){
        $order = Order::find($id);
        if($order->status){
            $order->status = $request->status;
        }
        if($order->date_of_delivery){
            $order->date_of_delivery = $request->date_of_delivery;
        }
        if($order->total_price){
            $order->total_price = $request->total_price;
        }
        $order->save();
        return redirect('orders')->with('Order Updated');
    }

    public function destroy($id){
        $order = Order::find($id);
        if($order){
            $order->delete();
            return redirect()->route('order.index')->with('success', 'Order deleted successfully.');;
        } else {
            return back()->withErrors('Order is not created');
        }
    }
}
