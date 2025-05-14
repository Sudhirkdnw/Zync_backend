@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/verificationRequests.js') }}"></script>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <div class="page-title w-100">
            <div class="d-flex align-items-center justify-content-between">
                <h4 class="mb-0 fw-normal">{{ __('verificationRequests')}}</h4>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-striped w-100" id="profileVerificationTable">
            <thead>
                <tr>
                    <th> {{ __('userImage')}} </th>
                    <th> {{ __('selfie')}} </th>
                    <th> {{ __('document')}} </th>
                    <th> {{ __('documentType')}} </th>
                    <th> {{ __('fullname')}} </th>
                    <th> {{ __('username')}} </th>
                    <th width="250px" style="text-align: right"> {{ __('action')}} </th>
                </tr>
            </thead>
        </table>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="previewModalTitle"></h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="imagePreviewSub">
                    <img src="" alt="" id="imagePreview" width="100%">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection