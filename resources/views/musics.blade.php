@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/music.js') }}"></script>
@endsection

@section('content')
<section class="section">
    <nav class="card-tab">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                {{ __('music')}}
            </button>
            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">
                {{ __('categories')}}
            </button>
        </div>
    </nav>
    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h4 class="mb-0 fw-normal d-flex align-items-center">
                        {{ __('music')}}
                    </h4>
                    <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#addMusicModal ">
                        {{ __('addMusic') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="for-table-responsive">
                        <table class="table table-striped w-100" id="musicTable">
                            <thead>
                                <tr>
                                    <th> {{ __('music') }} </th>
                                    <th> {{ __('title') }} </th>
                                    <th> {{ __('category') }} </th>
                                    <th> {{ __('duration') }} </th>
                                    <th> {{ __('artist') }} </th>
                                    <th style="text-align: right; width: 200px;"> {{ __('action') }} </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h4>{{ __('categories')}}</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal ">
                        {{ __('addCategory') }}
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="categoryTable">
                        <thead>
                            <tr>
                                <th> {{ __('title') }} </th>
                                <th> {{ __('musicCount') }} </th>
                                <th style="text-align: right; width: 200px !important;"> {{ __('action') }} </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Modal  -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('addCategory') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close1"></button>
            </div>
            <form id="addCategoryForm" enctype="multipart/form-data" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('title') }}</label>
                        <input type="text" class="form-control" name="title" id="title" required="">
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

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('editCategory') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="post">
                <input type="hidden" name="" id="categoryId">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="form-label">{{ __('title') }}</label>
                        <input type="text" class="form-control" name="title" id="editCategory" required="">
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

<!-- Music Modal -->
<div class="modal fade" id="addMusicModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel"> {{ __('addMusic')}} </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addMusicForm" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label> {{ __('title') }}</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label> {{ __('image') }}</label>
                        <input type="file" name="image" class="form-control" required accept="image/*">
                    </div>
                    <div class="form-group">
                        <label> {{ __('category') }}</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="" selected disabled> {{ __('selectOne') }} </option>
                            @foreach($categories as $category)
                                @if($category->is_deleted == 0)
                                <option value="{{ $category->id }}"> {{ $category->title }} </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label> {{ __('music') }}</label>
                        <input type="file" name="sound" class="form-control" required accept=".mp3,audio/*">
                    </div>
                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label> {{ __('duration') }}</label>
                                <input type="text" name="duration" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> {{ __('artist') }}</label>
                                <input type="text" name="artist" class="form-control" required>
                            </div>
                        </div>
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

<!-- Edit Music Modal -->
<div class="modal fade" id="editMusicModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel"> {{ __('editMusic')}} </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editMusicForm" method="post">
                <div class="modal-body">
                    <input type="hidden" id="musicId" name="music_id" required>
                    <div class="form-group">
                        <label> {{ __('title') }}</label>
                        <input type="text" name="title" id="edit_music_title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label> {{ __('image') }}</label>
                        <input type="file" name="image" class="form-control" id="edit_music_image">
                    </div>
                    <div class="form-group">
                        <label> {{ __('category') }}</label>
                        <select name="category_id" id="edit_category_id" class="form-control">
                            <option value="" selected disabled> {{ __('selectOne') }} </option>
                            @foreach($categories as $category)
                                @if($category->is_deleted == 0)
                                <option value="{{ $category->id }}"> {{ $category->title }} </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label> {{ __('music') }}</label>
                        <input type="file" name="sound" id="edit_music_sound" class="form-control" accept=".mp3,audio/*">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> {{ __('duration') }}</label>
                                <input type="text" name="duration" id="edit_music_duration" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> {{ __('artist') }}</label>
                                <input type="text" name="artist" id="edit_music_artist" class="form-control">
                            </div>
                        </div>
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

 

@endsection