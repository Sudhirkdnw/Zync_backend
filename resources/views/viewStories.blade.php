@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/story.js') }}"></script>
@endsection

@section('content')
    <section class="section">

        <div class="card">
            <div class="card-header">
                <div class="page-title w-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 fw-normal d-flex align-items-center"> {{ __('allStories') }} </h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped w-100" id="allStoriesTable">
                    <thead>
                        <tr>
                            <th width="100px"> {{ __('content')}} </th>
                            <th width="100px"> {{ __('fullname')}} </th>
                            <th width="100px"> {{ __('time')}} </th>
                            <th width="250px" style="text-align: right"> {{ __('action')}} </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
@endsection
