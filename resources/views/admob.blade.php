@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/admob.js') }}"></script>
@endsection

@section('content')

<div class="text-right mb-4" id="admob_switch">
    @if ($setting->is_admob_on == 1)
    <div class="onOffSwitch d-flex align-items-center">
        <label class="switch m-0">
            <input type="checkbox" name="is_admob_on" id="is_admob_on" value="1" checked>
            <span class="slider"></span>
        </label>
        <p class="fw-normal fs-6 mb-0 ms-2" id="admob_text">
            {{ __('admobIsOn')}}
        </p>
    </div>
    @else
    <div class="onOffSwitch d-flex align-items-center">
        <label class="switch m-0">
            <input type="checkbox" name="is_admob_on" id="is_admob_on" value="0">
            <span class="slider"></span>
        </label>
        <p class="fw-normal fs-6 mb-0 ms-2" id="admob_text">
            {{ __('admobIsOff')}}
        </p>
    </div>
    @endif

</div>
<section class="section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('admobAndroid') }}</h1>
                    </div>
                    <div class="card-body px-4">
                        <form id="admobAndroidForm" method="post">
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label for="ad_banner_android" class="form-label">{{ __('bannerId') }}</label>
                                        <input type="text" class="form-control" name="ad_banner_android" required="" value="{{ $setting->ad_banner_android }}">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label for="title" class="form-label">{{ __('intersialId') }}</label>
                                        <input type="text" class="form-control" name="ad_interstitial_android" required="" value="{{ $setting->ad_interstitial_android }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer px-0">
                                <button type="submit" class="btn theme-btn text-white saveButton2">{{ __('save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('admobiOS') }}</h1>
                    </div>
                    <div class="card-body px-4">
                        <form id="admobiOSForm" method="post">
                            <input type="hidden" name="type" value="2">
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label for="title" class="form-label">{{ __('bannerId') }}</label>
                                        <input type="text" class="form-control" name="ad_banner_iOS" required="" value="{{ $setting->ad_banner_iOS }}">
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label for="title" class="form-label">{{ __('intersialId') }}</label>
                                        <input type="text" class="form-control" name="ad_interstitial_iOS" required="" value="{{ $setting->ad_interstitial_iOS }}">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer px-0">
                                <button type="submit" class="btn theme-btn text-white saveButton3">{{ __('save') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@endsection