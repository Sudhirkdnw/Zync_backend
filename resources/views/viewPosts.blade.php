@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/post.js') }}"></script>
@endsection
@section('content')
<section class="section">
    <div class="card">
        <div class="card-header">
            <div class="page-title w-100">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-normal d-flex align-items-center"> {{ __('allPosts') }} </h4>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped w-100" id="allPostsTable">
                <thead>
                    <tr>
                        <th style="width: 150px"> {{ __('content') }} </th>
                        <th> {{ __('userName') }} </th>
                        <th> {{ __('fullname') }} </th>
                        <th> {{ __('comments') }} </th>
                        <th> {{ __('likes') }} </th>
                        <th> {{ __('restricted') }} </th>
                        <th> {{ __('createdAt') }} </th>
                        <th style="text-align: right; width: 200px;"> {{ __('action') }} </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

@endsection