@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/faqs.js') }}"></script>
@endsection

@section('content')
<section class="section">
    <nav class="card-tab">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                {{ __('faqsList')}}
            </button>
            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                {{ __('faqsTypeList')}}
            </button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane  show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h4 class="mb-0 fw-normal d-flex align-items-center">
                        {{ __('faqs')}}
                    </h4>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addFAQsModal ">
                        {{ __('addFaqs') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="for-table-responsive">
                        <table class="table table-striped w-100" id="faqsTable">
                            <thead>
                                <tr>
                                    <th width="100%"> {{ __('faqs') }} </th>
                                    <th width="150px"> {{ __('faqsType') }} </th>
                                    <th style="text-align: right; width: 200px;"> {{ __('action') }} </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h4>{{ __('faqsType')}}</h4>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addFAQsTypeModal ">
                        {{ __('addFaqsType') }}
                    </button>
                </div>
                <div class="card-body">
                    <table class="table  table-striped w-100 " id="faqsTypeTable">
                        <thead>
                            <tr>
                                <th width="100%" class="FAQsType"> {{ __('faqsType') }} </th>
                                <th style="text-align: right; width: 200px !important;"> {{ __('action') }} </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- Add Faqs Modal  -->

<div class="modal fade" id="addFAQsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('addFaqs') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addFAQsForm" enctype="multipart/form-data" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('faqsType') }}</label>
                        <select name="faqs_type_id" id="faqs_type_id" class="form-control" required>
                            <option value="" selected disabled> {{ __('selectOne') }} </option>
                            @foreach($FAQsAllTypes as $FAQsAllType)
                            @if($FAQsAllType->is_deleted == 0)
                            <option value="{{ $FAQsAllType->id }}"> {{ $FAQsAllType->title }} </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="question" class="form-label">{{ __('question') }}</label>
                        <input type="question" name="question" id="question" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="answer" class="form-label">{{ __('answer') }}</label>
                        <textarea name="answer" id="answer" cols="30" rows="10" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                    <button type="submit" class="btn btn-primary text-white">{{ __('submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Edit Faqs Modal  -->
<div class="modal fade" id="editFAQsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('addFaqs') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFAQsForm" enctype="multipart/form-data" method="post">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="" id="faqsID">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('faqsType') }}</label>
                        <select name="faqs_type_id" id="edit_faqs_type_id" class="form-control" required>
                            <option value=""> {{ __('selectOne') }} </option>
                            @foreach($FAQsAllTypes as $FAQsAllType)
                            @if($FAQsAllType->is_deleted == 0)
                            <option value="{{ $FAQsAllType->id }}"> {{ $FAQsAllType->title }} </option>
                            @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="question" class="form-label">{{ __('question') }}</label>
                        <input type="question" name="question" id="editQuestion" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="answer" class="form-label">{{ __('answer') }}</label>
                        <textarea name="answer" id="editAnswer" cols="30" rows="10" class="form-control" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                    <button type="submit" class="btn btn-primary text-white">{{ __('submit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Faqs Type Modal  -->
<div class="modal fade" id="addFAQsTypeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('addFaqsType') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addFAQsTypeForm" enctype="multipart/form-data" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('faqsType') }}</label>
                        <input type="text" class="form-control" name="title" id="faqsType" required="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                    <button type="submit" class="btn btn-primary text-white saveButton">{{ __('save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit FAQs Type Modal -->
<div class="modal fade" id="editFaqsTypeModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('editFaqsType') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editFAQsTypeForm" method="post">
                <input type="hidden" name="" id="faqsTypeId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('title') }}</label>
                        <input type="text" class="form-control" name="title" id="editFaqsType" required="">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
                    <button type="submit" class="btn btn-primary saveButton1">{{ __('save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection