@extends('layouts.crud')

@section('crud_title')
<h4 class="panel-title">{{trans('crud.read')}} <span class="text-bold">{{trans("crud.$table_title")}}</span></h4>
@endsection
@section('actions')
{!!panel_tools(
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
<form class="form-readcrud">
    <!--Display Add Fields-->
    <div class="form-group">
        @foreach($columns as $column)
        <div class="{{$columns_class[$column]}}">
            <div class="form-group">
                <label for="{{$column}}">{{trans("crud.$column")}}</label>
                <?php $value = isset($rows[0]->$column) ? $rows[0]->$column : '';?>
                @if($columns_type[$column] == 'timestamp')
                    {!!field('readonly', $column, $value)!!}
                @else
                    {!!field($columns_type[$column], $column, $value, ['disabled' => 'disabled'])!!}
                @endif
            </div>
        </div>
        @endforeach
    </div>
</form>
@endsection
