@extends('include.app')
@section('header')
    <script src="{{ asset('asset/script/room.js') }}"></script>
@endsection

@section('content')
 
    <div class="card">
        <div class="card-header">
            <div class="page-title w-100">
                <div class="d-flex justify-content-between">
                    <div class="d-flex justify-content-between w-50"> 
                        @if($room->photo != null)
                        <img src="{{  $room->photo }}" alt="" class="roomProfile">
                        @else
                        <img src="{{ asset('public/asset/image/default.png') }}" alt="" class="roomProfile">
                        @endif

                        <div class="d-flex w-100 justify-content-around" style="flex-direction: column;">
                          
                            <div class="room-description ml-3">
                                <h3 class="mb-0 fw-normal lh-1">{{ $room->title }}</h3>
                                <p class="fw-normal text-muted room-desc my-2">{{ $room->desc }}</p>
                            </div>
                           
                              <ul class="ml-3 room_interest mt-1"> 
                                @foreach(explode(',', $room->interest_ids) as $interest_id)
                                    @foreach($interests as $interest)
                                        @if($interest->id == $interest_id)
                                            <li class="badge badge-success fw-medium">{{$interest->title}}</li>
                                        @endif                                
                                    @endforeach
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="d-flex align-items-end justify-content-between" style="flex-direction: column;">
                        <div class="card-right">
                           
                            
                        <a href="#" class="mb-3 ml-3 btn btn-danger px-4 text-white delete deleteThisRoom d-flex align-items-center" rel="{{$room->id}}" data-tooltip="Delete This Room"> 
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 mr-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> Delete This Room  </a>
                           <div class="ml-3">
                                <p class="lh-1">
                                    {{ __('createdBy')}} : <a href="../usersDetail/{{$room->admin_id}}"> {{$room->user->full_name}}  </a>
                                </p>
                            </div>
                        </div>
                        <div class="">
                            <div class="ms-3 card-tab">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item " role="presentation">
                                        <button class="nav-link active" id="allRoomMembersTab" data-bs-toggle="tab"
                                            data-bs-target="#allRoomMembersTab-pane" type="button" role="tab"
                                            aria-controls="allRoomMembersTab-panel" aria-selected="true"> {{ __('all')}} </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="roomMembersTab" data-bs-toggle="tab"
                                        data-bs-target="#roomMembersTab-pane" type="button" role="tab"
                                        aria-controls="roomMembersTab-pane" aria-selected="false">  {{ __('roomMembers')}} </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link " id="roomCoAdminTab" data-bs-toggle="tab"
                                            data-bs-target="#roomCoAdminTab-pane" type="button" role="tab"
                                            aria-controls="roomCoAdminTab-panel" aria-selected="true">  {{ __('coAdmin')}} </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" name="room_id" value="{{$room->id}}" id="room_id">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane show active" id="allRoomMembersTab-pane" role="tabpanel" tabindex="0">
                    <table class="table table-striped w-100" id="allRoomUsersListTableWeb">
                        <thead>
                            <tr>
                                <th> {{ __('memberName')}} </th>
                                <th> {{ __('type')}} </th> 
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane" id="roomMembersTab-pane" role="tabpanel" tabindex="0">
                    <table class="table table-striped w-100" id="roomMembersListTableWeb">
                        <thead>
                                <tr>
                                <th> {{ __('memberName')}} </th>
                                <th> {{ __('type')}} </th> 
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="tab-pane" id="roomCoAdminTab-pane" role="tabpanel" tabindex="0">
                    <table class="table table-striped w-100" id="roomCoAdminTableWeb">
                        <thead>
                            <tr>
                                <th> {{ __('memberName')}} </th>
                                <th> {{ __('type')}} </th> 
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
           
        </div>
    </div>   
@endsection
