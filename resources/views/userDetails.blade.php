@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/user.js') }}"></script>
@endsection

@section('content')
<section class="section">

    <div class="card" id="reloadContent">
        <div class="card-header">
            <div class="page-title w-100">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="mb-0 fw-normal d-flex align-items-center">
                        {{ $user->full_name }}
                        @if ($user->is_verified == 2)
                        <img src="{{ asset('asset/image/verified.svg') }}" alt="verified" class="verified-badge">
                        @endif
                    </h4>
                    <div class="card-header-right d-flex align-items-center">
                        @if ($user->is_verified != 2)
                        <div class="verify-badge">
                            <a href="#" class="btn btn-primary px-4 text-white verifyUser" rel="{{ $user->id }}" data-tooltip="Verify user">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                                <span class="ms-2"> {{__('verifyUser')}} </span>
                            </a>
                        </div>
                        @endif

                        @if ($user->is_block == 0)
                        <div class="User-badge">
                            <a href="#" class="ms-3 btn btn-danger px-4 text-white blockUserBtn" rel="{{ $user->id }}" data-tooltip="Block user">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <line x1="18" y1="8" x2="23" y2="13"></line>
                                    <line x1="23" y1="8" x2="18" y2="13"></line>
                                </svg>
                                <span class="ms-2"> {{__('blockUser')}} </span>
                            </a>
                        </div>
                        @else
                        <div class="User-badge">
                            <a href="#" class="ms-3 btn btn-primary px-4 text-white unblockUserBtn" rel="{{ $user->id }}" data-tooltip="Block user">
                                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="8.5" cy="7" r="4"></circle>
                                    <polyline points="17 11 19 13 23 9"></polyline>
                                </svg>
                                <span class="ms-2">{{__('unblockUser')}} </span>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form id="editProfileForm" method="POST">
                <input type="hidden" id="user_id" value="{{ $user->id }}">
                <div class="profileDetailCard row">
                    <div class="col-lg-4">
                        <div class="profileDetailImages">
                            <div class="form-group w-100">
                                <div class="avatar-upload">
                                    <div class="d-flex avatar-edit">
                                        <div class="">
                                            <input type='file' name="background_image" id="imageUpload" accept=".png, .jpg, .jpeg" />
                                            <label for="imageUpload" class="btn btn-success px-3 py-1">
                                                {{__('edit')}}
                                            </label>
                                        </div>
                                        @if ($user->background_image != null)
                                        <div class="avatar-delete ms-2" rel="{{$user->id}}">
                                            <label class="btn btn-danger px-3 py-1">
                                                {{__('delete')}}
                                            </label>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="avatar-preview">
                                        @if ($user->background_image != null)
                                        <div id="imagePreview" style="background-image: url('{{ $user->background_image }}')"></div>
                                        @else
                                        <div id="imagePreview" style="background-image: url(../asset/image/default.png)"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="profilePicture">
                                    <div class="profilePictureMain">
                                        <div class="d-flex profile-edit">
                                            <div class="">
                                                <input type='file' name="profile" id="profileImageUpload" accept=".png, .jpg, .jpeg" />
                                                <label for="profileImageUpload" class="btn btn-success px-3 py-1">
                                                    {{__('edit')}}
                                                </label>
                                            </div>
                                            @if ($user->profile != null)
                                            <div class="profile-delete ms-2" rel="{{$user->id}}">
                                                <label class="btn btn-danger px-3 py-1">
                                                    {{__('delete')}}
                                                </label>
                                            </div>
                                            @endif
                                        </div>
                                        <div class="profile-preview">
                                            @if ($user->profile != null)
                                            <div id="imagePreviewProfile" style="background-image: url('{{ $user->profile }}')"></div>
                                            @else
                                            <div id="imagePreviewProfile" style="background-image: url('../asset/image/default.png')"></div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <div class="container-fluid p-0" id="userDetailReload">
                            <div class="row">
                                <div class="col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label> {{ __('identity') }}</label>
                                        <input type="text" name="identity" class="form-control" readonly value="{{ $user->identity }}">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label> {{ __('username') }}</label>
                                        <input type="text" name="username" class="form-control" value="{{ $user->username }}" readonly>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label> {{ __('fullname') }}</label>
                                        <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}">
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label> {{ __('bio') }}</label>
                                        <input type="text" name="bio" class="form-control" value="{{ $user->bio }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="align-items-end justify-content-between">
                                        <div class="otherDetails">
                                            <ul>
                                                @if ($user->followers != null)
                                                <li> {{ __('totalFollowers') }} : {{ $user->followers }} </li>
                                                @else
                                                <li>{{ __('totalFollowers') }} : 0 </li>
                                                @endif

                                                @if ($user->following != null)
                                                <li>{{ __('totalFollowing') }} : {{ $user->following }} </li>
                                                @else
                                                <li>{{ __('totalFollowing') }} : 0 </li>
                                                @endif

                                                @if ($user->device_type == 0)
                                                <li>{{ __('deviceType') }} : Android </li>
                                                @else
                                                <li>{{ __('deviceType') }} : iOS </li>
                                                @endif
                                            </ul>
                                        </div>

                                    </div>
                                    <div class="w-auto">
                                        <div class="text-left">
                                            <button type="submit" class="btn btn-success saveButton">{{ __('saveChanges') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="my-3 card-tab">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="allUserPostsTab" data-bs-toggle="tab" data-bs-target="#allUserPostsTab-pane" type="button" role="tab" aria-controls="allUserPostsTab-panel" aria-selected="true"> {{ __('posts')}} </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="userReelsTab" data-bs-toggle="tab" data-bs-target="#userReelsTab-pane" type="button" role="tab" aria-controls="userReelsTab-panel" aria-selected="true"> {{ __('reels')}} </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="userStoryTab" data-bs-toggle="tab" data-bs-target="#userStoryTab-pane" type="button" role="tab" aria-controls="userStoryTab-panel" aria-selected="true"> {{ __('stories')}} </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="userRoomsOwnTab" data-bs-toggle="tab" data-bs-target="#userRoomsOwnTab-pane" type="button" role="tab" aria-controls="userRoomsOwnTab-pane" aria-selected="false"> {{ __('roomsOwn')}} </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="userRoomInTab" data-bs-toggle="tab" data-bs-target="#userRoomInTab-pane" type="button" role="tab" aria-controls="userRoomInTab-panel" aria-selected="true"> {{ __('roomsIn')}} </button>
            </li>
        </ul>
    </div>

    <div class="tab-content" id="myTabContent">
        <div class="tab-pane show active" id="allUserPostsTab-pane" role="tabpanel" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal d-flex align-items-center"> {{ __('userPosts') }} </h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="userPostTable">
                        <thead>
                            <tr>
                                <th style="width: 150px"> {{ __('content') }} </th>
                                <th> {{ __('comments') }} </th>
                                <th> {{ __('likes') }} </th>
                                <th> {{ __('createdAt') }} </th>
                                <th> {{ __('restricted') }} </th>
                                <th style="text-align: right; width: 200px;"> {{ __('action') }} </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="userReelsTab-pane" role="tabpanel" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal d-flex align-items-center"> {{ __('userReels') }} </h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="userReelTable">
                        <thead>
                            <tr>
                                <th style="width: 150px"> {{ __('content') }} </th>
                                <th> {{ __('description') }} </th>
                                <th> {{ __('hashtags') }} </th>
                                <th> {{ __('counts') }} </th>
                                <th style="text-align: right; width: 200px;"> {{ __('action') }} </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="userStoryTab-pane" role="tabpanel" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal d-flex align-items-center"> {{ __('userStory') }} </h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="userStoryTable">
                        <thead>
                            <tr>
                                <th width="100px"> {{ __('content')}} </th>
                                <th width="100px"> {{ __('time')}} </th>
                                <th width="250px" style="text-align: right"> {{ __('action')}} </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="userRoomsOwnTab-pane" role="tabpanel" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal d-flex align-items-center"> {{ __('roomsOwn') }} </h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="userRoomsOwnTable">
                        <thead>
                            <tr>
                                <th width="100px"> {{ __('roomImage')}} </th>
                                <th> {{ __('title')}} </th>
                                <th> {{ __('totalMembers')}} </th>
                                <th> {{ __('joinRequestEnable')}} </th>
                                <th> {{ __('private')}} </th>
                                <th width="250px" style="text-align: right"> {{ __('action')}} </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="tab-pane" id="userRoomInTab-pane" role="tabpanel" tabindex="0">
            <div class="card">
                <div class="card-header">
                    <div class="page-title w-100">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0 fw-normal d-flex align-items-center"> {{ __('roomsIn') }} </h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped w-100" id="userRoomInTable">
                        <thead>
                            <tr>
                                <th style="width: 150px"> {{ __('roomImage') }} </th>
                                <th> {{ __('roomName') }} </th>
                                <th> {{ __('myType') }} </th>
                                <th style="text-align: right; width: 200px;"> {{ __('action') }} </th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<input type="hidden" name="" id="userId" value="{{$user->id}}">
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#imageUpload").change(function() {
        readURL(this);
    });

    function readURL1(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#imagePreviewProfile').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreviewProfile').hide();
                $('#imagePreviewProfile').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#profileImageUpload").change(function() {
        readURL1(this);
    });
</script>
@endsection