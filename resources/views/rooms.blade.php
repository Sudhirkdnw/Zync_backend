@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/room.js') }}"></script>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="page-title w-100">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-normal">{{ __('rooms')}}</h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped w-100" id="roomsListTable">
                <thead>
                    <tr>
                        <th width="100px"> {{ __('roomImage')}} </th>
                        <th> {{ __('title')}} </th>
                        <th> {{ __('admin')}} </th>
                        <th> {{ __('totalMembers')}} </th>
                        <th> {{ __('joinRequestEnable')}} </th>
                        <th> {{ __('private')}} </th>
                        <th width="250px" style="text-align: right"> {{ __('action')}} </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>   
@endsection
