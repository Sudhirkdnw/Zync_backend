@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/interest.js') }}"></script>
@endsection

@section('content')
<section class="section">
    <div class="card">
        <div class="card-header">
            <div class="page-title w-100">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-normal">{{ __('interests') }}</h4>
                    <button type="button" class="btn theme-bg theme-btn text-white" data-bs-toggle="modal"
                        data-bs-target="#interestModal">
                        {{ __('addInterest') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-striped w-100" id="interestTable">
                <thead>
                    <tr>
                        <th>{{ __('title') }}</th>
                        <th width="250px" style="text-align: right">{{ __('action') }}</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>

<!-- Interest Modal -->
<div class="modal fade" id="interestModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('addInterest') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addInterestForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('title') }}</label>
                        <input type="text" class="form-control" name="title" id="interest" required="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('close') }}</button>
                    <button type="submit" class="btn theme-btn text-white saveButton">{{ __('save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Interest Modal --}}
<div class="modal fade" id="editInterestModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('editInterest') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editInterestForm" method="post">
                <input type="hidden" name="" id="interestID">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('title') }}</label>
                        <input type="text" class="form-control" name="title" id="editInterest" required="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('close') }}</button>
                    <button type="submit" class="btn theme-btn text-white saveButton1">{{ __('save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection