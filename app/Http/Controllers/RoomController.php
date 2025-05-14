<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use App\Models\GlobalFunction;
use App\Models\Interest;
use App\Models\Report;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\SavedNotification;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function createRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'desc' => 'required',
            'interest_ids' => 'required',
            'is_private' => 'required',
            'is_join_request_enable' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $userData = User::where('id', $request->admin_id)->first();

        if ($userData) {
            $room = new Room();
            $room->admin_id = (int) $request->admin_id;
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $path = GlobalFunction::saveFileAndGivePath($file);
                $room->photo = $path;
            }

            $room->title = $request->title;
            $room->desc = $request->desc;
            $room->interest_ids = $request->interest_ids;
            $room->is_private = (int) $request->is_private;
            $room->is_join_request_enable = (int) $request->is_join_request_enable;
            $room->total_member += 1;
            $room->save();

            $roomUser = new RoomUser();
            $roomUser->room_id = $room->id;
            $roomUser->user_id = $room->admin_id;
            $roomUser->type = 5;
            $roomUser->save();

            return response()->json([
                'status' => true,
                'message' => 'Room Created Successfully',
                'data' => $room,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function editRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $room = Room::where('id', $request->room_id)->first();
        if ($room) {
            if ($request->hasFile('photo')) {
                $path = GlobalFunction::deleteFile($room->photo);
                $file = $request->file('photo');
                $path = GlobalFunction::saveFileAndGivePath($file);
                $room->photo = $path;
            }
            if ($request->has('title')) {
                $room->title = $request->title;
            }
            if ($request->has('desc')) {
                $room->desc = $request->desc;
            }
            if ($request->has('interest_ids')) {
                $room->interest_ids = $request->interest_ids;
            }
            if ($request->has('is_private')) {
                $room->is_private = (int) $request->is_private;
            }
            if ($request->has('is_join_request_enable')) {
                $room->is_join_request_enable = (int) $request->is_join_request_enable;
            }
            $room->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Room edit successfully',
            'data' => $room,
        ]);
    }

    public function inviteUserToRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)->where('id', $request->user_id)->first();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
        $roomRequest = Room::with('user')->where('id', $request->room_id)->first();
        if ($roomRequest == null) {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        }

        $roomAdmin = Room::where('id', $request->room_id)->where('admin_id', $request->user_id)->first();
        if($roomAdmin != null) {
            return response()->json([
                'status' => false,
                'message' => 'Lol, You are the Admin of the room',
            ]);
        }

        if ($user && $roomRequest) {
            $roomRequestData = RoomUser::where('room_id', $request->room_id)
                                        ->where('user_id', $request->user_id)
                                        ->first();

            if ($roomRequestData != null) {
                return response()->json([
                    'status' => false,
                    'message' => 'User record already exists',
                ]);
            }

            $roomUser = RoomUser::where('room_id', $request->room_id)
                                ->where(function($query){
                                    $query->where('type', 2)
                                        ->orWhere('type', 3)
                                        ->orWhere('type', 5);
                                })->count();

            $setting = Setting::first();

            if ($roomUser < $setting->setRoomUsersLimit) {

                $invitedByAdmin = 4;

                $roomUser = new RoomUser;
                $roomUser->room_id = (int) $request->room_id;
                $roomUser->user_id = (int) $request->user_id;
                $roomUser->invited_by = (int) $roomRequest->admin_id;
                $roomUser->type = $invitedByAdmin;
                $roomUser->save();

                $fromUser = $roomRequest->admin_id;

                if ($fromUser != $roomUser->user_id) {
                    if($user->is_push_notifications == 1) {
                        $notificationDesc = $roomRequest->user->full_name . ' has invited to room : ' . $roomRequest->title;
                        GlobalFunction::sendPushNotificationToUser($notificationDesc, $user->device_token, $user->device_type);
                    }
                }

                $roomUser->room = $roomRequest;

                $type = Constants::notificationTypeInviteRoom;

                $savedNotification = new SavedNotification();
                $savedNotification->my_user_id = (int) $request->user_id;
                $savedNotification->user_id = (int) $roomUser->room->admin_id;
                $savedNotification->room_id = (int) $request->room_id;
                $savedNotification->type = $type;
                $savedNotification->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Request Send',
                    'data' => $roomUser,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Room users limit reached',
                ]);
            }
        }
    }

    public function joinOrRequestRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)->where('id', $request->user_id)->first();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
        $roomRequest = Room::where('id', $request->room_id)->first();
        if ($roomRequest == null) {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        }

        if ($roomRequest) {
            $roomRequestData = RoomUser::where('room_id', $request->room_id)->where('user_id', $request->user_id)->first();

            if ($roomRequestData != null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Already in room users list',
                ]);
            }

            if ($roomRequest->is_join_request_enable == 1) {
                $roomUser = RoomUser::where('room_id', $request->room_id)
                                    ->where(function($query){
                                        $query->where('type', 2)
                                        ->orWhere('type', 3);
                                    })->count();

                $setting = Setting::get()->first();

                if ($roomUser < $setting->setRoomUsersLimit) {
                    $requestToJoin = 1;
                    $roomUser = new RoomUser();
                    $roomUser->room_id = (int) $request->room_id;
                    $roomUser->user_id = (int) $request->user_id;
                    $roomUser->type = $requestToJoin;
                    $roomUser->save();


                    if ($user->id != $roomRequest->admin_id) {
                        if($roomRequest->user->is_push_notifications == 1 && $roomRequest->user->device_token != null) {
                            $notificationDesc = $user->full_name . ' has requested to join your room : ' . $roomRequest->title;
                            GlobalFunction::sendPushNotificationToUser($notificationDesc, $roomRequest->user->device_token, $roomRequest->user->device_type);
                        }
                    }

                    $roomUser->room = $roomRequest;

                    $type = Constants::notificationTypejoinRoom;

                    $savedNotification = new SavedNotification();
                    $savedNotification->my_user_id = (int) $roomUser->room->admin_id;
                    $savedNotification->user_id = (int) $request->user_id;
                    $savedNotification->room_id = (int) $request->room_id;
                    $savedNotification->type = $type;
                    $savedNotification->save();


                    return response()->json([
                        'status' => true,
                        'message' => 'Request Send',
                        'data' => $roomUser,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Room users limit reached',
                    ]);
                }
            } else {
                $roomUser = RoomUser::where('room_id', $request->room_id)
                                    ->where(function($query){
                                    $query->where('type', 2)
                                        ->orWhere('type', 3)
                                        ->orWhere('type', 5);
                                    })->count();
                $setting = Setting::get()->first();

                if ($roomUser < $setting->setRoomUsersLimit) {
                    $directJoin = 2;
                    $roomUser = new RoomUser;
                    $roomUser->room_id = (int) $request->room_id;
                    $roomUser->user_id = (int) $request->user_id;
                    $roomUser->type = $directJoin;
                    $roomUser->save();

                    $roomTotalUserCount = Room::where('id', $request->room_id)->first();
                    $roomTotalUserCount->total_member += 1;
                    $roomTotalUserCount->save();

                    if ($user->id != $roomRequest->admin_id) {
                        if ($roomRequest->user->is_push_notifications == 1 && $roomRequest->user->device_token != null) {
                            $notificationDesc = $user->full_name . ' has joined your room : ' . $roomRequest->title;
                            GlobalFunction::sendPushNotificationToUser($notificationDesc, $roomRequest->user->device_token, $roomRequest->user->device_type);
                        }
                    }

                    $roomUser->room = $roomRequest;

                    $type = Constants::notificationTypeDirectjoinRoom;

                    $savedNotification = new SavedNotification();
                    $savedNotification->my_user_id = (int) $roomUser->room->admin_id;
                    $savedNotification->user_id = (int) $request->user_id;
                    $savedNotification->room_id = (int) $request->room_id;
                    $savedNotification->type = $type;
                    $savedNotification->save();

                    return response()->json([
                        'status' => true,
                        'message' => 'Great, You are in the room',
                        'data' => $roomUser,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Room users limit reached',
                    ]);
                }
            }
        }
    }

    public function getInvitationList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }


        $getInvitationList = RoomUser::where('user_id', $request->user_id)
            ->where('type', 4)
            ->with(['room', 'invited_user'])
            ->offset($request->start)
            ->limit($request->limit)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Get All Room Requests (Invitation)',
            'data' => $getInvitationList,
        ]);
    }

    public function acceptInvitation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)->where('id', $request->user_id)->first();
        $userId = RoomUser::where('user_id', $request->user_id)->first();

        if ($user == null || $userId == null) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
        $roomRequest = Room::with('user')->where('id', $request->room_id)->first();
        if ($roomRequest == null) {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        }

        $acceptRequest = RoomUser::where('room_id', $request->room_id)
                                ->where('user_id', $request->user_id)
                                ->first();

        if ($acceptRequest->type == 2) {
            return response()->json([
                'status' => false,
                'message' => 'Invitation Already Accepted',
            ]);
        }

        if ($acceptRequest) {
            $invitedByUser = User::where('id', $acceptRequest->invited_by)->first();

            if ($acceptRequest->type == 1 || $acceptRequest->type == 4) {
                $acceptRequest->type = 2;

                $userCount = Room::where('id', $request->room_id)->first();
                $userCount->total_member += 1;
                $userCount->save();

                $acceptRequest->save();

                if ($user->id != $invitedByUser->invited_by) {
                    if($invitedByUser->is_push_notifications == 1) {
                        $notificationDesc = $user->full_name . ' has accepted your invitation of room : '. $roomRequest->title ;
                        GlobalFunction::sendPushNotificationToUser($notificationDesc, $invitedByUser->device_token, $invitedByUser->device_type);
                    }
                }

                $acceptRequest->room = $roomRequest;

                $type = Constants::notificationTypeAcceptInvitationRoom;

                $savedNotification = new SavedNotification();
                $savedNotification->my_user_id = (int) $acceptRequest->room->admin_id;
                $savedNotification->user_id = (int) $request->user_id;
                $savedNotification->room_id = (int) $request->room_id;
                $savedNotification->type = $type;
                $savedNotification->save();


                return response()->json([
                    'status' => true,
                    'message' => 'Accept Invitation',
                    'data' => $acceptRequest,
                ]);


            }


        }
    }

    public function acceptRoomRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)->where('id', $request->user_id)->first();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
        $roomRequest = Room::where('id', $request->room_id)->first();
        if ($roomRequest == null) {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        }

        $acceptRoomRequest = RoomUser::where('room_id', $request->room_id)
                                    ->where('user_id', $request->user_id)
                                    ->first();
        if ($acceptRoomRequest == null) {
            return response()->json([
                'status' => false,
                'message' => 'Record Not found',
            ]);
        }

        if ($acceptRoomRequest->type == 2) {
            return response()->json([
                'status' => false,
                'message' => 'Invitation Already Accepted',
            ]);
        }

        if ($acceptRoomRequest) {
            $roomUser = RoomUser::where('room_id', $request->room_id)
                                ->where(function($query){
                                $query->where('type', 2)
                                    ->orWhere('type', 3)
                                    ->orWhere('type', 5);
                                })->count();
            $setting = Setting::get()->first();
            if ($roomUser < $setting->setRoomUsersLimit) {

                $roomUser = RoomUser::where('id', $acceptRoomRequest->id)->first();
                $roomUser->type = 2;
                $roomRequest->total_member += 1;
                $roomRequest->save();
                $roomUser->save();


                if ($roomRequest->admin_id != $user->id) {
                    if($user->is_push_notifications == 1) {
                        $notificationDesc = $roomRequest->user->full_name . ' has accepted your join request of room : '. $roomRequest->title ;
                        GlobalFunction::sendPushNotificationToUser($notificationDesc, $user->device_token, $user->device_type);
                    }
                }

                $roomUser->room = $roomRequest;

                $userNotification = SavedNotification::where('room_id', $request->room_id)
                                                    ->where('user_id', $request->user_id)
                                                    ->where('type', Constants::notificationTypejoinRoom)
                                                    ->first();
                $userNotification->delete();


                $type = Constants::notificationTypeAcceptRoomRequest;

                $savedNotification = new SavedNotification();
                $savedNotification->my_user_id = (int) $request->user_id;
                $savedNotification->user_id = (int) $roomUser->room->admin_id;
                $savedNotification->room_id = (int) $request->room_id;
                $savedNotification->type = $type;
                $savedNotification->save();



                return response()->json([
                    'status' => true,
                    'message' => 'Accept Room request',
                    'data' => $roomUser,
                ]);
            } else {
                    return response()->json([
                    'status' => false,
                    'message' => 'Room users limit reached',
                ]);
            }
        }
    }

    public function rejectInvitation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)->where('id', $request->user_id)->first();
        $userId = RoomUser::where('user_id', $request->user_id)->first();

        if ($user == null || $userId == null) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
        $roomRequest = Room::where('id', $request->room_id)->first();
        if ($roomRequest == null) {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        }

        $rejectInvitation = RoomUser::where('room_id', $request->room_id)
            ->where('user_id', $request->user_id)
            ->get()
            ->first();

        if ($rejectInvitation) {
            $roomUser = RoomUser::where('id', $rejectInvitation->id)
                ->get()
                ->first();

            if ($roomUser != null) {
                $roomUser->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Reject Invitation',
                    'data' => $roomUser,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'This User is not In your Room list',
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'Record not found',
        ]);
    }

    public function rejectRoomRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $user = User::where('is_block', 0)->where('id', $request->user_id)->first();
        $userId = RoomUser::where('user_id', $request->user_id)->first();
        if ($user == null || $userId == null) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
        $roomRequest = Room::where('id', $request->room_id)->first();
        if ($roomRequest == null) {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        }

        $rejectInvitation = RoomUser::where('room_id', $request->room_id)
                                    ->where('user_id', $request->user_id)
                                    ->first();

        if ($rejectInvitation) {

            $roomUser = RoomUser::where('id', $rejectInvitation->id)->first();
            $roomUser->delete();

            $userNotification = SavedNotification::where('room_id', $request->room_id)
                                                ->where('user_id', $request->user_id)
                                                ->where('type', Constants::notificationTypejoinRoom)
                                                ->first();
            $userNotification->delete();

            return response()->json([
                'status' => true,
                'message' => 'Reject Room Request',
                'data' => $roomUser,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Record does not exist',
        ]);
    }

    public function fetchRoomRequestList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }


        $roomUser = RoomUser::where('room_id', $request->room_id)
                            ->where(function($query){
                                $query->where('type', 1);
                                // ->orWhere('type', 4);
                            })
                            ->with('user')
                            ->get();

        if ($roomUser) {
            return response()->json([
                'status' => true,
                'message' => 'Fetch Room Request List',
                'data' => $roomUser,
            ]);
        }
    }

    public function fetchRoomUsersList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $room = Room::where('id', $request->room_id)->first();

        if($room) {

            $roomUsers = RoomUser::where('room_id', $request->room_id)
                        ->where(function($query){
                            $query->where('type', 2)
                            ->orWhere('type', 3)
                            ->orWhere('type', 5);
                        })
                        ->with('user')
                        ->offset($request->start)
                        ->limit($request->limit)
                        ->get();

            return response()->json([
                'status' => true,
                'message' => 'Fetch Room User List',
                'data' => $roomUsers,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Room not found',
        ]);
    }

    public function removeUserFromRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)->where('id', $request->user_id)->first();

        if ($user != null) {

            $roomRequest = Room::where('id', $request->room_id)->first();
            if ($roomRequest == null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Room Not Found',
                ]);
            }

            $removeUser = RoomUser::where('room_id', $request->room_id)
                ->where('user_id', $request->user_id)
                ->get()
                ->first();

            if ($removeUser) {
                $roomUser = RoomUser::where('id', $removeUser->id)->first();

                $userCount = Room::where('id', $request->room_id)->first();
                $userCount->total_member = max(0, $userCount->total_member - 1);
                $userCount->save();

                $roomUser->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Remove user from Room',
                    'data' => $roomUser,
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'User Not Found',
        ]);

    }

    public function reports()
    {
        return view('reports');
    }

    public function reportList(Request $request)
    {
        $reportType = 0;
        $totalData = Report::where('type', $reportType)->count();
        $rows = Report::where('type', $reportType)
            ->orderBy('id', 'DESC')
            ->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'room_id',
            2 => 'user_id',
            3 => 'reason',
            4 => 'desc',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Report::where('type', $reportType)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = Report::where('type', $reportType)
                            ->whereHas('room', function ($query) use ($search) {
                                $query->where('title', 'like', '%' . $search . '%');
                            })
                            ->orWhere('reason', 'like', '%' . $search . '%')
                            ->orWhere('desc', 'like', '%' . $search . '%')
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            $totalFiltered = Report::where('type', $reportType)
                                    ->whereHas('room', function ($query) use ($search) {
                                        $query->where('title', 'like', '%' . $search . '%');
                                    })
                                    ->orWhere('reason', 'like', '%' . $search . '%')
                                    ->orWhere('desc', 'like', '%' . $search . '%')
                                    ->count();
        }
        $data = [];
        foreach ($result as $item) {
            $userData = User::where('id', $item->user_id)->first();

            if ($userData->profile == null) {
                $image = '<img src="asset/image/default.png" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            } else {
                $image = '<img src="' . $userData->profile . '" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            }

            $rejectReport = '<a href="#" class="me-3 btn btn-orange px-4 text-white rejectReport d-flex align-items-center" rel=' . $item->id . '  data-tooltip="Reject Report" >' . __(' <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> <span class="ms-2"> Reject </span>') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deleteReportWithRoom d-flex align-items-center " rel=' . $item->id . '  data-tooltip="Delete Room" >' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> <span class="ms-2"> Delete Room </span> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $rejectReport . $delete . ' </span>';

            $data[] = [$image, $item->room->title, $userData->identity, $item->reason, $item->desc, $action];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function reportRoom(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
            'reason' => 'required',
            'desc' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }


        $room = Room::where('id', $request->room_id)
                    ->first();
        $user = User::where('is_block', 0)
                    ->where('id', $request->user_id)
                    ->first();
        $roomUser = RoomUser::where('user_id', $request->user_id)
                            ->first();

        if ($roomUser != null) {
            if ($room != null) {
                if ($user) {

                    $reportType = 0;

                    $report = new Report();
                    $report->type = $reportType;
                    $report->room_id = $request->room_id;
                    $report->user_id = $request->user_id;
                    $report->reason = $request->reason;
                    $report->desc = $request->desc;
                    $report->save();

                    return response()->json([
                        'status' => true,
                        'message' => 'Report Added Successfully',
                        'data' => $report,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'User Not Found',
                    ]);
                }
            }
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function deleteReport(Request $request)
    {
        $report = Report::where('id', $request->report_id)->first();

        if ($report) {
            $roomReports = Report::where('room_id', $report->room_id)->get();
            $roomReports->each->delete();

            return response()->json([
                'status' => true,
                'message' => 'Report Delete Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Report Not Found',
            ]);
        }
    }

    public function deleteReportWithRoom(Request $request)
    {
        $report = Report::where('id', $request->report_RoomId)->first();

        if (!$report) {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        }
         
        Report::where('room_id', $report->room_id)->delete();

        RoomUser::where('room_id', $report->room_id)->delete();

        SavedNotification::where('room_id', $report->room_id)->delete();

        GlobalFunction::deleteFile($report->room->photo);

        Room::where('id', $report->room_id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Room Delete Successfully.',
        ]);
    }

    public function leaveThisRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)->first();
        if ($user == null) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

        $roomId = Room::where('id', $request->room_id)->first();

        if ($roomId == null) {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        }

        $leaveRoom = RoomUser::where('room_id', $request->room_id)->where('user_id', $request->user_id)->first();

        if($leaveRoom) {

            $roomId->total_member = max(0, $roomId->total_member - 1);
            $roomId->save();

            // $leaveRoom->type = -1;
            // $leaveRoom->save();

            $leaveRoom->delete();

            $userNotification = SavedNotification::where('room_id', $request->room_id)
                                    ->where('user_id', $request->user_id)
                                    ->where('type', Constants::notificationTypeAcceptRoomRequest)
                                    ->first();
            if ($userNotification != null) {
                $userNotification->delete();
            }


            return response()->json([
                'status' => true,
                'message' => 'Leave This Room',
                'data' => $leaveRoom,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Room user data not found',
            ]);
        }

    }

    public function fetchRoomDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $roomDetails = Room::where('id', $request->room_id)->first();
        if ($roomDetails) {
            $userRoomStatus = RoomUser::where('user_id', $request->user_id)->where('room_id', $request->room_id)->first();
            if($userRoomStatus) {
                $roomDetails->userRoomStatus = $userRoomStatus->type;
                $roomDetails->is_mute = $userRoomStatus->is_mute;

                $allInterests = Interest::whereIn('id', explode(',', $roomDetails->interest_ids))->get();

                if ($request->should_show_member == 1) {
                    $roomUsers = RoomUser::where('room_id', $request->room_id)
                                        ->where(function ($query) {
                                            $query->where('type', 2)
                                                ->orWhere('type', 3)
                                                ->orWhere('type', 5)
                                                ->orWhere('type', -1);
                                            })
                                            ->with('user')
                                            ->get()
                                            ->pluck('user');

                    //  $roomDetails->total_member = $roomUsers->count();
                     $roomDetails->roomUsers = $roomUsers;
                }

                $roomDetails->interests = $allInterests;
                $admin = User::where('id', $roomDetails->admin_id)->first();
                $roomDetails->admin = $admin;
                return response()->json([
                    'status' => true,
                    'message' => 'Room Details',
                    'data' => $roomDetails,
                ]);
             } else {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found',
                ]);
             }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Room not found',
            ]);
        }
    }

    public function deleteRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $roomUser = RoomUser::where('room_id', $request->room_id)->where('user_id', $request->user_id)->where('type', 5)->first();
        if (!$roomUser) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
        
        $room = Room::where('id', $request->room_id)->first();
        if (!$room) {
            return response()->json([
                'status' => false,
                'message' => 'Room not found',
            ]);
        }

        GlobalFunction::deleteFile($room->photo);
        RoomUser::where('room_id', $request->room_id)->delete();
        SavedNotification::where('room_id', $request->room_id)->delete();
        Report::where('room_id', $request->room_id)->where('type', 0)->delete();

        $room->delete();

        return response()->json([
            'status' => true,
            'message' => 'Room deleted successfully',
            'data' => $room,
        ]);
    }

    public function fetchMyOwnRooms(Request $request)
    {
        $user = User::where('id', $request->my_user_id)->first();
        if ($user) {
            $room = Room::where('admin_id', $user->id)->get();

            return response()->json([
                'status' => true,
                'message' => 'Room you own',
                'data' => $room,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);
    }

    public function fetchRoomsByInterest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'interest_id' => 'required',
            'user_id' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $setting = Setting::first();

        $rooms = Room::whereRelation('user', 'is_block', 0)
                    ->whereRaw('find_in_set("' . $request->interest_id . '",interest_ids)')
                    ->where('is_private', 0)
                    ->where('total_member', '<>', (int) $setting->setRoomUsersLimit)
                    ->offset($request->start)
                    ->limit($request->limit)
                    ->get();

        if (!$rooms->isEmpty()) {
            foreach ($rooms as $room) {
                $roomUser = RoomUser::where('user_id', $request->user_id)->where('room_id', $room->id)->first();
                if ($roomUser) {
                    $room->userRoomStatus = $roomUser->type;
                } else {
                    $room->userRoomStatus = 0;
                }
            }
        }


        return response()->json([
            'status' => true,
            'message' => 'Room Result',
            'data' => $rooms,
        ]);
    }

    public function fetchSuggestedRooms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)->where('id', $request->my_user_id)->first();
        if ($user) {
            $rooms = Room::all();
            if ($rooms != null) {
                $interest_ids = explode(',', $user->interest_ids);
                $myRoomIds = RoomUser::where('user_id', $request->my_user_id)->pluck('room_id');
                $blockUserIds = User::where('is_block', 1)->pluck('id');


                if ($user->interest_ids == null) {

                    $rooms = Room::where('admin_id', '!=', $request->my_user_id)
                                ->inRandomOrder()
                                ->where('is_private', 0)
                                ->whereNotIn('id', $myRoomIds)
                                ->whereNotIn('admin_id', $blockUserIds)
                                ->limit(2)
                                ->get();

                    $setting = Setting::first();

                    return response()->json([
                        'status' => true,
                        'message' => 'Suggested random room',
                        'data' => $rooms,
                    ]);

                } else {


                    // for loop
                    // for ($i = 0; $i < count($interest_ids); $i++) {
                    //     $rooms = Room::where('is_private', 0)->whereRaw('find_in_set("' . $interest_ids[$i] . '", interest_ids)')->limit(10)->get();
                    //     for ($j = 0; $j < count($rooms); $j++) {
                    //         foreach ($rooms as $room) {
                    //             if ($room->total_member <= $setting->setRoomUsersLimit) {
                    //                 if (!in_array($rooms[$j], $data) && count($data) != 10) {
                    //                     array_push($data, $rooms[$j]);
                    //                 }
                    //             }
                    //         }
                    //     }

                    $setting = Setting::first();

                    shuffle($interest_ids);

                        $data = [];

                        // foreach loop
                        foreach ($interest_ids as $interest_id) {

                        $rooms = Room::where('admin_id', '!=', $request->my_user_id)
                                    ->whereRaw('find_in_set("' . $interest_id . '", interest_ids)')
                                    ->inRandomOrder()
                                    ->where('is_private', 0)
                                    ->whereNotIn('id', $myRoomIds)
                                    ->whereNotIn('admin_id', $blockUserIds)
                                    ->limit(2)
                                    ->get();

                        foreach ($rooms as $room) {
                            if ($room->total_member < $setting->setRoomUsersLimit) {
                                    if(!in_array($room, $data) && (count($data) != 2)) {
                                        array_push($data, $room);
                                    }
                                }
                            }
                        }

                        if (count($rooms) <= 1) {
                            $rooms = Room::where('admin_id', '!=', $request->my_user_id)
                                        ->inRandomOrder()
                                        ->where('is_private', 0)
                                        ->whereNotIn('id', $myRoomIds)
                                        ->whereNotIn('admin_id', $blockUserIds)
                                        ->limit(2)
                                        ->get();

                            foreach ($rooms as $room) {
                                if ($room->total_member < $setting->setRoomUsersLimit) {
                                    if(!in_array($room, $data) && (count($data) != 2)) {
                                        array_push($data, $room);
                                    }
                                }
                            }
                        }


                        return response()->json([
                            'status' => true,
                            'message' => 'Suggested Room',
                            'data' => $rooms,
                        ]);

                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Rooms not available',
                ]);
            }
        }
    }

    public function searchUserForInvitation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'room_id' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)
                    ->where('id', $request->my_user_id)
                    ->first();
        if ($user != null) {
            $room = Room::where('id', $request->room_id)->first();
            if ($room != null) {

                $blockUserIds = explode(',', $user->block_user_ids);

                $alreadyInRoomOrAcceptedUserList = RoomUser::where('room_id', $request->room_id)->pluck('user_id');

                $roomUserIds = RoomUser::where('user_id', $request->my_user_id)->where('room_id', $request->room_id)->get()->pluck('user_id');

                if ($roomUserIds) {
                    $searchUser = User::where('is_block', 0)
                                    ->where('is_invited_to_room', 1)
                                    ->whereNotIn('id', $roomUserIds)
                                    ->whereNotIn('id', $alreadyInRoomOrAcceptedUserList)
                                    ->where(function ($query) use ($request) {
                                        $query
                                            ->where('username', 'like', '%' . $request->keyword . '%')
                                            ->orWhere('full_name', 'like', '%' . $request->keyword . '%');
                                    })
                                    ->whereNotIn('id', $blockUserIds)
                                    ->offset($request->start)
                                    ->limit($request->limit)
                                    ->get();

                    return response()->json([
                        'status' => true,
                        'message' => 'User result',
                        'data' => $searchUser,
                    ]);
                } else {
                       return response()->json([
                        'status' => false,
                        'message' => 'User not found',
                    ]);
                }
            }
            return response()->json([
                'status' => false,
                'message' => 'Room not found',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User Not Found',
        ]);
    }

    public function fetchRandomRooms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)
                    ->where('id', $request->user_id)
                    ->first();
        if($user) {


            $blockUserIds = User::where('is_block', 1)->pluck('id');

            $rooms = Room::whereNotIn('admin_id', $blockUserIds)
                        ->where('is_private', 0)
                        // ->where('admin_id', '!=', $request->user_id)
                        ->inRandomOrder()
                        ->limit($request->limit)
                        ->get();


            if(!$rooms->isEmpty()) {

                foreach ($rooms as $room) {
                    $roomUser = RoomUser::where('user_id', $request->user_id)->where('room_id', $room->id)->first();
                    if($roomUser){
                        $room->userRoomStatus = $roomUser->type;
                    } else {
                        $room->userRoomStatus = 0;
                    }

                    // $total_member = RoomUser::whereNotIn('user_id', $blockUserIds)->where('room_id', $room->id)->whereIn('type', 2)->count();
                    // $room->total_member = $total_member;
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'Random Room List',
                'data' => $rooms,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found'
        ]);
    }

    public function makeRoomAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)->first();
        if($user) {
            $roomUser = RoomUser::where('room_id', $request->room_id)->where('user_id', $request->user_id)->first();
            if($roomUser) {
                $roomUser->type = 3;
                $roomUser->save();
                return response()->json([
                    'status' => true,
                    'message' => 'Co\'admin created succesfully',
                    'data' => $roomUser,
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'Room user not found',
                'data' => $roomUser,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);

    }

    public function fetchRoomAdmins(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $room = Room::where('id', $request->room_id)->first();
        if($room) {
            $roomAdmins = RoomUser::where('room_id', $request->room_id)->whereIn('type', [3, 5])->with('user')->get();
            return response()->json([
                'status' => true,
                'message' => 'Fetch room admins',
                'data' => $roomAdmins,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Room not found',
        ]);
    }

    public function removeAdminFromRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $room = Room::where('id', $request->room_id)->first();
        if ($room) {
            $roomAdmin = RoomUser::where('room_id', $request->room_id)->where('user_id', $request->user_id)->where('type', 3)->first();
            if($roomAdmin) {
                $roomAdmin->type = 2;
                $roomAdmin->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Remove admin from room',
                    'data' => $roomAdmin,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Already User not co-admin',
                ]);
            }

        }
        return response()->json([
            'status' => false,
            'message' => 'Room not found',
        ]);

    }

    public function fetchRoomsList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $rooms = RoomUser::with('room')->where('user_id', $request->user_id)
                        ->where(function($query){
                            $query->where('type', 2)
                                  ->orWhere('type', 3)
                                  ->orWhere('type', 5);
                            })->get();
        return response()->json([
            'status' => true,
            'message' => 'Fetching rooms list Successfully',
            'data' => $rooms,
        ]);

    }

    public function rooms()
    {
         return view('rooms');
    }

    public function roomsListWeb(Request $request)
    {
        $totalData = Room::count();
        $rows = Room::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'image',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Room::offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
        } else {
            $search = $request->input('search.value');
            $result = Room::with(['user', 'roomUser'])
                            ->Where('title', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            $totalFiltered = $result->count();
        }
        $data = [];

        foreach ($result as $item) {

            if ($item->photo == null) {
                $image = '<img src="asset/image/default.png" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            } else {
                $image = '<img src="' . $item->photo . '" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            }

            $adminName = '<a href="usersDetail/'. $item->admin_id .'">'.  $item->user->full_name .'</a>';

            if ($item->is_private == 1) {
                $private = '<label class="switch"><input type="checkbox" name="private" rel="' . $item->id . '" value="' . $item->is_private . '" id="private" class="private " checked ><span class="slider"></span> </label>';
            } else {
                $private = '<label class="switch"><input type="checkbox" name="private" rel="' . $item->id . '" value="' . $item->is_private . '" id="private" class="private"><span class="slider"></span> </label>';
            }

            if ($item->is_join_request_enable == 1) {
                $join_request = '<label class="switch"><input type="checkbox" name="is_join_request_enable" rel="' . $item->id . '" value="' . $item->is_join_request_enable . '" id="is_join_request_enable" class="is_join_request_enable" checked ><span class="slider"></span> </label>';
            } else {
                $join_request = '<label class="switch"><input type="checkbox" name="is_join_request_enable" rel="' . $item->id . '" value="' . $item->is_join_request_enable . '" id="is_join_request_enable" class="is_join_request_enable"><span class="slider"></span> </label>';
            }

            $view = '<a href="./roomDetails/' . $item->id . '" data-title="' . $item->title . '" class="ms-3 btn btn-info px-4 text-white edit" rel=' . $item->id . ' data-tooltip="View Room">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>') . '</a>';
            $delete = '<a href="#" class="ms-3 btn btn-danger px-4 text-white delete deleteRoomByAdmin d-flex align-items-center" rel="' . $item->id . '" data-tooltip="Delete Room">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' .  $view .  $delete .' </span>';

            $data[] = [
                $image,
                $item->title,
                $adminName,
                $item->total_member,
                $join_request,
                $private,
                $action
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function updatePrivateStatus(Request $request)
    {
        $room = Room::where('id', $request->id)->first();
        if ($room) {
            $room->is_private = $request->is_private;
            $room->save();

            return response()->json([
                'status' => true,
                'message' => 'Room Status Updated successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Room not found',
        ]);
    }

    public function updateJoinRequestStatus(Request $request)
    {
        $room = Room::where('id', $request->id)->first();
        if ($room) {
            $room->is_join_request_enable = $request->is_join_request_enable;
            $room->save();

            return response()->json([
                'status' => true,
                'message' => 'Room Status Updated successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Room not found',
        ]);
    }

    public function roomDetails(Request $request)
    {
        $room = Room::where('id', $request->id)->first();
        $interests = Interest::get();
        if ($room) {
            return view('roomDetails', [
                'room' => $room,
                'interests' => $interests,
            ]);
        }
    }

    public function allRoomUsersListTableWeb(Request $request)
    {
        $totalData = RoomUser::where('room_id', $request->room_id)
                                ->where(function($query){
                                    $query->where('type', 2)
                                    ->orWhere('type', 3)
                                    ->orWhere('type', 5);
                                })->count();
        $rows = RoomUser::where('room_id', $request->room_id)->where(function($query){
                            $query->where('type', 2)
                            ->orWhere('type', 3)
                            ->orWhere('type', 5);
                        })->orderBy('type', 'ASC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'admin_id',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = RoomUser::where('room_id', $request->room_id)->with('user')->where(function($query){
                                    $query->where('type', 2)
                                    ->orWhere('type', 3)
                                    ->orWhere('type', 5);
                                })
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
        } else {
            $search = $request->input('search.value');
            $result = RoomUser::where('room_id', $request->room_id)->with('user')->where(function($query){
                                    $query->where('type', 2)
                                    ->orWhere('type', 3)
                                    ->orWhere('type', 5);
                                })
                            ->WhereRelation('user', 'full_name', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            $totalFiltered = RoomUser::where('room_id', $request->room_id)
                                    ->where(function($query){
                                        $query->where('type', 2)
                                        ->orWhere('type', 3)
                                        ->orWhere('type', 5);
                                    })
                                    ->WhereRelation('user', 'full_name', 'LIKE', "%{$search}%")
                                    ->count();
        }
        $data = [];

        foreach ($result as $item) {

            $user = '<a href="../usersDetail/'. $item->user->id .'" class="userLink"> '. $item->user->full_name .' </a>';

            if ($item->type == 2) {
                $typeOfMember = '<span class="type-badge badge rounded bg-warning text-white fs-6 fw-medium w-20"> Member </span>';
            } elseif ($item->type == 3) {
                $typeOfMember = '<span class="type-badge badge rounded bg-info text-white fs-6 fw-medium w-20"> Co - Member </span>';
            } elseif ($item->type == 5) {
                $typeOfMember = '<span class="type-badge badge rounded bg-success text-white fs-6 fw-medium w-20"> Admin </span>';
            }


            $data[] = [
                $user,
                $typeOfMember,
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function roomMembersListTableWeb(Request $request)
    {
        $totalData = RoomUser::where('room_id', $request->room_id)->where('type', 2)->count();
        $rows = RoomUser::where('room_id', $request->room_id)->where('type', 2)->orderBy('type', 'ASC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'admin_id',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = RoomUser::where('room_id', $request->room_id)
                            ->where('type', 2)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
        } else {
            $search = $request->input('search.value');
            $result = RoomUser::where('room_id', $request->room_id)
                            ->where('type', 2)
                            ->WhereRelation('user', 'full_name', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            $totalFiltered = RoomUser::where('room_id', $request->room_id)
                                    ->WhereRelation('user', 'full_name', 'LIKE', "%{$search}%")
                                    ->where('type', 2)
                                    ->count();
        }
        $data = [];

        foreach ($result as $item) {

            $user = '<a href="../usersDetail/'. $item->user->id .'" class="userLink"> '. $item->user->full_name .' </a>';
            $typeOfMember = '<span class="type-badge badge rounded bg-warning text-white fs-6 fw-medium w-20"> Member </span>';

            $data[] = [
                $user,
                $typeOfMember,
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function roomCoAdminTableWeb(Request $request)
    {
        $totalData = RoomUser::where('room_id', $request->room_id)->where('type', 3)->count();
        $rows = RoomUser::where('room_id', $request->room_id)->where('type', 3)->orderBy('type', 'ASC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'admin_id',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = RoomUser::where('room_id', $request->room_id)
                            ->where('type', 3)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
        } else {
            $search = $request->input('search.value');
            $result = RoomUser::where('room_id', $request->room_id)
                            ->where('type', 3)
                            ->WhereRelation('user', 'full_name', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            $totalFiltered = RoomUser::where('room_id', $request->room_id)
                                    ->where('type', 3)
                                    ->WhereRelation('user', 'full_name', 'LIKE', "%{$search}%")
                                    ->count();
        }
        $data = [];

        foreach ($result as $item) {

            $user = '<a href="../usersDetail/'. $item->user->id .'" class="userLink"> '. $item->user->full_name .' </a>';
            $typeOfMember = '<span class="type-badge badge rounded bg-info text-white fs-6 fw-medium w-20"> Co - Admin </span>';

            $data[] = [
                $user,
                $typeOfMember,
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function userRoomsOwnTable(Request $request)
    {
        $totalData = Room::where('admin_id', $request->user_id)
                        ->count();
        $rows = Room::where('admin_id', $request->user_id)
                    ->orderBy('id', 'DESC')
                    ->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'image',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;

        if (empty($request->input('search.value'))) {
            $result = Room::where('admin_id', $request->user_id)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
        } else {
            $search = $request->input('search.value');
            $result = Room::where('admin_id', $request->user_id)
                            ->with()
                            ->with(['user','roomUser'])
                            ->Where('title', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            $totalFiltered = Room::where('admin_id', $request->user_id)
                                    ->Where('title', 'LIKE', "%{$search}%")
                                    ->count();
        }
        $data = [];
        foreach ($result as $item) {

            if ($item->photo == null) {
                $image = '<img src="../asset/image/default.png" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            } else {
                $image = '<img src="' . $item->photo . '" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            }

            if ($item->is_private == 1) {
                $private = '<label class="switch"><input type="checkbox" name="private" rel="' . $item->id . '" value="' . $item->is_private . '" id="private" class="private " checked ><span class="slider"></span> </label>';
            } else {
                $private = '<label class="switch"><input type="checkbox" name="private" rel="' . $item->id . '" value="' . $item->is_private . '" id="private" class="private"><span class="slider"></span> </label>';
            }


            if ($item->is_join_request_enable == 1) {
                $join_request = '<label class="switch"><input type="checkbox" name="is_join_request_enable" rel="' . $item->id . '" value="' . $item->is_join_request_enable . '" id="is_join_request_enable" class="is_join_request_enable" checked ><span class="slider"></span> </label>';
            } else {
                $join_request = '<label class="switch"><input type="checkbox" name="is_join_request_enable" rel="' . $item->id . '" value="' . $item->is_join_request_enable . '" id="is_join_request_enable" class="is_join_request_enable"><span class="slider"></span> </label>';
            }

            $view = '<a href="../roomDetails/' . $item->id . '" data-title="' . $item->title . '" class="ms-3 btn btn-info px-4 text-white edit" rel=' . $item->id . ' data-tooltip="View User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>') . '</a>';
            $delete = '<a href="#" class="ms-3 btn btn-danger px-4 text-white delete deleteRoomByAdmin d-flex align-items-center" rel="' . $item->id . '" data-tooltip="Delete Room">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' .  $view . $delete . ' </span>';

            $data[] = [
                $image,
                $item->title,
                $item->roomUser->count(),
                $join_request,
                $private,
                $action
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function userRoomInTable(Request $request)
    {
        $totalData = RoomUser::where('user_id', $request->user_id)->where('type', '!=', Constants::invitedForRoom)->count();
        $rows = RoomUser::where('user_id', $request->user_id)
                    ->where('type', '!=', Constants::invitedForRoom)
                    ->orderBy('id', 'DESC')
                    ->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'image',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;

        if (empty($request->input('search.value'))) {
            $result = RoomUser::where('user_id', $request->user_id)
                            ->where('type', '!=', Constants::invitedForRoom)
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
        } else {
            $search = $request->input('search.value');
            $result = RoomUser::where('user_id', $request->user_id)
                            ->where('type', '!=', Constants::invitedForRoom)
                            ->with('room')
                            ->Where('title', 'LIKE', "%{$search}%")
                            ->offset($start)
                            ->limit($limit)
                            ->orderBy($order, $dir)
                            ->get();
            $totalFiltered = RoomUser::where('user_id', $request->user_id)
                                    ->where('type', '!=', Constants::invitedForRoom)
                                    ->Where('title', 'LIKE', "%{$search}%")
                                    ->count();
        }
        $data = [];
        foreach ($result as $item) {

            if ($item->room->photo == null) {
                $image = '<img src="../asset/image/default.png" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            } else {
                $image = '<img src="' . $item->room->photo . '" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            }
            if ($item->type == 1) {
                $typeOfMember = '<span class="type-badge badge rounded bg-warning text-white fs-6 fw-medium w-20"> Requested </span>';
            } elseif ($item->type == 2) {
                $typeOfMember = '<span class="type-badge badge rounded bg-warning text-white fs-6 fw-medium w-20"> Member </span>';
            } elseif ($item->type == 3) {
                $typeOfMember = '<span class="type-badge badge rounded bg-info text-white fs-6 fw-medium w-20"> Co - Member </span>';
            } elseif ($item->type == 5) {
                $typeOfMember = '<span class="type-badge badge rounded bg-success text-white fs-6 fw-medium w-20"> Admin </span>';
            } else {
                $typeOfMember = '<span class="type-badge badge rounded bg-success text-white fs-6 fw-medium w-20"> -1 </span>';
            }

            $view = '<a href="../roomDetails/' . $item->room->id . '" data-title="' . $item->room->title . '" class="ms-3 btn btn-info px-4 text-white edit" rel=' . $item->room->id . ' data-tooltip="View User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>') . '</a>';
            $delete = '<a href="#" class="ms-3 btn btn-danger px-4 text-white delete deleteRoomByAdmin d-flex align-items-center" rel="' . $item->room->id . '" data-tooltip="Delete Room">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' .  $view . $delete . ' </span>';

            $data[] = [
                $image,
                $item->room->title,
                $typeOfMember,
                $action
            ];
        }
        $json_data = [
            'draw' => intval($request->input('draw')),
            'recordsTotal' => intval($totalData),
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ];
        echo json_encode($json_data);
        exit();
    }

    public function deleteThisRoom(Request $request)
    {
        $room = Room::where('id', $request->room_id)->first();

        if ($room) {

            Report::where('room_id', $request->room_id)->delete();

            RoomUser::where('room_id', $request->room_id)->delete();

            SavedNotification::where('room_id', $request->room_id)->delete();

            GlobalFunction::deleteFile($room->photo);

            $room->delete();

            return response()->json([
                'status' => true,
                'message' => 'Room Delete Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Room Not Found',
            ]);
        }
    }

    public function muteUnmuteRoomNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)->first();
        $room = Room::where('id', $request->room_id)->first();

        if ($user != null || $room != null) {


            $roomUser = RoomUser::where('room_id', $request->room_id)
                                ->where('user_id', $request->user_id)
                                ->first();
            
            $roomUser->is_mute = (int) $request->is_mute;
            $roomUser->save();

            return response()->json([
                'status' => true,
                'message' => 'Mute Unmute Successfully',
                'data' => $roomUser,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong',
        ]);

    }
    
    public function fetchRoomsIAmIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            $userInRooms = RoomUser::where('user_id', $request->user_id)->get();

            return response()->json([
                'status' => false,
                'message' => 'Users In Room List',
                'data' => $userInRooms,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User Not Found',
        ]);

    }



}
