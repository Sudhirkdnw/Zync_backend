@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/index.js') }}"></script>
@endsection
@section('content')

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="header-title mb-0 fw-normal">{{ __('Total Number of Posts')}} : 
                        <span class="text-primary"> {{ $posts }} </span>
                    </h6>
                    <div class="toolbars toolbars-post apex-toolbar">
                        <button id="seven_days_post" class="btn btn-sm rounded btn-light">Last 7 Days </button>
                        <button id="thirty_days_post" class="btn btn-sm rounded btn-light active">1M</button>
                        <button id="all_posts" class="btn btn-sm rounded btn-light">ALL</button>
                    </div>
                </div>
                <div dir="ltr">
                    <div id="totalPostsChart" class="apex-charts"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="header-title mb-0 fw-normal">{{ __('Total Number of Users')}} : 
                        <span class="text-primary "> {{ $user }} </span>
                    </h6>
                    <div class="toolbars apex-toolbar">
                        <button id="seven_days" class="btn btn-sm rounded btn-light">Last 7 Days </button>
                        <button id="thirty_days" class="btn btn-sm rounded btn-light active">1M</button>
                        <button id="allUsers" class="btn btn-sm rounded btn-light">ALL</button>
                    </div>
                </div>
                <div dir="ltr">
                    <div id="totalUsersChart" class="apex-charts"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="header-title mb-0 fw-normal">{{ __('Total Number of Reels')}} : 
                        <span class="text-primary"> {{ $reels }} </span>
                    </h6>
                    <div class="toolbars toolbars-reel apex-toolbar">
                        <button id="seven_days_reel" class="btn btn-sm rounded btn-light">Last 7 Days </button>
                        <button id="thirty_days_reel" class="btn btn-sm rounded btn-light active">1M</button>
                        <button id="all_reels" class="btn btn-sm rounded btn-light">ALL</button>
                    </div>
                </div>
                <div dir="ltr">
                    <div id="totalReelsChart" class="apex-charts"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="header-title mb-0 fw-normal">{{ __('Total Number of Rooms')}} : 
                        <span class="text-primary"> {{ $rooms }} </span>
                    </h6>
                    <div class="toolbars toolbars-room apex-toolbar">
                        <button id="seven_days_room" class="btn btn-sm rounded btn-light">Last 7 Days </button>
                        <button id="thirty_days_room" class="btn btn-sm rounded btn-light active">1M</button>
                        <button id="all_rooms" class="btn btn-sm rounded btn-light">ALL</button>
                    </div>
                </div>
                <div dir="ltr">
                    <div id="totalRoomsChart" class="apex-charts"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="section dashboard-section">
    <div class="dashboard-cards">
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p>{{ $user }}</p>
                <div class="card-icon">
                    <i data-feather="users"></i>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('users') }}</h5>
                <a href="{{ route('users') }}">{{ __('viewMore') }}</a>
            </div>
        </div>
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p> {{ $posts }} </p>
                <div class="card-icon">
                    <i data-feather="image"></i>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('posts') }}</h5>
                <a href="{{ route('viewPosts')}}">{{ __('viewMore') }}</a>
            </div>
        </div>
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p> {{ $reels }} </p>
                <div class="card-icon">
                    <i data-feather="play-circle"></i>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('reels') }}</h5>
                <a href="{{ route('viewReels')}}">{{ __('viewMore') }}</a>
            </div>
        </div>
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p> {{ $music }} </p>
                <div class="card-icon">
                    <i data-feather="music"></i>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('music') }}</h5>
                <a href="{{ route('musics')}}">{{ __('viewMore') }}</a>
            </div>
        </div>
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p> {{ $rooms}} </p>
                <div class="card-icon">
                    <svg class="feather room_svg" width="22" height="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.8631 19.7708C13.2199 19.1051 13.2692 17.2662 11.9504 16.516C11.3905 16.1974 10.6024 16.1974 10.0424 16.516C9.07536 17.0661 8.78962 18.3137 9.42568 19.2083C9.73311 19.6407 10.446 20 10.9964 20C11.2618 20 11.553 19.923 11.8631 19.7708ZM14.7684 17.201C15.3186 16.9357 15.7622 16.4481 15.9905 15.8577C16.1416 15.4667 16.1634 15.2085 16.1634 13.8081C16.1634 11.9797 16.1054 11.77 15.5086 11.4388C15.0861 11.2045 14.6847 11.2005 14.2692 11.4265C13.749 11.7094 13.5799 12.093 13.5799 12.9905L13.5799 13.75L10.9964 13.75L8.41295 13.75L8.41295 12.9905C8.41295 12.093 8.24391 11.7094 7.72368 11.4265C7.30817 11.2005 6.90679 11.2045 6.4843 11.4388C5.88743 11.77 5.82948 11.9797 5.82948 13.8081C5.82948 15.6233 5.91869 16.0148 6.47621 16.6451C6.74575 16.9498 7.52484 17.4167 7.76381 17.4167C7.81445 17.4167 7.92235 17.2355 8.00364 17.014C8.68723 15.1513 11.1159 14.4449 12.8678 15.5991C13.3296 15.9033 13.7989 16.4955 13.9892 17.014C14.0705 17.2355 14.1784 17.4167 14.229 17.4167C14.2797 17.4167 14.5224 17.3196 14.7684 17.201ZM17.4982 10.2658C18.3002 9.90275 20.0228 8.87683 20.3565 8.56367C20.8585 8.09242 21.0845 7.54717 21.0821 6.8125C21.0809 6.48917 21.0493 6.19708 21.0114 6.1635C20.9737 6.12992 20.7297 6.13108 20.4692 6.16617C19.8995 6.24292 19.2341 6.15225 18.7054 5.926C18.1594 5.69225 17.4154 4.98267 17.1328 4.426C16.9187 4.00425 16.8956 3.87233 16.8977 3.08333C16.8998 2.29983 16.9241 2.16383 17.1298 1.78275C17.2561 1.54858 17.4667 1.23375 17.5978 1.083L17.8362 0.808834L17.5005 0.528085C17.1238 0.213001 16.4905 7.1044e-07 15.9304 6.61467e-07C15.3745 6.12871e-07 14.7941 0.2345 13.4077 1.01933C12.3987 1.5905 12.1211 1.78842 11.9617 2.05058C11.5094 2.79425 12.0307 3.77108 12.9429 3.8895C13.2283 3.9265 13.401 3.87233 14.0444 3.544C14.4641 3.32975 14.8383 3.18542 14.8761 3.22308C14.9748 3.32167 17.369 7.36075 17.369 7.42867C17.369 7.45983 17.0555 7.66042 16.6722 7.87433C16.0699 8.2105 15.9518 8.31483 15.8017 8.64325C15.3151 9.70758 16.4127 10.7573 17.4982 10.2658ZM5.53238 10.2994C6.19116 10.0217 6.48051 9.32392 6.20494 8.67725C6.06156 8.34083 5.95314 8.24133 5.35412 7.89675C4.9765 7.67958 4.65761 7.47292 4.64538 7.4375C4.62394 7.37533 6.97714 3.3625 7.11674 3.22308C7.15454 3.18542 7.5288 3.32975 7.94845 3.544C8.59182 3.87233 8.76457 3.9265 9.04995 3.8895C9.96252 3.771 10.4829 2.79517 10.0312 2.04925C9.869 1.78142 9.60023 1.59292 8.52206 0.9905C7.0847 0.187417 6.61727 -1.52709e-07 6.05166 -2.02156e-07C5.51981 -2.48652e-07 4.84681 0.231583 4.47513 0.542499L4.15668 0.808832L4.39505 1.083C4.52611 1.23375 4.73675 1.54858 4.86309 1.78275C5.06873 2.16383 5.09302 2.29983 5.09517 3.08333C5.09724 3.87233 5.07416 4.00425 4.86007 4.426C4.57744 4.98267 3.83348 5.69225 3.28742 5.926C2.75876 6.15225 2.09334 6.24292 1.52368 6.16617C1.26318 6.13108 1.01913 6.12992 0.981412 6.1635C0.943607 6.19708 0.911915 6.48917 0.910796 6.8125C0.907007 7.98558 1.35317 8.54125 3.07377 9.50625C4.77361 10.4595 4.9883 10.5287 5.53238 10.2994ZM20.4697 4.92533C22.2379 4.37892 22.5547 2.29067 20.9939 1.47017C20.5948 1.26033 20.4713 1.23575 19.9573 1.26392C19.2648 1.30175 18.796 1.55575 18.4296 2.09158C18.156 2.49167 18.0653 3.22467 18.2296 3.70667C18.3633 4.09867 18.7715 4.54958 19.1713 4.747C19.5447 4.93133 20.1716 5.01742 20.4697 4.92533ZM2.34178 4.92567C2.99937 4.76167 3.57049 4.27183 3.7633 3.70667C3.92761 3.22467 3.83684 2.49167 3.56325 2.09158C3.19683 1.55575 2.72801 1.30175 2.03556 1.26392C1.52153 1.23575 1.39804 1.26033 0.998979 1.47017C-0.373278 2.1915 -0.32135 4.05175 1.09043 4.74867C1.48528 4.94358 1.98595 5.01442 2.34178 4.92567Z" fill="white"></path>
                    </svg>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('rooms') }}</h5>
                <a href="{{ route('rooms')}}">{{ __('viewMore') }}</a>
            </div>
        </div>
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p> {{ $adminNotification}} </p>
                <div class="card-icon">
                    <i data-feather="bell"></i>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('notifications') }}</h5>
                <a href="{{ route('notification')}}">{{ __('viewMore') }}</a>
            </div>
        </div>
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p>{{ $interest }}</p>
                <div class="card-icon">
                    <i data-feather="heart"></i>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('interests') }}</h5>
                <a href="{{ route('interests')}}">{{ __('viewMore') }}</a>
            </div>
        </div>
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p>{{ $verificationRequests }}</p>
                <div class="card-icon">
                    <i data-feather="check-circle"></i>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('verificationRequests') }}</h5>
                <a href="{{ route('verificationRequests')}}">{{ __('viewMore') }}</a>
            </div>
        </div>
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p>{{ $report }}</p>
                <div class="card-icon">
                    <i data-feather="flag"></i>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('reports') }}</h5>
                <a href="{{ route('reports')}}">{{ __('viewMore') }}</a>
            </div>
        </div>
        <div class="dashboard-blog">
            <div class="dashboard-blog-content-top">
                <p> {{ $faqs}} </p>
                <div class="card-icon">
                    <i data-feather="help-circle"></i>
                </div>
            </div>
            <div class="dashboard-blog-content">
                <h5 class="fw-normal">{{ __('faqs') }}</h5>
                <a href="{{ route('faqs')}}">{{ __('viewMore') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection