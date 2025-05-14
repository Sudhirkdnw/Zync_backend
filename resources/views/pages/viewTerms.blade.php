@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/viewTerms.js') }}"></script>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>{{ __('termsOfUse') }}</h4>
              <a href="termsOfUse" target="_blank" class="btn theme-btn text-white" style="padding: 3px 25px;">
                {{ __('preview') }}
            </a>
        </div>
        <div class="card-body">
            <form method="post" id="contentForm">
                @csrf
                <textarea class="summernote-simple" name="termsofuse" id="summernote">{{$data}}</textarea>
                <div class="modal-footer d-flex align-items-center justify-content-start p-0">
                    <button type="submit" class="btn theme-btn text-white">{{ __('submit') }} </button>
                </div>
            </form>
        </div>
    </div>
@endsection
