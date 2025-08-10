@if (Auth::guard('vendor')->user()->vendor_type!=null)
    @if (Auth::guard('vendor')->user()->vendor_type->vendor_type=='manufacturer')
        <div class="col-sm-6 col-lg-3">
            <!-- Card -->
            <a class="resturant-card dashboard--card card--bg-1" href="{{route('vendor.my-stores.my_stores',['wholeseller'])}}">
            <h4 class="title">{{$stores['wholeseller']}}</h4>
            <span class="subtitle">{{translate('messages.wholesellers')}}</span>
            <img src="{{asset('public/assets/admin/img/dashboard/wholeseller.png')}}" alt="img" class="resturant-icon">
            </a>
        </div>
        <div class="col-sm-6 col-lg-3">
            <!-- Card -->
            <a class="resturant-card dashboard--card card--bg-1" href="{{route('vendor.my-stores.my_stores',['retailer'])}}">
            <h4 class="title">{{$stores['retailer']}}</h4>
            <span class="subtitle">{{translate('messages.ratailers')}}</span>
            <img src="{{asset('public/assets/admin/img/dashboard/retailer.png')}}" alt="img" class="resturant-icon">
            </a>
        </div>
    @elseif(Auth::guard('vendor')->user()->vendor_type->vendor_type=='wholeseller')
        <div class="col-sm-6 col-lg-3">
            <!-- Card -->
            <a class="resturant-card dashboard--card card--bg-1" href="{{route('vendor.my-stores.my_stores',['retailer'])}}">
            <h4 class="title">{{$stores['retailer']}}</h4>
            <span class="subtitle">{{translate('messages.ratailers')}}</span>
            <img src="{{asset('public/assets/admin/img/dashboard/retailer.png')}}" alt="img" class="resturant-icon">
            </a>
        </div>
    @endif
@else
    <a href="{{ url('store-panel/business-settings/store-setup#vendor_type_form') }}" class="btn btn-danger">{{ translate('messages.update_vedor_type_now') }}</a>
@endif



{{-- <div class="col-sm-6 col-lg-3">
    <!-- Card -->
    <a class="resturant-card dashboard--card card--bg-2" href="{{route('vendor.order.list',['cooking'])}}">
        @php($store_data=\App\CentralLogics\Helpers::get_store_data())
       <h4 class="title">{{$data['cooking']}}</h4>
        @if($store_data->module->module_type == 'food')
       <span class="subtitle">{{translate('messages.cooking')}}</span>
        @else
       <span class="subtitle">{{translate('messages.processing')}}</span>
        @endif
       <img src="{{asset('public/assets/admin/img/dashboard/2.png')}}" alt="img" class="resturant-icon">
    </a>
    <!-- End Card -->
</div>

<div class="col-sm-6 col-lg-3">
    <!-- Card -->
    <a class="resturant-card dashboard--card card--bg-3" href="{{route('vendor.order.list',['ready_for_delivery'])}}">
       <h4 class="title">{{$data['ready_for_delivery']}}</h4>
       <span class="subtitle">{{translate('messages.ready_for_delivery')}}</span>
       <img src="{{asset('public/assets/admin/img/dashboard/3.png')}}" alt="img" class="resturant-icon">
    </a>
    <!-- End Card -->
</div>

<div class="col-sm-6 col-lg-3">
    <!-- Card -->
    <a class="resturant-card dashboard--card card--bg-4" href="{{route('vendor.order.list',['item_on_the_way'])}}">
       <h4 class="title">{{$data['item_on_the_way']}}</h4>
       <span class="subtitle">{{translate('messages.item_on_the_way')}}</span>
       <img src="{{asset('public/assets/admin/img/dashboard/4.png')}}" alt="img" class="resturant-icon">
    </a>
    <!-- End Card -->
</div> --}}



