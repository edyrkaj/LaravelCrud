@extends('layouts.crud')
@section('crud_title')
    <h4 class="box-title">{{trans('crud.add')}} <span class="text-bold">{{trans("crud.$table_title")}}</span></h4>
@endsection
@section('actions')
    {!!
        panel_tools(
            panel_tool(
            array(
            'class' => 'btn btn-xs btn-link tooltips btn-box-tool',
            'href' => $menu->back["url"],
            'data-original-title' => $menu->back["title"],
            'data-placement' => 'right'
            ), 'fa-arrow-circle-left'
        ))
    !!}
@endsection
@section('content')
    <!--Display Add Fields-->
    {!!Form::open(['url' => URL::to(routeClass()),'class' => 'form-addcrud simple_crud', 'enctype' => 'multipart/form-data'])!!}
    {{ csrf_field() }}
        <!--Display Add Fields-->
        {!!Form::Hidden('created_at', date('Y-m-d H:i:s'))!!}
        {!!Form::Hidden('updated_at', date('Y-m-d H:i:s'))!!}
        <div class="row">
            @foreach($columns as $i => $column)
                @if(($i % 4) == 0)
                    </div><div class="row">
                    <?php $i == 0; ?>
                @endif
                @if(!in_array($column,array($primary_key, 'created_at', 'updated_at', 'deleted_at')))
                    <div class="{{$columns_class[$column]}}">
                        <div class="form-group">
                            <label for="{{$column}}">{{trans("crud.$column")}} {!!(strpos($columns_type[$column], "relation") !== FALSE || $columns_required[$column]) ? symbol_required($column) : ''!!}</label>
                            <!--&nbsp;({!!$columns_type[$column]!!})-->

                            @if(isset($columns_validation[$column]))
                                {!!field($columns_type[$column], $column, null, ['data-validation'=> $columns_validation[$column]])!!}
                            @else
                                {!!field($columns_type[$column], $column)!!}
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="form-group">
            <div class="col-lg-12">
                <hr/>
            </div>
        </div>
        <!-- END OF FORM-->
        <div class="clearfix">&nbsp;</div>
        <div class="form-group">
            <div class="col-lg-4 pull-right">
                <div class="form-group">
                    <div class="col-sm-6">
                        <button class="btn btn-success btn-block" type="submit">
                            <i class="fa fa-check">&nbsp;</i>{{trans('crud.save')}}
                        </button>
                    </div>
                    <div class="col-sm-6">
                        <button type="reset" class="btn btn-primary btn-block">
                            <i class="fa fa-recycle">&nbsp;</i>{{trans('crud.reset')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {!!Form::Hidden('method', 'add')!!}
    {!!Form::close()!!}
@endsection
