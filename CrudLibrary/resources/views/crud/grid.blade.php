@extends('layouts.master')
@section('content')
    <div class="container-fluid container-fullw padding-bottom-10">
     @if($errors->any())
        <div class="box box-info">
            <div class="box-header with-border">
                <h4 class="box-title"><i class="ti-thumb-down">&nbsp;</i>{!! $errors->first() !!}</h4>
                <div class="box-tools pull-right">
                    <a data-original-title="Close" data-toggle="tooltip" data-placement="top" class="btn btn-transparent btn-sm panel-close" href="#"><i class="ti-close"></i></a>
                </div>
            </div>
        </div>
     @endif
    <div class="row">
        <div class="col-md-12">
            <?php echo $grid; ?>
        </div>
    </div>
</div>
@endsection