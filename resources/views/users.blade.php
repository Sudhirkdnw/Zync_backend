@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/user.js') }}"></script>
@endsection

@section('content')
<section class="section">
    <nav class="card-tab">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-users-tab" data-bs-toggle="tab" data-bs-target="#nav-users" type="button" role="tab" aria-controls="nav-users" aria-selected="true">
                {{ __('allusers')}}
            </button>
            <button class="nav-link" id="nav-verifiedUser-tab" data-bs-toggle="tab" data-bs-target="#nav-verifiedUser" type="button" role="tab" aria-controls="nav-verifiedUser" aria-selected="false">
                {{ __('verifiedUser')}}
            </button>
            <button class="nav-link" id="nav-verifiedUserBySubscription-tab" data-bs-toggle="tab" data-bs-target="#nav-verifiedUserBySubscription" type="button" role="tab" aria-controls="nav-verifiedUserBySubscription" aria-selected="false">
                {{ __('verifiedUserBySubscription')}}
            </button>
            <button class="nav-link" id="nav-moderators-tab" data-bs-toggle="tab" data-bs-target="#nav-moderators" type="button" role="tab" aria-controls="nav-moderators" aria-selected="false">
                {{ __('moderators')}}
            </button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane show active" id="nav-users" role="tabpanel" aria-labelledby="nav-users-tab" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal d-flex align-items-center">{{ __('userList') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="userTable">
                        <thead>
                            <tr>
                                <th style="width: 100px"> {{ __('userImage') }}</th>
                                <th> {{ __('fullname') }} </th>
                                <th> {{ __('username') }} </th>
                                <th> {{ __('deviceType') }} </th>
                                <th> {{ __('moderator') }} </th>
                                <th style="text-align: right; width: 350px;">{{ __('action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="nav-verifiedUser" role="tabpanel" aria-labelledby="nav-verifiedUser-tab" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal d-flex align-items-center">{{ __('verifiedUser') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="verifiedUserTable">
                        <thead>
                            <tr>
                                <th style="width: 100px"> {{ __('userImage') }}</th>
                                <th> {{ __('fullname') }} </th>
                                <th> {{ __('username') }} </th>
                                <th> {{ __('deviceType') }} </th>
                                <th style="text-align: right; width: 350px;">{{ __('action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="nav-verifiedUserBySubscription" role="tabpanel" aria-labelledby="nav-verifiedUserBySubscription-tab" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal d-flex align-items-center">{{ __('verifiedUserBySubscription') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="verifiedUserBySubscriptionTable">
                        <thead>
                            <tr>
                                <th style="width: 100px"> {{ __('userImage') }}</th>
                                <th> {{ __('fullname') }} </th>
                                <th> {{ __('username') }} </th>
                                <th> {{ __('deviceType') }} </th>
                                <th style="text-align: right; width: 350px;">{{ __('action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="nav-moderators" role="tabpanel" aria-labelledby="nav-moderators-tab" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal d-flex align-items-center">{{ __('moderators') }}</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="moderatorsTable">
                        <thead>
                            <tr>
                                <th style="width: 100px"> {{ __('userImage') }}</th>
                                <th> {{ __('fullname') }} </th>
                                <th> {{ __('username') }} </th>
                                <th> {{ __('deviceType') }} </th>
                                <th> {{ __('moderator') }} </th>
                                <th style="text-align: right; width: 350px;">{{ __('action') }}</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection