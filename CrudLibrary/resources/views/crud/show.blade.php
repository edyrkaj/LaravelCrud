@extends('layouts.crud')
@section('crud_title')
    <h4 class="box-title">{{trans('crud.show')}} <span class="text-bold">{{trans("crud.$table_title")}}</span></h4>
@stop
@section('actions')
    @if(isset($title_actions))
        {!!panel_tools(crud_title_actions($title_actions))!!}
    @else
        {!!panel_tools()!!}
    @endif
@stop
@section('content')
    <!--Display Add Fields-->
    <div class="table-responsive">
        @if(Session::has('messages'))
            <?php
            $message = json_decode(Session::get('messages'));
            Session::forget('messages');
            $status = trans("crud.$message->status");
            ?>
            <div class="alert alert-{{$message->status}}">
                <button data-dismiss="alert" class="close">×</button>
                <strong class="uppercase">{{$status}}!</strong>
                @if(is_object($message->data)) <br/>
                @foreach ($message->data->all() as $message)
                    {{$message}} <br/>
                @endforeach
                @else
                    {{$message->data}}
                @endif
            </div>
        @endif
        @if(isset($columns))
            <table class="table table-condensed table-hover" width="100%" id="crud_table">
                <thead>
                <tr>
                    @foreach($columns as $column)
                        <th>{{trans("crud.$column")}}</th>
                    @endforeach
                    @if(isset($actions))
                        <th>{{trans("crud.actions")}}</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $row)
                    @if(isset($row->deleted_at))
                        <tr class="danger">
                    @else
                        <tr>
                            @endif
                            @foreach($columns as $column)
                                <td>{!!show_field($column, $columns_type[$column], $row->$column)!!}</td>
                            @endforeach
                            @if(isset($actions))
                                <td>
                                @foreach($actions as $key => $action)
                                    <!-- Check cases to exclude from view-->
                                        @if($key == 'delete' && isset($row->deleted_at))
                                        @elseif($key == 'restore' && !isset($row->deleted_at))
                                        @elseif($key == 'activate' && ($row->active == 1))
                                        @elseif($key == 'inactivate' && ($row->active == 0))
                                        @elseif($key == 'lock' && ($row->locked == true))
                                        @elseif($key == 'unlock' && ($row->locked == false))
                                        @else
                                            @if(isset($action->callback))
                                                {!!crud_action($action->attributes, $action->content, $primary_key, $row, $action->callback)!!}
                                            @else
                                                {!!crud_action($action->attributes, $action->content, $primary_key, $row)!!}
                                            @endif
                                        @endif
                                    @endforeach
                                </td>
                            @endif
                        </tr>
                        @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if(isset($scripts))
        {!! $scripts !!}
    @endif
@endsection

