<?php

namespace App\Http\Controllers\CRUD;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class ProductController extends Controller
{
    public function apiIndex()
    {
        $products = Product::paginate(5);
        return response()->json(['message' => $products]);
    }

    public function apiShow($id)
    {
        $product = Product::find($id);
        $comments = DB::table('comments')
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->select('comments.*', 'users.name')
            ->where('comments.product_id', '=', $id)->get();
        if ($product) {
            return response()->json(['product' => $product, 'comments' => $comments]);
        } else {
            return response()->json('Product not found');
        }
    }

    public function apiStore(ProductRequest $request)
    {
        if ($request->hasFile('path')) {
            $product = new Product();
            $product->name = $request->name;
            $product->price = $request->price;
            if ($request->category) {
                $product->category = $request->category;
            }
            $product->quantity = $request->quantity;

            $imageName = $request->file('path')->getClientOriginalName();
            $request->file('path')->storeAs('product-img/', $imageName, 'public');
            $product->path = 'storage/product-img/' . $imageName;

            $product->save();
            return response()->json('Product created successfully.');
        }
        return response()->json('Product is not created');
    }

    public function apiEdit(ProductRequest $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->name = $request->name;
            $product->path = $request->path;
            $product->price = $request->price;
            if ($request->category) {
                $product->category = $request->category;
            }
            $product->quantity = $request->quantity;
            $product->save();
            return response()->json('Product updated successfuly');
        } else {
            return response()->json('Product not found');
        }
    }

    public function apiDestroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return response()->json('Product deleted successfuly');
        } else {
            return response()->json('Product not found');
        }
    }

    public function index()
    {
        $products = Product::paginate(6);
        return view('index', ['products' => $products]);
    }

    public function show($id)
    {
        $product = Product::find($id);
        $comments = DB::table('comments')
            ->join('users', 'comments.user_id', '=', 'users.id')
            ->select('comments.*', 'users.name')
            ->where('comments.product_id', '=', $id)->get();

        if ($product) {
            return view('product', compact('product', 'comments'));
        } else {
            return redirect('/');
        }
    }

    public function storeView()
    {
        return view('layouts.addProduct');
    }

    public function store(ProductRequest $request)
    {

        if ($request->hasFile('path')) {
            $product = new Product();
            $product->name = $request->name;
            $product->price = $request->price;
            if ($request->category) {
                $product->category = $request->category;
            }
            $product->quantity = $request->quantity;

            $imageName = $request->file('path')->getClientOriginalName();
            $request->file('path')->storeAs('product-img/', $imageName, 'public');
            $product->path = 'storage/product-img/' . $imageName;

            if ($this->productExists($product)) {
                $product->save();
                return redirect()->back()->with('success', 'Product created successfully.');
            } else {
                return redirect()->back()->with('error', 'Product is already exists.');
            }
        }

        return redirect()->back()->with('error', 'Product is not created');
    }


    private function productExists(Product $product)
    {
        return Product::where('name', $product->name)
            ->where('path', $product->path)
            ->exists();
    }

    public function edit(ProductRequest $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->name = $request->name;
            $product->path = $request->path;
            $product->price = $request->price;
            if ($request->category) {
                $product->category = $request->category;
            }
            $product->quantity = $request->quantity;
            $product->save();
            return redirect()->route('product.index')->with('success', 'Product created successfully.');
        } else {
            return back()->withErrors('Product is not created');
        }
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete();
            return redirect()->route('product.index')->with('success', 'Product deleted successfully.');;
        } else {
            return back()->withErrors('Product is not created');
        }
    }

    public function showProductsVulnerable(Request $request)
    {
        $products = collect(); // Inicijalizacija prazne kolekcije
        $paginator = null;     // Inicijalizacija paginatora

        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');

            $sql = "SELECT * FROM products WHERE name LIKE '%$searchTerm%'";

            try {
                $rawResults = DB::select($sql);
                $products = Product::hydrate($rawResults);


                $perPage = 6;
                $currentPage = $request->get('page', 1);
                $offset = ($currentPage - 1) * $perPage;

                $currentPageProducts = $products->slice($offset, $perPage)->all();

                $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
                    $currentPageProducts,
                    $products->count(),
                    $perPage,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
            } catch (\Exception $e) {
                $products = collect(); // U slucaju greske
            }


        } else {
            $paginator = Product::paginate(6);
        }

        return view('index', ['products' => $paginator]);
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        
        if (empty($search)) {
            return view('search', ['products' => []]);
        }

        $sql = "SELECT * FROM products WHERE name LIKE '%$search%'";

        $products= [];

        try {
           
            $products = DB::select($sql);

            return view('search', compact('products', 'search'));

        } catch (\Exception $e) {
            $products=[];
        }
    }


    public function showProductsFilteredFromCache(Request $request)
    {

        $allProducts = Cache::remember('all_products_for_filter', 60, function () {
            return Product::all();
        });

        $productsToDisplay = $allProducts;

        if ($request->has('search') && $request->input('search') != '') {
            $searchTerm = $request->input('search');

            $filteredProducts = $allProducts->filter(function ($product) use ($searchTerm) {
                return str_contains(
                    mb_strtolower($product->name, 'UTF-8'),
                    mb_strtolower($searchTerm, 'UTF-8')
                );
            });

            $productsToDisplay = $filteredProducts;
        }

        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
        $perPage = 6;

        // "Odseci" kolekciju da bi dobio samo proizvode za trenutnu stranicu
        $currentPageItems = $productsToDisplay->slice(($currentPage - 1) * $perPage, $perPage)->all();

        // Kreiraj paginator objekat
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $productsToDisplay->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('index', ['products' => $paginator]);
    }
}
