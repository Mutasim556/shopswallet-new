@extends('layouts.admin.app')

@section('title', translate('Add new sub sub category'))

@push('css_or_js')
@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">Sub Sub Category</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{ route('admin.category.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    @php($language = \App\Models\BusinessSetting::where('key', 'language')->first())
                    @php($language = $language->value ?? null)
                    @php($default_lang = str_replace('_', '-', app()->getLocale()))
                    @if ($language)
                        @php($default_lang = json_decode($language)[0])
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link lang_link active" href="#"
                                    id="default-link">{{ translate('messages.default') }}</a>
                            </li>
                            @foreach (json_decode($language) as $lang)
                                <li class="nav-item">
                                    <a class="nav-link lang_link" href="#"
                                        id="{{ $lang }}-link">{{ \App\CentralLogics\Helpers::get_language_name($lang) . '(' . strtoupper($lang) . ')' }}</a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="form-group lang_form" id="default-form">
                            <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.name') }}
                                ({{ translate('messages.default') }})</label>
                            <input type="text" name="name[]" class="form-control"
                                placeholder="{{ translate('messages.new_sub_sub_category') }}" maxlength="191"
                                oninvalid="document.getElementById('en-link').click()">
                        </div>
                        <input type="hidden" name="lang[]" value="default">
                        @foreach (json_decode($language) as $lang)
                            <div class="form-group d-none lang_form" id="{{ $lang }}-form">
                                <label class="input-label" for="exampleFormControlInput1">{{ translate('messages.name') }}
                                    ({{ strtoupper($lang) }})</label>
                                <input type="text" name="name[]" class="form-control"
                                    placeholder="{{ translate('messages.new_sub_category') }}" maxlength="191"
                                    oninvalid="document.getElementById('en-link').click()">
                            </div>
                            <input type="hidden" name="lang[]" value="{{ $lang }}">
                        @endforeach
                    @else
                        <div class="form-group">
                            <label class="input-label"
                                for="exampleFormControlInput1">{{ translate('messages.name') }}</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="{{ translate('messages.new_sub_sub_category') }}" value="{{ old('name') }}"
                                maxlength="191">
                        </div>
                        <input type="hidden" name="lang[]" value="default">
                    @endif
                    @php( $mod_check = DB::table('modules')->where('id', session()->get('dash_params')['module_id'])->first())
                    @if ($mod_check && ($mod_check->module_type == 'services' || $mod_check->module_type == 'booking'))
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlSelect1">Categories
                                <span class="input-label-secondary">*</span></label>
                            <select id="selectCategory" name="category_id" class="form-control" required>
                                <option value="">Please Select</option>
                                @foreach (\App\Models\Category::where(['position' => 0])->where('module_id', session()->get('current_module'))->get() as $cat)
                                    <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlSelect1">Sub Categories
                                <span class="input-label-secondary">*</span></label>
                            <select id="selectCategory2" name="parent_id" class="form-control" required>
                                <option value="">Please Select</option>

                            </select>
                        </div>
                        <div class="form-group">
                            <div class="h-100 d-flex flex-column">
                                <label class="m-0">{{ translate('messages.image') }} <small class="text-danger">* (
                                        {{ translate('messages.ratio') }} 1:1)</small></label>
                                <center class="py-3 my-auto">
                                    <img class="img--100" id="viewer"
                                        @if (isset($category)) src="{{ asset('storage/app/public/category') }}/{{ $category['image'] }}"
                                    @else
                                    src="{{ asset('public/assets/admin/img/900x400/img1.jpg') }}" @endif
                                        alt="image" />
                                </center>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                    <label class="custom-file-label"
                                        for="customFileEg1">{{ translate('messages.choose_file') }}</label>
                                </div>


                                <small class="text-danger mt-1 d-none d-md-block">&nbsp;</small>
                                    <label class="mt-4">{{ translate('messages.video') }} </label>
                                    <div class="custom-file">
                                        <input type="file" name="video" id="customFileEg2" class="custom-file-input"
                                            accept="video/*" >
                                        <label class="custom-file-label"
                                            for="customFileEg2">{{ translate('messages.choose_file') }}</label>
                                    </div>
                            </div>
                        </div>
                    @else
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlSelect1">Sub Categories
                                <span class="input-label-secondary">*</span></label>
                            <select id="selectCategory2" name="parent_id" class="form-control" required>
                                <option value="">Please Select</option>
                                @foreach (\App\Models\Category::where(['position' => 1])->where('module_id',session()->get('dash_params')['module_id'])->get() as $category)
                                    <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif


                    {{-- <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">Name</label>
                        <input type="text" name="name" class="form-control" placeholder="{{translate('messages.new_category')}}" required>
                    </div> --}}
                    <input name="position" value="2" class="initial-hidden">
                    <div class="btn--container justify-content-end mt-3">
                        <button type="reset" id="reset_btn"
                            class="btn btn--reset">{{ translate('messages.reset') }}</button>
                        <button type="submit"
                            class="btn btn--primary">{{ isset($category) ? translate('messages.update') : translate('messages.add') }}</button>
                    </div>

                </form>
            </div>

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <hr>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title"></h5>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                            class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                            data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true
                               }'>
                            <thead class="thead-light">
                                <tr>
                                    <th>#sl</th>
                                    <th class="w--5">Sub Category</th>
                                    <th class="w--5">Sub Sub Category</th>
                                    <th class="w--2">Status</th>
                                    <th class="w--1">Action</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th>
                                        <input type="text" id="column1_search" class="form-control form-control-sm"
                                            placeholder="Search Sub Category">
                                    </th>

                                    <th>
                                        <input type="text" id="column2_search" class="form-control form-control-sm"
                                            placeholder="Search Sub Sub Category">
                                    </th>

                                    <th>
                                        <select id="column3_search" class="js-select2-custom"
                                            data-hs-select2-options='{
                                              "minimumResultsForSearch": "Infinity",
                                              "customClass": "custom-select custom-select-sm text-capitalize"
                                            }'>
                                            <option value="">Any</option>
                                            <option value="Active">Active</option>
                                            <option value="Disabled">Disabled</option>
                                        </select>
                                    </th>
                                    <th>
                                        {{-- <input type="text" id="column4_search" class="form-control form-control-sm"
                                           placeholder="Search countries"> --}}
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach (\App\Models\Category::with(['parent'])->where(['position' => 2])->where('module_id',session()->get('dash_params')['module_id'])->latest()->get() as $key => $category)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                {{ $category->parent_id != 0 ? $category->parent['name'] : '' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                {{ $category['name'] }}
                                            </span>
                                        </td>

                                        <td>
                                            @if ($category['status'] == 1)
                                                <div class="initial--6"
                                                    onclick="location.href='{{ route('admin.category.status', [$category['id'], 0]) }}'">
                                                    <span class="legend-indicator bg-success"></span>Active
                                                </div>
                                            @else
                                                <div class="initial--6"
                                                    onclick="location.href='{{ route('admin.category.status', [$category['id'], 1]) }}'">
                                                    <span class="legend-indicator bg-danger"></span>Disabled
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <!-- Dropdown -->
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">
                                                    <i class="tio-settings"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.category.edit', [$category['id'],($mod_check->module_type == 'services'||$mod_check->module_type == 'booking')?'Child Category':'Sub Sub']) }}">Edit</a>
                                                    <a class="dropdown-item" href="javascript:"
                                                        onclick="$('#category-{{ $category['id'] }}').submit()">Delete</a>
                                                    <form action="{{ route('admin.category.delete', [$category['id']]) }}"
                                                        method="post" id="category-{{ $category['id'] }}">
                                                        @csrf @method('delete')
                                                    </form>
                                                </div>
                                            </div>
                                            <!-- End Dropdown -->
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#customFileEg1").change(function() {
            readURL(this);
        });
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function() {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });


            $('#column2_search').on('keyup', function() {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function() {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });

            $('#selectCategory').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
            $('#selectCategory2').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
    <script>
        $(".lang_link").click(function(e) {
            e.preventDefault();
            $(".lang_link").removeClass('active');
            $(".lang_form").addClass('d-none');
            $(this).addClass('active');

            let form_id = this.id;
            let lang = form_id.substring(0, form_id.length - 5);
            console.log(lang);
            $("#" + lang + "-form").removeClass('d-none');
            if (lang == '{{ $default_lang }}') {
                $(".from_part_2").removeClass('d-none');
            } else {
                $(".from_part_2").addClass('d-none');
            }
        });

        $('#selectCategory').on("change", function() {
                    $.ajax({
                            type: "get",
                            url: 'show/' + $(this).val(),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(data) {
                                $('#selectCategory2').empty().append('<option value="">Please Select</option>');
                                $.each(data,function(idx,val){
                                    $('#selectCategory2').append('<option value="'+val.id+'">'+val.name+'</option>');
                                })
                            },
                            error : function(err){

                            }
                    })
                });
    </script>
@endpush
