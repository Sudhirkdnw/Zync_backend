<!DOCTYPE html>
<html lang="en">


<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>{!! Session::get('app_name') !!}</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    @yield('header')

    <link href="{{ asset('asset/css/app.min.css') }}" rel="stylesheet">

    <link href="{{ asset('asset/css/bootstrap.min.css') }}" rel="stylesheet">

    <link rel='shortcut icon' type='image/x-icon' href="{{ asset('asset/img/favicon.png') }}" style="width: 2px !important;" />

    <link href="{{ asset('asset/bundles/codemirror/lib/codemirror.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/bundles/codemirror/theme/duotone-dark.css') }} " rel="stylesheet">
    <link href="{{ asset('asset/bundles/jquery-selectric/selectric.css') }}" rel="stylesheet">

    <script src="{{ asset('asset/cdnjs/iziToast.min.js') }}"></script>
    <script src="{{ asset('asset/cdnjs/sweetalert.min.js') }}"></script>

    <link href="{{ asset('asset/cdncss/iziToast.css') }}" rel="stylesheet" />
    <link href="{{ asset('asset/style/app.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />

    <link href="{{ asset('asset/css/components.css') }}" rel="stylesheet">
    <link href="{{ asset('asset/css/style.css') }}" rel="stylesheet">


</head>

