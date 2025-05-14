@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/report.js') }}"></script>
@endsection

@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="page-title w-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 fw-normal d-flex align-items-center">{{ __('reportsList')}}
                            <div class="ms-3 card-tab">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item " role="presentation">
                                        <button class="nav-link " id="roomReportTab" data-bs-toggle="tab" data-bs-target="#roomReport-pane" type="button" role="tab" aria-controls="roomReport-panel" aria-selected="true"> {{ __('roomReports')}} </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="postReport-tab" data-bs-toggle="tab" data-bs-target="#postReport-pane" type="button" role="tab" aria-controls="postReport-pane" aria-selected="false">  {{ __('postReports')}} </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="userReportTab" data-bs-toggle="tab" data-bs-target="#userReport-pane" type="button" role="tab" aria-controls="userReport-panel" aria-selected="true">  {{ __('userReports')}} </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="reelReportTab" data-bs-toggle="tab" data-bs-target="#reelReport-pane" type="button" role="tab" aria-controls="reelReport-panel" aria-selected="true">  {{ __('reelReports')}} </button>
                                    </li>
                                </ul>
                            </div>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane" id="roomReport-pane" role="tabpanel" tabindex="0">
                        <table class="table table-striped w-100" id="reportTable">
                            <thead>
                                <tr>
                                    <th style="width: 100px"> {{ __('userImage')}} </th>
                                    <th> {{ __('roomName')}} </th>
                                    <th> {{ __('userIdentity')}} </th>
                                    <th> {{ __('reason')}} </th>
                                    <th> {{ __('description')}} </th>
                                    <th style="text-align: right; width: 350px;"> {{ __('action')}} </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane" id="postReport-pane" role="tabpanel" tabindex="0">
                        <table class="table table-striped w-100" id="postReportTable">
                            <thead>
                                <tr>
                                    <th style="width: 200px"> {{ __('post')}} </th>
                                    <th> {{ __('reason')}} </th>
                                    <th> {{ __('description')}} </th>
                                    <th style="text-align: right; width: 350px;"> {{ __('action')}} </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane" id="userReport-pane" role="tabpanel" tabindex="0">
                        <table class="table table-striped w-100" id="userReportTable">
                            <thead>
                                <tr>
                                    <th style="width: 250px"> {{ __('userImage')}} </th>
                                    <th> {{ __('fullname')}} </th>
                                    <th> {{ __('userIdentity')}} </th>
                                    <th> {{ __('reason')}} </th>
                                    <th> {{ __('description')}} </th>
                                    <th style="text-align: right; width: 350px;"> {{ __('action')}} </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="tab-pane  show active" id="reelReport-pane" role="tabpanel" tabindex="0">
                        <table class="table table-striped w-100" id="reelReportTable">
                            <thead>
                                <tr>
                                    <th style="width: 200px"> {{ __('reel')}} </th>
                                    <th> {{ __('reason')}} </th>
                                    <th> {{ __('description')}} </th>
                                    <th style="text-align: right; width: 350px;"> {{ __('action')}} </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
@endsection
