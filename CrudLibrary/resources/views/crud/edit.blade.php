@extends('layouts.crud')
@section('crud_title')
    <h4 class="panel-title">{{trans('crud.edit')}} <span class="text-bold">{{trans("crud.$table_title")}}</span></h4>
@endsection

@section('actions')
{!!
panel_tools(
    panel_tool(
        array(
            'class' => 'btn btn-xs btn-link btn-transparent-grey tooltips',
            'href' => $menu->back["url"],
            'data-original-title' => $menu->back["title"],
            'data-placement' => 'right'
        ), 'fa-arrow-circle-left')
)
!!}
@endsection
@section('content')
<!--Display Add Fields-->
    {!!Form::open(['url' => URL::to(routeClass()),'class' => 'form-editcrud simple_crud', 'enctype' => 'multipart/form-data'])!!}
    {{ csrf_field() }}
    <input type="hidden" name="{{$primary_key}}" value="{{isset($rows[0]->$primary_key) ? $rows[0]->$primary_key : -1}}"/>
    <!--Display Add Fields-->
    <div class="row">
        @foreach($columns as $i => $column)
            @if(($i % 4) == 0)
                </div><div class="row">
                <?php $i == 0; ?>
            @endif
        @if(!in_array($column,array($primary_key, 'created_at', 'updated_at', 'user_id', 'deleted_at')))
        <div class="{{$columns_class[$column]}}">
            <div class="form-group">
                <label for="{{$column}}">
                    {{trans("crud.$column")}}
                    {!!(strpos($columns_type[$column], "relation") !== FALSE || $columns_required[$column]) ? symbol_required($column) : ''!!}
                </label>

                @if(isset($columns_validation[$column]))
                    {!!field(
                        $columns_type[$column],
                        $column,
                        isset($rows[0]->$column) ? $rows[0]->$column : null,
                        ['data-validation'=> $columns_validation[$column]]
                    )!!}
                @else
                    {!!field($columns_type[$column], $column, isset($rows[0]->$column) ? $rows[0]->$column : null)!!}
                @endif
            </div>
        </div>
        @endif
        @endforeach
    </div>
    <div class="form-group">
        <div class="col-lg-12"><hr/></div>
    </div>
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
    <input type="hidden" name="method" value="edit" />
    {!!Form::close()!!}
@endsection
