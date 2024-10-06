<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Models\purchase;

use App\Models\supplier;
use Illuminate\Http\Request;
use App\Models\purchaseProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class purchasecontroller extends Controller
{

    public function purchaseCreate(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'supplier_id' => 'required|string|max:50',
                'products' => 'required|array|min:1', // Ensure products array is provided
            ]);

            $user_id = Auth::id();
            $supplier_id = $request->input('supplier_id');
            $products = $request->input('products');

            // Insert into purchases table, leaving product_id null if not needed
            $purchase = purchase::create([
                'user_id' => $user_id,
                'supplier_id' => $supplier_id,

            ]);

            // Update each product's stock and insert into purchaseProduct table
            foreach ($products as $EachProduct) {
                $product_id = $EachProduct['product_id'];
                $quantity = $EachProduct['qty'];

                // Update product stock
                $product = Product::find($product_id);
                $product->stock += $quantity;
                $product->save();

                // Insert into purchase_products table
                purchaseProduct::create([
                    'purchase_id' => $purchase->id,
                    'user_id' => $user_id,
                    'qty' =>  $EachProduct['qty'],
                    'product_id' => $EachProduct['product_id'],

                ]);
            }
            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Request Successful"]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }



    function purchaseSelect(Request $request)
    {
        try {
            $user_id = Auth::id();
            $rows = purchase::where('user_id', $user_id)->with('supplier')->get();
            return response()->json(['status' => 'success', 'rows' => $rows]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }



    function purchaseDetails(Request $request)
    {
        try {
            $user_id = Auth::id();
            $supplierDetails = supplier::where('user_id', $user_id)->where('id', $request->input('sup_id'))->first();
            $purchase = purchase::where('id', $request->input('pur_id'))->first();
            $purchaseProduct = purchaseProduct::where('purchase_id', $request->input('pur_id'))
                ->where('user_id', $user_id)->with('product')
                ->get();
            $rows = array(
                'supplier' => $supplierDetails,
                'purchase' => $purchase,
                'product' => $purchaseProduct,
            );
            return response()->json(['status' => 'success', 'rows' => $rows]);
        } catch (Exception $e) {
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }



    function purchaseDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            $user_id = Auth::id();

            // purchase::where('purchase_id',$request->input('pur_id'))
            //     ->where('user_id',$user_id)
            //     ->delete();

            purchase::where('id', $request->input('pur_id'))->delete();

            DB::commit();
            return response()->json(['status' => 'success', 'message' => "Request Successful"]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }






    // // Other logic as needed

    // return redirect()->route('purchases.index')->with('success', 'Purchase added successfully.');

}
