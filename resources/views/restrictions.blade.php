@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/restrictions.js') }}"></script>
@endsection
@section('content')
<div class="mb-3 card-tab">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item " role="presentation">
            <button class="nav-link active" id="username-restrictions-Tab" data-bs-toggle="tab" data-bs-target="#username-restrictions-pane" type="button" role="tab" aria-controls="username-restrictions-panel" aria-selected="true"> {{ __('username')}} </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="myTabContent">
    <div class="tab-pane show active" id="username-restrictions-pane" role="tabpanel" tabindex="0">
        <div class="card">
            <div class="card-header">
                <div class="page-title w-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 fw-normal"> {{ __('restrictions') }} </h4>
                        <button type="button" class="btn theme-bg theme-btn text-white" data-bs-toggle="modal" data-bs-target="#usernameRestrictionModal">
                            {{ __('addRestrictions') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped w-100" id="usernameRestrictionsTable">
                    <thead>
                        <tr>
                            <th>{{ __('title') }}</th>
                            <th width="250px" style="text-align: right"> {{ __('action')}} </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Username Restriction Modal -->
<div class="modal fade" id="usernameRestrictionModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('addUsernameToRestrict') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUsernameRestrictForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('title') }}</label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                    <button type="submit" class="btn theme-btn text-white saveButton">{{ __('save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Username Restriction Modal  -->
<div class="modal fade" id="editUsernameRestrictionModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('editUsernameToRestrict') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editUsernameRestrictionForm" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <input type="hidden" name="" id="usernameId">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('title') }}</label>
                        <input type="text" class="form-control" name="title" id="editTitle" required="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn modal-cancel-btn" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn theme-btn text-white saveButton1">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection