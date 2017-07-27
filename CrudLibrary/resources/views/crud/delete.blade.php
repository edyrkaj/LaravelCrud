@extends('layouts.crud')

@section('crud_title')
<h4 class="panel-title">{{trans('crud.delete')}} <span class="text-bold">{{trans("crud.$table_title")}}</span></h4>
@endsection
@section('content')
<!--Display Add Fields-->
<form class="form-editcrud simple_crud" action="{{URL::to(routeClass())}}" method="POST">
{{ csrf_field() }}
    <input type="hidden" name="{{$primary_key}}" value="{{isset($rows[0]->$primary_key) ? $rows[0]->$primary_key : -1}}"/>        

    <div class="alert alert-block alert-warning fade in">
        <h4 class="alert-heading"><i class="fa fa-trash"></i>
            {{trans('crud.sure')}}. id=[<strong>{{isset($rows[0]->$primary_key) ? $rows[0]->$primary_key : '-'}}</strong>] ?
        </h4>     
    </div>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="col-xs-6">
                <button class="btn btn-purple btn-block" type="submit">
                    <i class="fa fa-check">&nbsp;</i>{{trans('crud.delete')}}
                </button>
            </div>
            <div class="col-xs-6">
                <a href="{{URL::to(routeClass())}}" class="btn btn-primary btn-block">
                    <i class="fa fa-close">&nbsp;</i>{{trans('crud.cancel')}}
                </a>
            </div>
        </div>
    </div>    

    <input type="hidden" name="method" value="delete" />
</form>
@endsection


