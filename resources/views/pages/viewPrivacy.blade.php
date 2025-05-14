@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/viewPrivacy.js') }}"></script>
@endsection

@section('content')
    <div class="card mt-3">
        <div class="card-header">
            <h4>{{ __('privacyPolicy') }}</h4>
            <a href="privacyPolicy" target="_blank" class="btn theme-btn text-white" style="padding: 3px 25px;">
               {{ __('preview') }}
            </a>
        </div>
        <div class="card-body">

            <form method="post" id="contentForm">
                <textarea class="summernote-simple" name="privacy" id="summernote">{{$data}}</textarea>
                <div class="modal-footer d-flex align-items-center justify-content-start p-0">
                    <button type="submit" class="btn theme-btn text-white"> {{ __('submit') }} </button>
                </div>
            </form>
  
        </div>
    </div>
@endsection
