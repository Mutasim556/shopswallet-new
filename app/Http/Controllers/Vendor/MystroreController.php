<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Item;
use App\Models\Order;
use App\Models\Vendor;
use App\Models\OrderTransaction;
use App\Models\Store;
use App\Models\VendorType;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MystroreController extends Controller
{
    public function myShops(Request $data){
        if(isset($data->type)){
            $store_type = $data->type;
        }else{
            $store_type = null;
        }

        $module_id = Helpers::get_store_data()->module_id;



        $my_brand = VendorType::where('vendor_id',Auth::guard('vendor')->user()->id)->first();

        $my_stores = [];
        $product = '';
        if($my_brand){
            $stores = VendorType::with('module','vendor')
                    ->where([['module_id',$module_id]])
                    ->when($store_type!=null,function(Builder $query)use($store_type){
                        $query->where([['vendor_type',$store_type]]);
                    })
                    ->get();

                    // dd($stores);

            foreach(explode(',',$my_brand->brand_id) as $brand_id){
                foreach($stores as $store){
                   if(in_array($brand_id,explode(',',$store->brand_id))){
                        $store_id = Store::where([['vendor_id',$store->vendor_id],['module_id',$module_id]])->select('id')->first();
                        // dd($store_id->id);
                        $product_count = DB::table('items')->where([['brand_id',$brand_id],['store_id',$store_id->id],['module_id',$module_id]])->get();
                        // dd($product_count);
                        $single = [];
                        $brand_details = Brand::find($brand_id);
                        $single['vendor_id']=$store->vendor_id;
                        $single['store_id']=$store_id->id;
                        $single['module_id']=$module_id;
                        $single['brand_id']=$brand_details->id;
                        $single['vendor_name']=$store->vendor->f_name." ".$store->vendor->l_name;
                        $single['vendor_email']=$store->vendor->email;
                        $single['vendor_phone']=$store->vendor->phone;
                        $single['brand']=$brand_details->name;
                        $single['total_product']=count($product_count);
                        $single['products']='';

                        foreach($product_count as $item){
                            $single['products'] = $single['products'].'<tr><td>'.$item->id.'</td><td>'.$item->name.'</td><td>'.$item->price.'</td><td>'.$item->discount.'</td><td>'.$item->discount_type.'</td><td>'.$item->stock.'</td></tr>';
                        }
                        array_push($my_stores,$single);
                   }
                }
            }
        }

        if( $store_type ==null){
            $store_type = translate('messages.all_stores');
        }
        // dd($my_stores);
        return view('vendor-views.my_stores.index',compact('my_stores','store_type'));
    }
}
