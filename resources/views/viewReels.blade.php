@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/reel.js') }}"></script>
@endsection
@section('content')
<section class="section">
    <div class="card">
        <div class="card-header">
            <div class="page-title w-100">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-normal d-flex align-items-center"> {{ __('allReels') }} </h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped w-100" id="reelTable">
                <thead>
                    <tr>
                        <th style="width: 150px"> {{ __('content') }} </th>
                        <th> {{ __('description') }} </th>
                        <th> {{ __('hashtags') }} </th>
                        <th> {{ __('counts') }} </th>
                        <th> {{ __('user') }} </th>
                        <th style="text-align: right; width: 200px;"> {{ __('action') }} </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

@endsection