<?php

namespace App\Http\Controllers\Admin;


use App\Models\Translation;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Exports\brandsExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\Facades\Excel;
use Rap2hpoutre\FastExcel\FastExcel;

class BrandController extends Controller
{
    function index(Request $request)
    {
        $key = explode(' ', $request['search']);

        $brands = Brand::with('category')->orderBy('name')
        ->when(isset($key) , function($q) use($key){
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->where('module_id',Config::get('module.current_module_id'))
        ->paginate(config('default_pagination'));
        return view('admin-views.brands.index', compact('brands'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brands|max:100',
            'category_id' => 'required',
            'name.0' => 'required',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'name.category_id' => translate('messages.Category is required!'),
            'name.0.required'=>translate('default_data_is_required'),
        ]);

        $brand = new Brand();
        $brand->name = $request->name[array_search('default', $request->lang)];
        $brand->category_id=$request->category_id;
        $brand->module_id=Config::get('module.current_module_id');
        $brand->save();

        $data = [];
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->name[$index])){
                if ($key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Brand',
                        'translationable_id' => $brand->id,
                        'locale' => $key,
                        'key' => 'name',
                        'value' => $brand->name,
                    ));
                }
            }else{
                if ($request->name[$index] && $key != 'default') {
                    array_push($data, array(
                        'translationable_type' => 'App\Models\Brand',
                        'translationable_id' => $brand->id,
                        'locale' => $key,
                        'key' => 'name',
                        'value' => $request->name[$index],
                    ));
                }
            }
        }

        Translation::insert($data);

        Toastr::success(translate('messages.brand_added_successfully'));
        return back();
    }


    public function edit($id)
    {
        $brand = Brand::withoutGlobalScope('translate')->findOrFail($id);
        return view('admin-views.brands.edit', compact('brand'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100|unique:brands,name,'.$id,
            'name.0' => 'required',
        ], [
            'name.required' => translate('messages.Name is required!'),
            'name.0.required'=>translate('default_data_is_required'),
        ]);

        $brand = Brand::findOrFail($id);
        $brand->name = $request->name[array_search('default', $request->lang)];
        $brand->save();
        $default_lang = str_replace('_', '-', app()->getLocale());
        foreach ($request->lang as $index => $key) {
            if($default_lang == $key && !($request->name[$index])){
                if ($key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\brand',
                            'translationable_id' => $brand->id,
                            'locale' => $key,
                            'key' => 'name'
                        ],
                        ['value' => $brand->name]
                    );
                }
            }else{

                if ($request->name[$index] && $key != 'default') {
                    Translation::updateOrInsert(
                        [
                            'translationable_type' => 'App\Models\brand',
                            'translationable_id' => $brand->id,
                            'locale' => $key,
                            'key' => 'name'
                        ],
                        ['value' => $request->name[$index]]
                    );
                }
            }
        }
        Toastr::success(translate('messages.brand_updated_successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $brand = Brand::findOrFail($request->id);
        $brand->translations()->delete();
        $brand->delete();
        Toastr::success(translate('messages.brand_deleted_successfully'));
        return back();
    }

    // public function search(Request $request){
    //     $key = explode(' ', $request['search']);
    //     $attributes=Attribute::
    //     where(function ($q) use ($key) {
    //         foreach ($key as $value) {
    //             $q->orWhere('name', 'like', "%{$value}%");
    //         }
    //     })
    //     ->limit(50)->get();
    //     return response()->json([
    //         'view'=>view('admin-views.attribute.partials._table',compact('attributes'))->render(),
    //         'count'=>$attributes->count()

    //     ]);
    // }

    public function bulk_import_index()
    {
        return view('admin-views.attribute.bulk-import');
    }

    public function bulk_import_data(Request $request)
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            Toastr::error(translate('messages.you_have_uploaded_a_wrong_format_file'));
            return back();
        }

        $data = [];
        $skip = ['youtube_video_url'];
        foreach ($collections as $collection) {
                if ($collection['name'] === "" ) {
                    Toastr::error(translate('messages.please_fill_all_required_fields'));
                    return back();
                }


            array_push($data, [
                'name' => $collection['name'],
                'created_at'=>now(),
                'updated_at'=>now()
            ]);
        }
        DB::table('attributes')->insert($data);
        Toastr::success(translate('messages.attribute_imported_successfully',['count'=>count($data)]));
        return back();
    }

    public function bulk_export_index()
    {
        return view('admin-views.attribute.bulk-export');
    }

    public function bulk_export_data(Request $request)
    {
        $request->validate([
            'type'=>'required',
            'start_id'=>'required_if:type,id_wise',
            'end_id'=>'required_if:type,id_wise',
            'from_date'=>'required_if:type,date_wise',
            'to_date'=>'required_if:type,date_wise'
        ]);
        $attributes = Brand::when($request['type']=='date_wise', function($query)use($request){
            $query->whereBetween('created_at', [$request['from_date'].' 00:00:00', $request['to_date'].' 23:59:59']);
        })
        ->when($request['type']=='id_wise', function($query)use($request){
            $query->whereBetween('id', [$request['start_id'], $request['end_id']]);
        })
        ->get();
        return (new FastExcel($attributes))->download('Attributes.xlsx');
    }

    public function export_attributes(Request $request){
        $key = explode(' ', $request['search']);

        $attributes = Brand::orderBy('name')
        ->when(isset($key) , function($q) use($key){
            $q->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->get();

        $data=[
            'data' =>$attributes,
            'search' =>$request['search'] ?? null,
        ];
        // if($request->type == 'csv'){
        //     return Excel::download(new AttributesExport($data), 'Attributes.csv');
        // }
        // return Excel::download(new AttributesExport($data), 'Attributes.xlsx');



    }
}