<body>
    <!-- <div class="loader">
        <div class="lds-ring">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div> -->
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar sticky">
                <div class="ms-4">
                    @if (session('user_type') == 0)
                    <h5 class="mb-0 fw-normal">{{__('forTestingPurpose')}} ( {{__('adminPanel')}} ) </h5>
                    @else
                    <h5 class="mb-0 fw-normal"> {{ __('adminPanel') }}</h5>
                    @endif
                </div>
                <div class="form-inline mr-auto">
                    {{-- <ul class="navbar-nav mr-3">
                        <li>
                            <a href="#" data-toggle="sidebar" class="nav-link nav-link-lgcollapse-btn">
                                <i data-feather="align-justify"></i>
                            </a>
                        </li>
                    </ul> --}}
                </div>
                <ul class="navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <span class="d-lg-inline-block btn btn-light">
                                {{ __('logOut') }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right pullDown">
                            <a href="{{ route('logout') }}" class="dropdown-item has-icon text-danger">
                                <i data-feather="log-out"></i>
                                {{ __('logOut') }}
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand" id="reloadContentLogo">
                        <a href="{{ route('index') }}">
                            <span class="logo-name">{!! Session::get('app_name') !!}</span>
                        </a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="sideBarli indexSideA">
                            <a href="{{ route('index') }}" class="nav-link">
                                <i data-feather="home"></i>
                                <span> {{ __('dashboard') }} </span>
                            </a>
                        </li>

                        <li class="sideBarli userSideA">
                            <a href="{{ route('users') }}" class="nav-link">
                                <i data-feather="users"></i>
                                <span> {{ __('users') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli postSideA">
                            <a href="{{ route('viewPosts') }}" class="nav-link">
                                <i data-feather="image"></i>
                                <span> {{ __('posts') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli reelSideA">
                            <a href="{{ route('viewReels') }}" class="nav-link">
                                <i data-feather="play-circle"></i>
                                <span> {{ __('reels') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli musicSideA">
                            <a href="{{ route('musics') }}" class="nav-link">
                                <i data-feather="music"></i>
                                <span> {{ __('Music') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli storySideA">
                            <a href="{{ route('viewStories') }}" class="nav-link">
                                <i data-feather="disc"></i>
                                <span> {{ __('stories') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli roomSideA">
                            <a href="{{ route('rooms') }}" class="nav-link">
                                <svg class="feather room_svg" width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M11.8631 19.7708C13.2199 19.1051 13.2692 17.2662 11.9504 16.516C11.3905 16.1974 10.6024 16.1974 10.0424 16.516C9.07536 17.0661 8.78962 18.3137 9.42568 19.2083C9.73311 19.6407 10.446 20 10.9964 20C11.2618 20 11.553 19.923 11.8631 19.7708ZM14.7684 17.201C15.3186 16.9357 15.7622 16.4481 15.9905 15.8577C16.1416 15.4667 16.1634 15.2085 16.1634 13.8081C16.1634 11.9797 16.1054 11.77 15.5086 11.4388C15.0861 11.2045 14.6847 11.2005 14.2692 11.4265C13.749 11.7094 13.5799 12.093 13.5799 12.9905L13.5799 13.75L10.9964 13.75L8.41295 13.75L8.41295 12.9905C8.41295 12.093 8.24391 11.7094 7.72368 11.4265C7.30817 11.2005 6.90679 11.2045 6.4843 11.4388C5.88743 11.77 5.82948 11.9797 5.82948 13.8081C5.82948 15.6233 5.91869 16.0148 6.47621 16.6451C6.74575 16.9498 7.52484 17.4167 7.76381 17.4167C7.81445 17.4167 7.92235 17.2355 8.00364 17.014C8.68723 15.1513 11.1159 14.4449 12.8678 15.5991C13.3296 15.9033 13.7989 16.4955 13.9892 17.014C14.0705 17.2355 14.1784 17.4167 14.229 17.4167C14.2797 17.4167 14.5224 17.3196 14.7684 17.201ZM17.4982 10.2658C18.3002 9.90275 20.0228 8.87683 20.3565 8.56367C20.8585 8.09242 21.0845 7.54717 21.0821 6.8125C21.0809 6.48917 21.0493 6.19708 21.0114 6.1635C20.9737 6.12992 20.7297 6.13108 20.4692 6.16617C19.8995 6.24292 19.2341 6.15225 18.7054 5.926C18.1594 5.69225 17.4154 4.98267 17.1328 4.426C16.9187 4.00425 16.8956 3.87233 16.8977 3.08333C16.8998 2.29983 16.9241 2.16383 17.1298 1.78275C17.2561 1.54858 17.4667 1.23375 17.5978 1.083L17.8362 0.808834L17.5005 0.528085C17.1238 0.213001 16.4905 7.1044e-07 15.9304 6.61467e-07C15.3745 6.12871e-07 14.7941 0.2345 13.4077 1.01933C12.3987 1.5905 12.1211 1.78842 11.9617 2.05058C11.5094 2.79425 12.0307 3.77108 12.9429 3.8895C13.2283 3.9265 13.401 3.87233 14.0444 3.544C14.4641 3.32975 14.8383 3.18542 14.8761 3.22308C14.9748 3.32167 17.369 7.36075 17.369 7.42867C17.369 7.45983 17.0555 7.66042 16.6722 7.87433C16.0699 8.2105 15.9518 8.31483 15.8017 8.64325C15.3151 9.70758 16.4127 10.7573 17.4982 10.2658ZM5.53238 10.2994C6.19116 10.0217 6.48051 9.32392 6.20494 8.67725C6.06156 8.34083 5.95314 8.24133 5.35412 7.89675C4.9765 7.67958 4.65761 7.47292 4.64538 7.4375C4.62394 7.37533 6.97714 3.3625 7.11674 3.22308C7.15454 3.18542 7.5288 3.32975 7.94845 3.544C8.59182 3.87233 8.76457 3.9265 9.04995 3.8895C9.96252 3.771 10.4829 2.79517 10.0312 2.04925C9.869 1.78142 9.60023 1.59292 8.52206 0.9905C7.0847 0.187417 6.61727 -1.52709e-07 6.05166 -2.02156e-07C5.51981 -2.48652e-07 4.84681 0.231583 4.47513 0.542499L4.15668 0.808832L4.39505 1.083C4.52611 1.23375 4.73675 1.54858 4.86309 1.78275C5.06873 2.16383 5.09302 2.29983 5.09517 3.08333C5.09724 3.87233 5.07416 4.00425 4.86007 4.426C4.57744 4.98267 3.83348 5.69225 3.28742 5.926C2.75876 6.15225 2.09334 6.24292 1.52368 6.16617C1.26318 6.13108 1.01913 6.12992 0.981412 6.1635C0.943607 6.19708 0.911915 6.48917 0.910796 6.8125C0.907007 7.98558 1.35317 8.54125 3.07377 9.50625C4.77361 10.4595 4.9883 10.5287 5.53238 10.2994ZM20.4697 4.92533C22.2379 4.37892 22.5547 2.29067 20.9939 1.47017C20.5948 1.26033 20.4713 1.23575 19.9573 1.26392C19.2648 1.30175 18.796 1.55575 18.4296 2.09158C18.156 2.49167 18.0653 3.22467 18.2296 3.70667C18.3633 4.09867 18.7715 4.54958 19.1713 4.747C19.5447 4.93133 20.1716 5.01742 20.4697 4.92533ZM2.34178 4.92567C2.99937 4.76167 3.57049 4.27183 3.7633 3.70667C3.92761 3.22467 3.83684 2.49167 3.56325 2.09158C3.19683 1.55575 2.72801 1.30175 2.03556 1.26392C1.52153 1.23575 1.39804 1.26033 0.998979 1.47017C-0.373278 2.1915 -0.32135 4.05175 1.09043 4.74867C1.48528 4.94358 1.98595 5.01442 2.34178 4.92567Z" fill="white" />
                                </svg>
                                <span> {{ __('rooms') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli interestsSideA">
                            <a href="{{ route('interests') }}" class="nav-link ">
                                <i data-feather="heart"></i>
                                <span> {{ __('interests') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli restrictionsSideA">
                            <a href="{{ route('restrictions') }}" class="nav-link ">
                                <i data-feather="slash"></i>
                                <span> {{ __('restrictions') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli reportSideA">
                            <a href="{{ route('reports') }}" class="nav-link ">
                                <i data-feather="flag"></i>
                                <span> {{ __('reports') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli faqsSideA">
                            <a href="{{ route('faqs') }}" class="nav-link">
                                <i data-feather="help-circle"></i>
                                <span> {{ __('faqs') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli verificationRequestsSideA">
                            <a href="{{ route('verificationRequests') }}" class="nav-link ">
                                <i data-feather="check-circle"></i>
                                <span> {{ __('verifications') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli notificationSideA">
                            <a href="{{ route('notification') }}" class="nav-link">
                                <i data-feather="bell"></i>
                                <span> {{ __('notifications') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli admobSideA">
                            <a href="{{ route('admob') }}" class="nav-link">
                                <i data-feather="activity"></i>
                                <span> {{ __('admob') }} </span>
                            </a>
                        </li>
                        <li class="sideBarli settingSideA">
                            <a href="{{ route('setting') }}" class="nav-link">
                                <i data-feather="settings"></i>
                                <span> {{ __('setting') }} </span>
                            </a>
                        </li>
                        <hr>
                        <li class="sideBarli privacySideA">
                            <a href="{{ route('viewPrivacy') }}" class="nav-link">
                                <i data-feather="shield"></i>
                                <span>{{ __('privacyPolicy') }}</span>
                            </a>
                        </li>

                        <li class="sideBarli termsSideA">
                            <a href="{{ route('viewTerms') }}" class="nav-link">
                                <i data-feather="clipboard"></i>
                                <span>{{ __('termsOfUse') }}</span>
                            </a>
                        </li>
                    </ul>
                </aside>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                @yield('content')
                <form action="">
                    <input type="hidden" id="user_type" value="{{ session('user_type') }}">
                </form>
            </div>
        </div>
    </div>

    <!-- View Post Modal -->
    <div class="modal fade" id="viewPostModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl post-modal-dialog">
            <div class="modal-content post-modal-content">
                <div class="modal-header border-0" style="position: absolute; right: 0; top: 0;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="post-modal-body">
                    <div class="modal-left">
                        <div class="form-group position-relative m-0 h-100">
                            <div class="swiper-container mySwiper shadow-none">
                                <div id="post_contents"></div>
                            </div>
                            <div class="swiper-button swiper-button-prev"></div>
                            <div class="swiper-button swiper-button-next"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                    <div class="comment-show-right">
                        <div id="user-data">
                            <a href="" target="" id="postUserDataId" class="d-flex align-items-center mb-2 text-dark text-decoration-none">
                                <div class="post-modal-user-profile mr-2">
                                    <img src="" alt="" id="postUserProfile">
                                </div>
                                <div id="postUserName"></div>
                            </a>
                            <div class="form-group border-bottom m-0">
                                <p id="postDesc" class="m-0"> </p>
                            </div>
                            <div id="image-post-interests"></div>
                        </div>
                        <div class="comment-content-div">
                            <div id="comment-content"></div>
                            <div id="no_comments" style="display: none;">
                                <h3 class="text-muted fw-medium">No comments yet.</h3>
                            </div>
                            <div class="d-block text-center">
                                <div id="loadMoreButton" class="loadMoreButton" data-tooltip="Load more comments">
                                    <i data-feather="plus-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Post Desc Modal -->
    <div class="modal fade" id="viewPostDescModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md post-modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0" style="position: absolute; right: 0; top: 0;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="post-modal-body">
                    <div class="comment-show-right w-100">
                        <div id="user-data1">
                            <a href="" target="" id="postUserDataId1" class="d-flex align-items-center mb-2 text-dark text-decoration-none">
                                <div class="post-modal-user-profile mr-2">
                                    <img src="" alt="" id="postUserProfile1">
                                </div>
                                <div id="postUserName1"></div>
                            </a>
                            <div class="form-group border-bottom m-0">
                                <p id="postDesc1"> </p>
                            </div>
                            <div id="desc-post-interests"></div>
                        </div>
                        <div class="comment-content-div1 ">
                            <div id="comment-content1"></div>
                            <div id="no_comments1" class="mt-3" style="display: none;">
                                <h3 class="text-muted fw-medium">No comments yet.</h3>
                            </div>
                            <div class="d-block text-center">
                                <div id="loadMoreButton1" class="loadMoreButton" data-tooltip="Load more comments">
                                    <i data-feather="plus-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Audio Post Modal -->
    <div class="modal fade" id="viewAudioPostModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md post-modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0" style="position: absolute; right: 0; top: 0;">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="post-modal-body">
                    <div class="comment-show-right w-100">
                        <div id="user-data2">
                            <a href="" target="" id="postUserDataId2" class="d-flex align-items-center mb-2 text-dark text-decoration-none">
                                <div class="post-modal-user-profile mr-2">
                                    <img src="" alt="" id="postUserProfile2">
                                </div>
                                <div id="postUserName2"></div>
                            </a>
                            <div class="form-group position-relative m-0 border-bottom">
                                <p id="postAudioDesc"></p>
                                <div id="post_contents_audio"></div>
                            </div>
                            <div id="audio-post-interests"></div>
                        </div>
                        <div class="comment-content-div2">
                            <div id="comment-content2"></div>
                            <div id="no_comments2" class="mt-3" style="display: none;">
                                <h3 class="text-muted fw-medium">No comments yet.</h3>
                            </div>
                            <div class="d-block text-center">
                                <div id="loadMoreButton2" class="loadMoreButton" data-tooltip="Load more comments">
                                    <i data-feather="plus-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Story Modal -->
    <div class="modal fade" id="viewStoryModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('viewStory') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group position-relative m-0">
                        <img src="" alt="" srcset="" id="story_content" class="img-fluid story_content_view">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Story Modal -->
    <div class="modal fade" id="viewStoryVideoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('viewStoryVideo') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group position-relative m-0">
                        <video controls id="story_content_video" class="img-fluid story_content_view">

                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
<!-- View Reel Modal -->
<div class="modal fade" id="viewReelModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content h-100">
            <div class="modal-header border-0" style="position: absolute; right: 0; top: 0;">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="post-modal-body">
                <div class="reel-show-left">
                    <div id="reel_view_section">
                        <video id="reel_contents" controls autoplay></video>
                    </div>
                </div>
                <div class="comment-show-right">
                    <div id="user-data">
                        <a href="" target="" id="reelUserId" class="d-flex align-items-center mb-2 text-dark text-decoration-none">
                            <div class="post-modal-user-profile mr-2">
                                <img src="" alt="" id="reelUserProfile">
                            </div>
                            <div id="username"></div>
                        </a>
                        <div class="border-bottom">
                            <p id="reel_description"> </p>
                        </div>
                    </div>

                    <div id="reel-interests" class="py-2 border-bottom"></div>
                    <div id="reel-product-data"></div>

                    <div class="comment-content-div" data-simplebar>
                        <div id="comment-content3"></div>
                        <div id="no_comments3" class="text-center" style="display: none;">
                            <h3 class="text-muted fw-medium">No comments yet.</h3>
                        </div>
                        <div class="d-block text-center">
                            <div id="loadMoreButton3" class="loadMoreButton" data-tooltip="Load more comments">
                                <i data-feather="plus-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <input type="hidden" value="{{ env('APP_URL')}}" id="appUrl">
    <script src="{{ asset('asset/script/env.js') }}"></script>

    <script src="{{ asset('asset/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('asset/js/app.min.js ') }}"></script>
    <script src="{{ asset('asset/bundles/datatables/datatables.min.js ') }}"></script>
    <script src="{{ asset('asset/bundles/jquery-ui/jquery-ui.min.js ') }}"></script>
    <script src="{{ asset('asset/js/page/datatables.js') }}"></script>
    <script src="{{ asset('asset/js/scripts.js') }}"></script>
    <script src="{{ asset('asset/script/app.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <!-- Custom JS File -->
    <script src="{{ asset('asset/bundles/summernote/summernote-bs4.js ') }}"></script>
    <script>
        $('#app_name').keyup(function() {
            let appName = $(this).val();
            $('.logo-name').text(appName);
            $('.logo-name-small').text(appName);
            document.title = appName;
        });
    </script>

    <script type="module">
        import {
            app,
            db
        } from "{{ asset('asset/script/firebase-init.js') }}";

        import {
            getFirestore,
            doc,
            deleteDoc
        } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-firestore.js";


        $(document).ready(function() {

            const db = getFirestore(app);

            async function deleteRoomFromFirebase(roomId) {
                const fullRoomId = "room_" + roomId;
                try {
                    const docRef = doc(db, "chats", fullRoomId);
                    await deleteDoc(docRef);
                    console.log("Room deleted from Firebase:", fullRoomId);

                } catch (error) {
                    console.error("Failed to delete room from Firebase:", error);
                }
            }

            $(document).on("click", ".deleteRoomByAdmin", function(e) {
                e.preventDefault();
                if (user_type == 1) {
                    var id = $(this).attr("rel");
                    console.log(id);
                    swal({
                        title: "Are you sure?",
                        icon: "error",
                        buttons: true,
                        dangerMode: true,
                        buttons: ["Cancel", "Yes"],
                    }).then((deleteValue) => {
                        if (deleteValue) {
                            if (deleteValue == true) {
                                $.ajax({
                                    type: "POST",
                                    url: `${domainUrl}deleteThisRoom`,
                                    dataType: "json",
                                    data: {
                                        room_id: id,
                                    },
                                    success: async function(response) {
                                        if (response.status == false) {
                                            console.log(response.message);
                                        } else if (response.status == true) {
                                            await deleteRoomFromFirebase(id);

                                            $("#roomsListTable").DataTable().ajax.reload(null, false);
                                            $("#userRoomsOwnTable").DataTable().ajax.reload(null, false);
                                            $("#userRoomInTable").DataTable().ajax.reload(null, false);
                                            iziToast.show({
                                                title: "Success",
                                                message: "Room Deleted Successfully.",
                                                color: "green",
                                                position: toastPosition,
                                                transitionIn: "fadeInUp",
                                                transitionOut: "fadeOutDown",
                                                timeout: 3000,
                                                animateInside: true,
                                                iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                            });


                                        }
                                    },
                                });
                            }
                        }
                    });
                } else {
                    iziToast.show({
                        title: "Oops",
                        message: "You are tester",
                        color: "red",
                        position: toastPosition,
                        transitionIn: "fadeInUp",
                        transitionOut: "fadeOutDown",
                        timeout: 3000,
                        animateInside: false,
                        iconUrl: `${domainUrl}asset/img/x.svg`,
                    });
                }
            });


            $(document).on("click", ".deleteThisRoom", function(e) {
                e.preventDefault();
                if (user_type == 1) {
                    var id = $(this).attr("rel");
                    swal({
                        title: "Are you sure?",
                        icon: "error",
                        buttons: true,
                        dangerMode: true,
                        buttons: ["Cancel", "Yes"],
                    }).then((deleteValue) => {
                        if (deleteValue) {
                            if (deleteValue == true) {
                                $.ajax({
                                    type: "POST",
                                    url: `${domainUrl}deleteThisRoom`,
                                    dataType: "json",
                                    data: {
                                        room_id: id,
                                    },
                                    success: async function(response) {
                                        if (response.status == false) {
                                            console.log(response.message);
                                        } else if (response.status == true) {
                                            await deleteRoomFromFirebase(id);
                                            window.location.replace("../rooms");
                                        }
                                    },
                                });
                            }
                        }
                    });
                } else {
                    iziToast.show({
                        title: "Oops",
                        message: "You are tester",
                        color: "red",
                        position: toastPosition,
                        transitionIn: "fadeInUp",
                        transitionOut: "fadeOutDown",
                        timeout: 3000,
                        animateInside: false,
                        iconUrl: `${domainUrl}asset/img/x.svg`,
                    });
                }
            });
        });
    </script>
</body>

</html>