@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/notification.js') }}"></script>
@endsection

@section('content')
<section class="section">

    <div class="card">
        <div class="card-header">
            <div class="page-title w-100">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-normal"> {{ __('notifications') }} </h4>
                    <button type="button" class="btn theme-bg theme-btn text-white" data-bs-toggle="modal"
                        data-bs-target="#notificationModal">
                        {{ __('addNotification') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped w-100" id="NotificationTable">
                <thead>
                    <tr>
                        <th>{{ __('title') }}</th>
                        <th>{{ __('description') }}</th>
                        <th width="250px" style="text-align: right"> {{ __('action')}} </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

<!-- Notification Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel"> {{ __('addNotification')}} </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addNotificationForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label> {{ __('title') }}</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label> {{ __('description') }}</label>
                        <textarea type="text" name="description" class="form-control" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> {{ __('close') }}</button>
                    <button type="submit" class="btn theme-btn text-white saveButton"> {{ __('save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="editNotificationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel"> {{ __('editNotification')}} </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNotificationForm" method="post">
                <input type="hidden" id="notificationID">
                <div class="modal-body">
                    <div class="form-group">
                        <label> {{ __('title') }}</label>
                        <input type="text" name="title" class="form-control" id="editNotificationTitle" required>
                    </div>
                    <div class="form-group">
                        <label> {{ __('description') }}</label>
                        <textarea type="text" name="description" class="form-control" id="editNotificationDesc" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"> {{ __('close')}} </button>
                    <button type="submit" class="btn theme-btn text-white saveButton1">{{ __('save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection