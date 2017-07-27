<div class="container-fluid container-fullw padding-bottom-10">
<div class="box box-info">
    <div class="box-header with-border">
        @yield('crud_title')
        <div class="box-tools pull-right">
            @yield('actions')
        </div>
    </div>
    <div class="box-body">
        @yield('content')
    </div>
    @yield('footer')
</div>
</div>
<script src="{{asset('js/validations/simple_crud.js')}}"></script>

<script type="text/javascript" defer>
    $(document).ready(function(){
        SimpleCrud.init();
        // Initialized dataTable js
        $('#crud_table').dataTable({ "aaSorting": []});
    });
</script>

