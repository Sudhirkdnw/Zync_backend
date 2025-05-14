<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Constants;
use App\Models\FollowingList;
use App\Models\GlobalFunction;
use App\Models\Interest;
use App\Models\Like;
use App\Models\Post;
use App\Models\PostContent;
use App\Models\ProfileVerification;
use App\Models\Reel;
use App\Models\ReelComment;
use App\Models\Report;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\SavedNotification;
use App\Models\Story;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function users()
    {
        return view('users');
    }

    public function userListWeb(Request $request)
    {
        $totalData = User::count();
        $rows = User::orderBy('id', 'DESC')->get();

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
            $result = User::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = User::Where('full_name', 'LIKE', "%{$search}%")->orWhere('username', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = User::Where('full_name', 'LIKE', "%{$search}%")->orWhere('username', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {
             
            if ($item->is_verified == 2 || $item->is_verified == 3) {
                $is_verified = '<img src="asset/image/verified.svg" class="verified_icon_top">';
                $username = $item->full_name . $is_verified;
            } else {
                $username = $item->full_name;
            }

            if ($item->profile == null) {
                $image = '<img src="asset/image/default.png" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            } else {
                $image = '<img src="' . $item->profile . '" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            }

            if ($item->device_type == 0) {
                $device_type = 'Android';
            } else {
                $device_type = 'iOS';
            }

            if ($item->is_block == 0) {
                $blockUser = '<a href="#" class="btn btn-danger px-4 text-white blockUserBtn" rel=' . $item->id . ' data-tooltip="Block User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg> <span class="ms-2"> Block </span>') . '</a>';
            } else {
                $blockUser = '<a href="#" class="btn btn-primary px-4 text-white unblockUserBtn" rel=' . $item->id . ' data-tooltip="Unblock User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg> <span class="ms-2"> Unblock </span>') . '</a>';
            }

            if ($item->is_moderator == 1) {
                $moderator = '<label class="switch"><input type="checkbox" name="moderator" rel="' . $item->id . '" value="' . $item->is_moderator . '" id="moderator" class="moderator" checked ><span class="slider"></span> </label>';
            } else {
                $moderator = '<label class="switch"><input type="checkbox" name="moderator" rel="' . $item->id . '" value="' . $item->is_moderator . '" id="moderator" class="moderator"><span class="slider"></span> </label>';
            }

            $view = '<a href="usersDetail/' . $item->id . '" data-title="' . $item->title . '" class="ms-3 btn btn-info px-4 text-white edit" rel=' . $item->id . ' data-tooltip="View User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> <span class="ms-2"> View </span>') . '</a>';
            $action = '<span class="float-right">' . $blockUser . $view . ' </span>';

            $data[] = [
                $image,
                $username,
                $item->username,
                $device_type,
                $moderator,
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

    public function moderatorsList(Request $request)
    {
        // Columns used for ordering
        $columns = [
            0 => 'id',
            1 => 'image',
        ];

        // Basic input validation and default settings
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $order = $columns[$request->input('order.0.column', 0)];
        $dir = $request->input('order.0.dir', 'DESC');
        $search = $request->input('search.value');
        $totalData = [];

        // Fetch filtered and paginated data
        $query = User::where('is_moderator', 1);

        // Apply search filter if present
        if (!empty($search)) {
            $query->where(function ($query) use ($search) {
                $query->where('full_name', 'LIKE', "%{$search}%")
                ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        // Clone the query to get the total filtered count
        $totalFiltered = $query->count();

        // Apply pagination and ordering
        $result = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        // Preparing the data array
        $data = $result->map(function ($item) {
            $username = $item->full_name;
            if (in_array($item->is_verified, [2, 3])) {
                $username .= '<img src="asset/image/verified.svg" class="verified_icon_top">';
            }

            $image = $item->profile
                ? '<img src="' . $item->profile . '" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">'
                : '<img src="asset/image/default.png" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';

            $device_type = $item->device_type == 0 ? 'Android' : 'iOS';

            $blockUser = $item->is_block == 0
                ? '<a href="#" class="btn btn-danger px-4 text-white blockUserBtn" rel=' . $item->id . ' data-tooltip="Block User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg> <span class="ms-2"> Block </span>') . '</a>'
                : '<a href="#" class="btn btn-primary px-4 text-white unblockUserBtn" rel=' . $item->id . ' data-tooltip="Unblock User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg> <span class="ms-2"> Unblock </span>') . '</a>';

            $moderator = '<label class="switch">
            <input type="checkbox" name="moderator" rel="' . $item->id . '" value="' . $item->is_moderator . '" class="moderator" ' . ($item->is_moderator ? 'checked' : '') . '>
            <span class="slider"></span>
        </label>';

            $view = '<a href="usersDetail/' . $item->id . '" data-title="' . $item->title . '" class="ms-3 btn btn-info px-4 text-white edit" rel=' . $item->id . ' data-tooltip="View User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> <span class="ms-2"> View </span>') . '</a>';
            $action = '<span class="float-right">' . $blockUser . $view . ' </span>';

            return [
                $image,
                $username,
                $item->username,
                $device_type,
                $moderator,
                $action,
            ];
        })->toArray();

        // JSON response
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function verifiedUserList(Request $request)
    {
        $totalData = User::where('is_verified', Constants::is_verified)->count();
        $rows = User::where('is_verified', Constants::is_verified)->orderBy('id', 'DESC')->get();

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
        $searchValue = $request->input('search.value');

        $query = User::where('is_verified', Constants::is_verified);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('full_name', 'LIKE', "%{$searchValue}%")
                ->orWhere('username', 'LIKE', "%{$searchValue}%");
            });
        }

        $result = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $totalFiltered = $result->count();

        $data = [];
        foreach ($result as $item) {

            if ($item->is_verified == 2 || $item->is_verified == 3) {
                $is_verified = '<img src="asset/image/verified.svg" class="verified_icon_top">';
                $username = $item->full_name . $is_verified;
            } else {
                $username = $item->full_name;
            }
            

            if ($item->profile == null) {
                $image = '<img src="asset/image/default.png" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            } else {
                $image = '<img src="' . $item->profile . '" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            }

            if ($item->device_type == 0) {
                $device_type = 'Android';
            } else {
                $device_type = 'iOS';
            }

            if ($item->is_block == 0) {
                $blockUser = '<a href="#" class="btn btn-danger px-4 text-white blockUserBtn" rel=' . $item->id . ' data-tooltip="Block User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg> <span class="ms-2"> Block </span>') . '</a>';
            } else {
                $blockUser = '<a href="#" class="btn btn-primary px-4 text-white unblockUserBtn" rel=' . $item->id . ' data-tooltip="Unblock User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg> <span class="ms-2"> Unblock </span>') . '</a>';
            }

            $view = '<a href="usersDetail/' . $item->id . '" data-title="' . $item->title . '" class="ms-3 btn btn-info px-4 text-white edit" rel=' . $item->id . ' data-tooltip="View User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> <span class="ms-2"> View </span>') . '</a>';
            $action = '<span class="float-right">' . $blockUser . $view . ' </span>';

            $data[] = [
                $image,
                $username,
                $item->username,
                $device_type,
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

    public function verifiedUserBySubscriptionList(Request $request)
    {
        $totalData = User::where('is_verified', Constants::is_subscribe_verified)->count();
        $rows = User::where('is_verified', Constants::is_subscribe_verified)->orderBy('id', 'DESC')->get();

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
        $searchValue = $request->input('search.value');

        $query = User::where('is_verified', Constants::is_subscribe_verified);

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('full_name', 'LIKE', "%{$searchValue}%")
                ->orWhere('username', 'LIKE', "%{$searchValue}%");
            });
        }

        $result = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $totalFiltered = $result->count();
        $data = [];
        foreach ($result as $item) {

            $is_verified = '<img src="asset/image/verified.svg" class="verified_icon_top">';
            $username = $item->full_name . $is_verified;

            if ($item->profile == null) {
                $image = '<img src="asset/image/default.png" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            } else {
                $image = '<img src="' . $item->profile . '" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            }

            if ($item->device_type == 0) {
                $device_type = 'Android';
            } else {
                $device_type = 'iOS';
            }

            if ($item->is_block == 0) {
                $blockUser = '<a href="#" class="btn btn-danger px-4 text-white blockUserBtn" rel=' . $item->id . ' data-tooltip="Block User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg> <span class="ms-2"> Block </span>') . '</a>';
            } else {
                $blockUser = '<a href="#" class="btn btn-primary px-4 text-white unblockUserBtn" rel=' . $item->id . ' data-tooltip="Unblock User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg> <span class="ms-2"> Unblock </span>') . '</a>';
            }

            $view = '<a href="usersDetail/' . $item->id . '" data-title="' . $item->title . '" class="ms-3 btn btn-info px-4 text-white edit" rel=' . $item->id . ' data-tooltip="View User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> <span class="ms-2"> View </span>') . '</a>';
            $action = '<span class="float-right">' . $blockUser . $view . ' </span>';

            $data[] = [
                $image,
                $username,
                $item->username,
                $device_type,
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

    public function usersDetail($id)
    {
        $user = User::where('id', $id)->first();
        if ($user) {
            return view('userDetails', [
                'user' => $user,
            ]);
        }
    }

    public function verifyUser(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            $user->is_verified = 2;
            $user->save();

            $notificationDesc = "Wow! Your profile has been verified.";
            GlobalFunction::sendPushNotificationToUser($notificationDesc, $user->device_token, $user->device_type);

            $checkUserInVerificationList = ProfileVerification::where('user_id', $user->id)->first();
            if ($checkUserInVerificationList != null) {
                GlobalFunction::deleteFile($checkUserInVerificationList->selfie);
                GlobalFunction::deleteFile($checkUserInVerificationList->document);
                $checkUserInVerificationList->delete();
            }

            return response()->json([
                'status' => true,
                'message' => 'User Verified Successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User Not Found',
        ]);
    }

    public function userPostsList(Request $request)
    {
        $totalData = Post::where('user_id', $request->userId)->count();
        $rows = Post::where('user_id', $request->userId)
                    ->orderBy('id', 'DESC')
                    ->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'Content',
            2 => 'Thumbnail',
            3 => 'Views',
            4 => 'likes',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = Post::where('user_id', $request->userId)->offset($start)->limit($limit)->orderBy($order, $dir)->get();
        } else {
            $search = $request->input('search.value');
            $result = Post::where('user_id', $request->userId)->Where('name', 'LIKE', "%{$search}%")->offset($start)->limit($limit)->orderBy($order, $dir)->get();
            $totalFiltered = Post::where('user_id', $request->userId)->Where('name', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        $fetchInterests = Interest::get();

        foreach ($result as $item) {

            $postContent = PostContent::where('post_id', $item->id)->get();
            $contentType = $postContent->count() == 0 ? 3 : $postContent->first()->content_type;
            $firstContent = $postContent->pluck('content');

            $interest_ids = explode(',', $item->interest_ids);
            $interest_titles = [];

            foreach ($fetchInterests as $interest) {
                if (in_array($interest->id, $interest_ids)) {
                    $interest_titles[] = $interest->title;
                }
            }

            $interest_titles_string = $fetchInterests
                                    ->whereIn('id', $interest_ids)
                                    ->pluck('title')
                                    ->implode(', '); 

            $profile = $item->user->profile ?? "null";
            if ($contentType == 0) {
                $viewPost = "<button type='button' 
                                    class='btn btn-primary viewPost commonViewBtn' 
                                    data-bs-toggle='modal' 
                                    data-username='{$item->user->username}' 
                                    data-profile='{$profile}' 
                                    data-image='{$firstContent}' 
                                    data-desc='{$item->desc}' 
                                    data-userid='{$item->user->id}' 
                                    data-postid='{$item->id}' 
                                    data-interests='{$interest_titles_string}'
                                    rel='{$item->id}'>
                <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-image'><rect x='3' y='3' width='18' height='18' rx='2' ry='2'></rect><circle cx='8.5' cy='8.5' r='1.5'></circle><polyline points='21 15 16 10 5 21'></polyline></svg> View Post</button>";
            } elseif ($contentType == 1) {
                $viewPost = "<button type='button' 
                                    class='btn btn-primary viewVideoPost commonViewBtn' 
                                    data-bs-toggle='modal' 
                                    data-username='{$item->user->username}'
                                    data-profile='{$profile}'
                                    data-userid='{$item->user->id}'
                                    data-image='{$firstContent}' 
                                    data-desc='{$item->desc}' 
                                    data-userid='{$item->user->id}' 
                                    data-postid='{$item->id}' 
                                    data-interests='{$interest_titles_string}'
                                    rel='{$item->id}'>
                <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-video'><polygon points='23 7 16 12 23 17 23 7'></polygon><rect x='1' y='5' width='15' height='14' rx='2' ry='2'></rect></svg> View Post</button>";
            } elseif ($contentType == 2) {
                $firstContent = $postContent->pluck('content')->first();
                $viewPost = "<button type='button' 
                                    class='btn btn-primary viewAudioPost commonViewBtn' 
                                    data-bs-toggle='modal'
                                    data-username='{$item->user->username}'
                                    data-profile='{$profile}'
                                    data-audio='{$firstContent}'
                                    data-desc='{$item->desc}'
                                    data-userid='{$item->user->id}'
                                    data-postid='{$item->id}'
                                    data-interests='{$interest_titles_string}'
                                    rel='{$item->id}'>
                <svg viewBox='0 0 24 24' width='24' height='24' stroke='currentColor' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round' class='css-i6dzq1'><path d='M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z'></path><path d='M19 10v2a7 7 0 0 1-14 0v-2'></path><line x1='12' y1='19' x2='12' y2='23'></line><line x1='8' y1='23' x2='16' y2='23'></line></svg> View Post</button>";
            } else {
                $viewPost = "<button type='button'
                                    class='btn btn-primary viewDescPost commonViewBtn'
                                    data-bs-toggle='modal'
                                    data-username='{$item->user->username}'
                                    data-profile='{$profile}'
                                    data-desc='{$item->desc}'
                                    data-userid='{$item->user->id}'
                                    data-postid='{$item->id}'
                                    data-interests='{$interest_titles_string}'
                                    rel='{$item->id}'>
                <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-type'><polyline points='4 7 4 4 20 4 20 7'></polyline><line x1='9' y1='20' x2='15' y2='20'></line><line x1='12' y1='4' x2='12' y2='20'></line></svg> View Post</button>";
            }


            $restricted = '<label class="switch"><input type="checkbox" name="restricted" rel="' . $item->id . '" value="' . $item->is_restricted . '" id="postRestricted" class="postRestricted"' . ($item->is_restricted == 1 ? ' checked' : '') . '><span class="slider"></span> </label>';

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deletePost d-flex align-items-center" rel=' . $item->id . ' data-tooltip="Delete Post">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $delete . ' </span>';

            $data[] = [
                $viewPost,
                $item->comments_count,
                $item->likes_count,
                $item->created_at->format('d-m-Y'),
                $restricted,
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

    public function blockUserByAdmin($id)
    {
        $user = User::where('id', $id)
            ->get()
            ->first();

        if ($user) {
            $user->is_block = 1;
            $user->save();

            return response()->json([ 
                'status' => true,
                'message' => 'User Added in Blocklist',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
    }

    public function unblockUserByAdmin($id)
    {
        $user = User::where('id', $id)
            ->get()
            ->first();

        if ($user) {
            $user->is_block = 0;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User Added in Blocklist',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
    }

    public function deletePostFromUserPostTable(Request $request)
    {
        $post = Post::where('id', $request->post_id)->first();
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post Not Found',
            ]);
        }

        $postContents = PostContent::where('post_id', $request->post_id)->get();
        foreach ($postContents as $postContent) {
            GlobalFunction::deleteFile($postContent->content);
            GlobalFunction::deleteFile($postContent->thumb);
        }
        $postContents->each->delete();

        $postComments = Comment::where('post_id', $request->post_id)->get();
        $postComments->each->delete();

        $postLikes = Like::where('post_id', $request->post_id)->get();
        $postLikes->each->delete(); 

        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post Delete Successfully',
        ]);
       
    }

    public function addUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identity' => 'required',
            'login_type' => 'required',
            'device_type' => 'required',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('identity', $request->identity)->first();

        if ($user) {
            $user->device_type = (int) $request->device_type;
            $user->device_token = $request->device_token;
            $user->save();
            return response()->json([
                'status' => false,
                'message' => 'User is already exist',
                'data' => $user,
            ]);
        } else {
            $user = new User();
            $user->identity = $request->identity;
            $user->full_name = $request->full_name;
            $user->login_type = (int) $request->login_type;
            $user->device_type = (int) $request->device_type;
            $user->device_token = $request->device_token;
            $user->save();
            $user = User::where('id', $user->id)->first();
            return response()->json([
                'status' => true,
                'message' => 'User Added succesfully',
                'data' => $user,
            ]);
        }
    }

    public function editProfile(Request $request)
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
            if ($request->has('username')) {
                $user->username = $request->username;
            }
            if ($request->hasFile('profile')) {
                $path = GlobalFunction::deleteFile($user->profile);
                $file = $request->file('profile');
                $path = GlobalFunction::saveFileAndGivePath($file);
                $user->profile = $path;
            }
            if ($request->hasFile('background_image')) {
                GlobalFunction::deleteFile($user->background_image);
                $file = $request->file('background_image');
                $path = GlobalFunction::saveFileAndGivePath($file);
                $user->background_image = $path;
            }
            if ($request->has('bio')) {
                $user->bio = $request->bio;
            }
            if ($request->has('full_name')) {
                $user->full_name = $request->full_name;
            }
            if ($request->has('interest_ids')) {
                $user->interest_ids = $request->interest_ids;
            }
            if ($request->has('block_user_ids')) {
                $user->block_user_ids = $request->block_user_ids;
            }
            if ($request->has('saved_music_ids')) {
                $user->saved_music_ids = $request->saved_music_ids;
            }
            if ($request->has('saved_reel_ids')) {
                $user->saved_reel_ids = $request->saved_reel_ids;
            }
            if ($request->has('is_push_notifications')) {
                $user->is_push_notifications = (int) $request->is_push_notifications;
            }
            if ($request->has('is_invited_to_room')) {
                $user->is_invited_to_room = (int) $request->is_invited_to_room;
            }
            if ($request->has('is_verified')) {
                $user->is_verified = (int) $request->is_verified;
            }            
            if ($request->has('device_token')) {
                $user->device_token = $request->device_token;
            }            
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User Updated Successfully',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function followUser(Request $request)
    {
        $fromUser = User::where('id', $request->my_user_id)->first();
        $toUser = User::where('id', $request->user_id)->first();

        if ($fromUser && $toUser) {
            if ($fromUser == $toUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lol You did not follow yourself',
                ]);
            } else {
                $followingList = FollowingList::where('my_user_id', $request->my_user_id)->where('user_id', $request->user_id)->first();
                if ($followingList) {
                    return response()->json([
                        'status' => false,
                        'message' => 'User is Already in following list',
                    ]);
                } else {

                    $blockUserIds = explode(',', $fromUser->block_user_ids);

                    foreach ($blockUserIds as $blockUserId) {
                        if ($blockUserId == $request->user_id) {
                            return response()->json([
                                'status' => false,
                                'message' => 'You blocked this User',
                            ]);
                        }
                    }

                    $following = new FollowingList();
                    $following->my_user_id = (int) $request->my_user_id;
                    $following->user_id = (int) $request->user_id;
                    $following->save();

                    $followingCount = User::where('id', $request->my_user_id)->first();
                    $followingCount->following += 1;
                    $followingCount->save();

                    $followersCount = User::where('id', $request->user_id)->first();
                    $followersCount->followers += 1;
                    $followersCount->save();

                    if($toUser->is_push_notifications == 1) {
                        $notificationDesc = $fromUser->full_name . ' has stared following you.';
                        GlobalFunction::sendPushNotificationToUser($notificationDesc, $toUser->device_token, $toUser->device_type);
                    }

                    $following->user = $fromUser;

                    $type = Constants::notificationTypeFollow;

                    $savedNotification = new SavedNotification();
                    $savedNotification->my_user_id = (int) $request->user_id;
                    $savedNotification->user_id = (int) $request->my_user_id;
                    $savedNotification->type = $type;
                    $savedNotification->save();

                    return response()->json([
                        'status' => true,
                        'message' => 'User Added in Following List',
                        'data' => $following,
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

    }

    public function fetchFollowingList(Request $request)
    {
        $user = User::where('id', $request->my_user_id)->first();
        $blockUserIds = explode(',', $user->block_user_ids);

        $fetchFollowingList = FollowingList::whereRelation('user', 'is_block', 0)
                                            ->whereNotIn('user_id', $blockUserIds)
                                            ->where('my_user_id', $request->my_user_id)
                                            ->with('user')
                                            ->offset($request->start)
                                            ->limit($request->limit)
                                            ->get()
                                            ->pluck('user');

        return response()->json([
            'status' => true,
            'message' => 'Fetch Following List',
            'data' => $fetchFollowingList,
        ]);
    }

    public function fetchFollowersList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'keyword' => 'nullable|string',
            'start' => 'nullable|integer',
            'limit' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $query = FollowingList::where('user_id', $request->user_id)
            ->with(['followerUser' => function ($query) use ($request) {
                $query->where('is_block', 0);
                if ($request->keyword) {
                    $query->where(function ($q) use ($request) {
                        $q->where('full_name', 'like', '%' . $request->keyword . '%')
                            ->orWhere('username', 'like', '%' . $request->keyword . '%');
                    });
                }
            }]);

        if ($request->start) {
            $query->offset($request->start);
        }

        if ($request->limit) {
            $query->limit($request->limit);
        }

        $fetchFollowersList = $query->get()->pluck('followerUser')->filter()->values();

        return response()->json([
            'status' => true,
            'message' => 'Fetch Followers List',
            'data' => $fetchFollowersList,
        ]);
    }

    public function unfollowUser(Request $request)
    {
        $user = User::where('id', $request->my_user_id)->first();
        $user1 = User::where('id', $request->user_id)->first();

        if ($user && $user1) {
            if ($user == $user1) {
                return response()->json([
                    'status' => false,
                    'message' => 'Lol You did not Remove yourself, Bcz You dont follow yourself',
                ]);
            } else {
                $followingList = FollowingList::where('my_user_id', $request->my_user_id)->where('user_id', $request->user_id)->first();
                if ($followingList) {
                    $followingCount = User::where('id', $request->my_user_id)->first();
                    $followingCount->following = max(0, $followingCount->following - 1);
                    $followingCount->save();

                    $followersCount = User::where('id', $request->user_id)->first();
                    $followersCount->followers = max(0, $followersCount->followers - 1);
                    $followersCount->save();

                    $userNotification = SavedNotification::where('my_user_id', $request->user_id)
                                                            ->where('user_id', $request->my_user_id)
                                                            ->where('type', Constants::notificationTypeFollow)
                                                            ->get();
                    $userNotification->each->delete();

                    $followingList->delete();



                    return response()->json([
                        'status' => true,
                        'message' => 'Unfollow user',
                        'data' => $followingList,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'User Not Found',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function checkUsername(Request $request)
    {
        $user = User::where('username', $request->username)->first();
        if ($user == null) {
            return response()->json([
                'status' => true,
                'message' => 'Username is available',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Username is not available',
            ]);
        }
    }

    public function fetchRandomProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->my_user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not Found',
            ]);
        }

        $blockUserIds = explode(',', $user->block_user_ids);
        $interestsIds = $user->interest_ids ? explode(',', $user->interest_ids) : null;

        if (!$interestsIds) {
            return response()->json([
                'status' => false,
                'message' => 'Interests not found',
            ]);
        }

        shuffle($interestsIds);
        $randomUser = null;
        foreach ($interestsIds as $interestId) {
            $randomUser = User::whereNotIn('id', $blockUserIds)
                ->where('is_block', 0)
                ->where('id', '!=', $request->my_user_id)
                ->whereNotNull('bio')
                ->whereRaw('find_in_set("' . $interestId . '", interest_ids)')
                ->inRandomOrder()
                ->first();

            if ($randomUser) {
                break;
            }
        }
        if (!$randomUser) {
            $randomUser = User::whereNotIn('id', $blockUserIds)
                                ->where('is_block', 0)
                                ->where('id', '!=', $request->my_user_id)
                                ->whereNotNull('bio')
                                ->inRandomOrder()
                                ->first();
        }

        return response()->json([
            'status' => true,
            'message' => 'Random profile found',
            'data' => $randomUser,
        ]);    
    }

    public function userReportList(Request $request)
    {
        $reportType = 2;
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
                ->Where('reason', 'LIKE', "%{$search}%")
                ->orWhere('desc', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = Report::where('type', $reportType)
                ->Where('reason', 'LIKE', "%{$search}%")
                ->orWhere('desc', 'LIKE', "%{$search}%")
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

            $rejectReport = '<a href="#" class="me-3 btn btn-orange px-4 text-white rejectReport d-flex align-items-center" rel=' . $item->id . ' data-tooltip="Reject Report" >' . __(' <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> <span class="ms-2"> Reject </span>') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete blockUserBtn d-flex align-items-center " rel=' . $item->id . ' data-tooltip="Block User">' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="18" y1="8" x2="23" y2="13"></line><line x1="23" y1="8" x2="18" y2="13"></line></svg> <span class="ms-2"> Block User </span> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $rejectReport . $delete . ' </span>';

            $data[] = [$image, $userData->full_name, $userData->identity, $item->reason, $item->desc, $action];
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

    public function reportUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'reason' => 'required',
            'desc' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)->first();

        if ($user != null) {
            if ($user->is_block == 1) {
                return response()->json([
                    'status' => false,
                    'message' => 'User is already block',
                ]);
            }

            $reportType = 2;

            $report = new Report;
            $report->type = $reportType;
            $report->user_id = $request->user_id;
            $report->reason = $request->reason;
            $report->desc = $request->desc;
            $report->save();

            return response()->json([
                'status' => true,
                'message' => 'User Report Added Successfully',
                'data' => $report,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function deleteUserReport(Request $request)
    {
        $report = Report::where('id', $request->report_id)->first();
        if ($report) {
            $userReports = Report::where('user_id', $report->user_id)->get();

            $userReports->each->delete();

            return response()->json([
                'status' => true,
                'message' => 'Report Delete Successfully',
                'data' => $userReports
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Report Not Found',
            ]);
        }
    }

    public function blockUserFromReport(Request $request)
    {
        $report = Report::where('id', $request->report_id)->first();

        if ($report) {

            $user = User::where('id', $report->user_id)->first();
            $user->is_block = 1;
            $user->save();

            $reportUsers = Report::where('user_id', $report->user_id)->get();
            $reportUsers->each->delete();

            return response()->json([
                'status' => true,
                'message' => 'User Added in Blocklist',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
    }

    public function fetchPostByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'my_user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            $fetchPosts = Post::where('user_id', $request->user_id)->with(['content', 'user'])->orderBy('created_at', 'desc')->offset($request->start)->limit($request->limit)->get();

            foreach ($fetchPosts as $fetchPost) {
                $isPostLike = Like::where('user_id', $request->my_user_id)->where('post_id', $fetchPost->id)->first();
                if ($isPostLike) {
                    $fetchPost->is_like = 1;
                } else {
                    $fetchPost->is_like = 0;
                }


                $blockUserIds = User::where('is_block', 1)->pluck('id');

                $comments_count = Comment::whereNotIn('user_id', $blockUserIds)->where('post_id', $fetchPost->id)->count();
                $likes_count = Like::whereNotIn('user_id', $blockUserIds)->where('post_id', $fetchPost->id)->count();

                $fetchPost->comments_count = $comments_count;
                $fetchPost->likes_count = $likes_count;


            }

            return response()->json([
                'status' => true,
                'message' => 'Fetch post successfully',
                'data' => $fetchPosts,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);
    }

    public function fetchProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $profile = User::where('id', $request->user_id)->first();

        if($profile) {

            $followingStatus = FollowingList::whereRelation('user', 'is_block', 0)->where('user_id', $request->my_user_id)->where('my_user_id', $request->user_id)->first();
            $followingStatus2 = FollowingList::whereRelation('user', 'is_block', 0)->where('my_user_id', $request->my_user_id)->where('user_id', $request->user_id)->first();

            // koi ek bija ne follow nathi kartu to 0
            if ($followingStatus == null && $followingStatus2 == null) {
                $profile->followingStatus = 0;
            }
            // same valo mane follow kar che to 1
            if ($followingStatus != null) {
                $profile->followingStatus = 1;
            }
            // hu same vala ne follow karu chu to 2
            if ($followingStatus2) {
                $profile->followingStatus = 2;
            }
            // banne ek bija ne follow kare to 3
            if ($followingStatus && $followingStatus2) {
                $profile->followingStatus = 3;
            }
            $stories = Story::where('user_id', $request->user_id)->where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())->get();
            $profile->stories = $stories;

            $interest = Interest::whereIn('id', explode(',', $profile->interest_ids))->get();
            $profile->interest = $interest;

            // $blockUserIds = User::where('is_block', 1)->get()->pluck('id');
            $blockUserIds = explode(',', $profile->block_user_ids);


            // foreach ($blockUserIds as $blockUserId) {
            //     if ($request->my_user_id == $blockUserId) {
            //         return response()->json([
            //             'status' => false,
            //             'message' => 'you are blocked by this user'
            //         ]);
            //     }
            // }

            // $followersUserCount = FollowingList::whereRelation('user', 'is_block', 0)->where('user_id', $request->user_id)->whereNotIn('my_user_id', $blockUserIds)->count();
            // $followersUserCount = FollowingList::whereRelation('followerUser', 'is_block', 0)
            //                     ->whereNotIn('user_id', $blockUserIds)
            //                     ->where('user_id', $request->user_id)
            //                     ->count();
            // $profile->followers = $followersUserCount;

            // $followingUserCount = FollowingList::whereRelation('user', 'is_block', 0)->where('user_id', $request->my_user_id)->whereNotIn('my_user_id', $blockUserIds)->count();
            // $followingUserCount = FollowingList::whereRelation('user', 'is_block', 0)
            //     ->whereNotIn('user_id', $blockUserIds)
            //     ->where('my_user_id', $request->my_user_id)
            //     ->count();
            // $profile->following = $followingUserCount;


            return response()->json([
                'status' => true,
                'message' => 'Getting profile successfully',
                'data' => $profile,
            ]);

        }
         return response()->json([
            'status' => false,
            'message' => 'Profile Not found',
        ]);
    }

    public function deleteUser(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $userPosts = Post::where('user_id', $request->user_id)->get();

        foreach ($userPosts as $userPost) {
            foreach ($userPost as $userOnlyOnePost) {
                $userOnlyOnePost = PostContent::where('post_id', $userPost->id)->first();
                if($userOnlyOnePost != null) {
                    GlobalFunction::deleteFile($userOnlyOnePost->content);
                    GlobalFunction::deleteFile($userOnlyOnePost->thumbnail);
                    $userOnlyOnePost->delete();
                }
            }
            $myComments = Comment::where('post_id', $userPost->id)->get();
            foreach ($myComments as $myComment) {
                $myComment->delete();
            }
            $myPostsLikes = Like::where('post_id', $userPost->id)->get();
            foreach ($myPostsLikes as $myPostsLike) {
                $myPostsLike->delete();
            }
        }

        Comment::where('user_id', $request->user_id)->delete();

        $userLikes = Like::where('user_id', $request->user_id)->get();

        foreach ($userLikes as $userLike) {
            $removeLikeFromPost = Post::where('id', $userLike->post_id)->first();
            if ($removeLikeFromPost != null) {
                $removeLikeFromPost->likes_count = max(0, $removeLikeFromPost->likes_count - 1);
                $removeLikeFromPost->save();
            }
            $userLike->delete();
        }

        $userfollowings = FollowingList::where('my_user_id', $request->user_id)->get();
        foreach ($userfollowings as $userfollowing) {
            $userFollowers = User::where('id', $userfollowing->user_id)->first();
            $userFollowers->followers = max(0, $userFollowers->followers - 1);
            $userFollowers->save();
            $userfollowing->delete();
        }

        $removefollowings = FollowingList::where('user_id', $request->user_id)->get();
        foreach ($removefollowings as $removefollowing) {
            $removeUserFollowing = User::where('id', $removefollowing->my_user_id)->first();
            $removeUserFollowing->following = max(0, $removeUserFollowing->following - 1);
            $removeUserFollowing->save();
            $removefollowing->delete();
        }

        $userPosts->each->delete();

        Story::where('user_id',$request->user_id)->delete();
        Report::where('user_id', $request->user_id)->delete();
        ProfileVerification::where('user_id', $request->user_id)->delete();

        $deleteRooms = Room::where('admin_id', $request->user_id)->get();
        foreach ($deleteRooms as $deleteRoom) {
            if ($deleteRoom->photo != null) {
                GlobalFunction::deleteFile($deleteRoom->photo);
            }
            RoomUser::where('room_id',  $deleteRoom->id)->delete();
            $deleteRoom->delete();
        }

        $reels = Reel::where('user_id', $request->user_id)->get();

        foreach ($reels as $reel) {
            $reelComments = ReelComment::where('reel_id', $reel->id)->get();
            foreach ($reelComments as $reelComment) {
                SavedNotification::where('reel_comment_id', $reelComment->id)->delete();
                $reelComment->delete();
            }

            Like::where('reel_id', $reel->id)->delete();

            GlobalFunction::deleteFile($reel->content);
            GlobalFunction::deleteFile($reel->thumbnail);

            SavedNotification::where('reel_id', $reel->id)->delete();

            $reel->delete();
        }

        RoomUser::where('user_id', $request->user_id)->delete();
        SavedNotification::where('user_id', $request->user_id)->delete();
        SavedNotification::where('my_user_id', $request->user_id)->delete();
        GlobalFunction::deleteFile($user->profile);
        GlobalFunction::deleteFile($user->background_image);
        
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User Delete successfully',
            'data' => $user,
        ]);
    }

    public function searchProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $myProfile = User::where('id', $request->my_user_id)->first();
        $blockUserIds = explode(',', $myProfile->block_user_ids);

        $user = User::where('is_block', 0)
                    ->where(function($query) use ($request) {
                        $query->where('username', 'like', '%' . $request->keyword .'%');
                        $query->orWhere('full_name', 'like', '%' . $request->keyword .'%');
                    })
                    ->whereNotIn('id', $blockUserIds)
                    ->offset($request->start)
                    ->limit($request->limit)
                    ->orderByDesc('id')
                    ->get();
        return response()->json([
            'status' => true,
            'message' => 'User profile',
            'data' => $user
        ]);
    }

    public function fetchBlockedUserList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->my_user_id)->first();
        if($user) {
            $blockUserIds = explode(',', $user->block_user_ids);

            $blockedUser = User::whereIn('id', $blockUserIds)->get();
            return response()->json([
                'status' => true,
                'message' => 'Fetch blocked user list successfully',
                'data' => $blockedUser
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ]);
        }
    }

    public function logOut(Request $request)
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
        if($user) {

            $user->device_token = null;
            $user->save();
            return response()->json([
                'status' => true,
                'message' => 'User logout successfully',
                'data' => $user,
            ]);

        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);



    }

    public function editProfileFormWeb(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if ($user) {
            if ($request->has('username')) {
                $user->username = $request->username;
            }
            if ($request->hasFile('profile')) {
                $path = GlobalFunction::deleteFile($user->profile);
                $file = $request->file('profile');
                $path = GlobalFunction::saveFileAndGivePath($file);
                $user->profile = $path;
            }
            if ($request->hasFile('background_image')) {
                GlobalFunction::deleteFile($user->background_image);
                $file = $request->file('background_image');
                $path = GlobalFunction::saveFileAndGivePath($file);
                $user->background_image = $path;
            }
            if ($request->has('bio')) {
                $user->bio = $request->bio;
            }
            if ($request->has('full_name')) {
                $user->full_name = $request->full_name;
            }
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'User Updated Successfully',
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function UserBlockedByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $fromUser = User::where('id', $request->my_user_id)->first();
        if ($fromUser == null) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $toUser = User::where('id', $request->user_id)->first();
        if ($toUser == null) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $fetchFollowingUsers = FollowingList::where('my_user_id', $request->my_user_id)->where('user_id', $request->user_id)->first();
        if ($fetchFollowingUsers != null) {
            $followingCount = User::where('id', $request->my_user_id)->first();
            $followingCount->following = max(0, $followingCount->following - 1);
            $followingCount->save();

            $followersCount = User::where('id', $request->user_id)->first();
            $followersCount->followers = max(0, $followersCount->followers - 1);
            $followersCount->save();

            $fetchFollowingUsers->delete();
        }


        $fetchFollowerUsers = FollowingList::where('user_id', $request->my_user_id)->where('my_user_id', $request->user_id)->first();
        if ($fetchFollowerUsers != null) {
            $followersCount = User::where('id', $request->my_user_id)->first();
            $followersCount->followers = max(0, $followersCount->followers - 1);
            $followersCount->save();

            $followingCount = User::where('id', $request->user_id)->first();
            $followingCount->following = max(0, $followingCount->following - 1);
            $followingCount->save();

            $fetchFollowerUsers->delete();
        }

        $blockUserIds = explode(',', $fromUser->block_user_ids);
        foreach ($blockUserIds as $blockUserId) {
            if ($blockUserId == $request->user_id) {
                return response()->json([
                    'status' => false,
                    'message' => 'User already Blocked'
                ]);
            }
        }

        $fromUser->block_user_ids = $fromUser->block_user_ids . $request->user_id . ',';
        $fromUser->save();

        $userNotification = SavedNotification::where('my_user_id', $request->my_user_id)
                                             ->where('type', Constants::notificationTypeFollow)
                                             ->get();
        $userNotification->each->delete();

        return response()->json([
            'status' => true,
            'message' => 'User Block Successfully',
            'data' => $toUser
        ]);
    }

    public function UserUnblockedByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $fromUser = User::where('id', $request->my_user_id)->first();
        if ($fromUser == null) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $toUser = User::where('id', $request->user_id)->first();
        if ($toUser == null) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $blockUserIds = explode(',', $fromUser->block_user_ids);
        foreach (array_keys($blockUserIds, $request->user_id) as $key) {
            unset($blockUserIds[$key]);
        }
        $fromUser->block_user_ids = implode(",", $blockUserIds);
        $fromUser->save();

        return response()->json([
            'status' => true,
            'message' => 'User Unblock Successfully',
            'data' => $fromUser
        ]);


    }

    public function fetchUserNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $savedNotification = SavedNotification::where('my_user_id', $request->my_user_id)
                                            ->with(['user', 'post', 'room', 'reel'])
                                            ->offset($request->start)
                                            ->limit($request->limit)
                                            ->orderBy('created_at', 'desc')
                                            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Fetch Saved Notification Successfully',
            'data' => $savedNotification
        ]);

    }

    public function updateModeratorStatus(Request $request)
    {
        $user = User::where('id', $request->id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Room not found',
            ]);
        }

        if($user->is_push_notifications == 1) {
            if ($request->is_moderator == 1) {
                $notificationDesc = 'You are Moderator now.';
                GlobalFunction::sendPushNotificationToUser($notificationDesc, $user->device_token, $user->device_type);
            }
        }

        $user->is_moderator = $request->is_moderator;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'User Updated successfully',
        ]);
      
    }

    public function deletePostByModerator(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        if ($user->is_moderator !== Constants::moderator) {
            return response()->json([
                'status' => false,
                'message' => 'User is not a Moderator',
            ]);
        }

        $post = Post::find($request->post_id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post Not Found',
            ]);
        }

        $postContents = PostContent::where('post_id', $request->post_id)->get();
        foreach ($postContents as $postContent) {
            GlobalFunction::deleteFile($postContent->content);
            GlobalFunction::deleteFile($postContent->thumb);
        }
        $postContents->each->delete();

        Comment::where('post_id', $request->post_id)->delete();
        Like::where('post_id', $request->post_id)->delete();
        SavedNotification::where('post_id', $request->post_id)->delete();
        Report::where('post_id', $request->post_id)->where('type', 1)->delete();

        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post Deleted Successfully',
        ]);
    }

    public function deleteCommentByModerator(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        if ($user->is_moderator !== Constants::moderator) {
            return response()->json([
                'status' => false,
                'message' => 'User is not a Moderator',
            ]);
        }

        $comment = Comment::where('id', $request->comment_id)->first();

        if (!$comment) {
            return response()->json([
                'status' => false,
                'message' => 'Comment not found'
            ]);
        }

        $commentCount = Post::where('id', $comment->post_id)->first();
        $commentCount->comments_count = max(0, $commentCount->comments_count - 1);
        $commentCount->save();

        SavedNotification::where('user_id', $request->user_id)
                            ->where('post_id', $comment->post_id)
                            ->where('type', Constants::notificationTypeComment)
                            ->delete();
        
        $comment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Comment Delete successfully',
            'data' => $comment
        ]);


        
    }

    public function deleteRoomByModerator(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        if ($user->is_moderator !== Constants::moderator) {
            return response()->json([
                'status' => false,
                'message' => 'User is not a Moderator',
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
        Report::where('room_id', $request->room_id)->where('type', 1)->delete();

        $room->delete();

        return response()->json([
            'status' => true,
            'message' => 'Room deleted successfully',
            'data' => $room,
        ]);
    }

    public function deleteStoryByModerator(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        if ($user->is_moderator !== Constants::moderator) {
            return response()->json([
                'status' => false,
                'message' => 'User is not a Moderator',
            ]);
        }

        $story = Story::where('id', $request->story_id)->first();
        if (!$story) {
            return response()->json([
                'status' => false,
                'message' => 'Story not found',
            ]);
        }

        GlobalFunction::deleteFile($story->content);

        $story->delete();

        return response()->json([
            'status' => true,
            'message' => 'Story deleted successfully',
            'data' => $story,
        ]);
    }

    public function userBlockByModerator(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        if ($user->is_moderator !== Constants::moderator) {
            return response()->json([
                'status' => false,
                'message' => 'User is not a Moderator',
            ]);
        }

        $toUser = User::where('id', $request->to_user_id)->first();
        
        if (!$toUser) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        if ($request->user_id == $request->to_user_id) {
            return response()->json([
                'status' => false,
                'message' => 'Lol, You can not block yourself',
            ]);
        }

        $toUser = User::where('id', $request->to_user_id)->first(); 
        $toUser->is_block = 1;
        $toUser->save();

        $reportUsers = Report::where('user_id', $request->to_user_id)->get();
        $reportUsers->each->delete();

        return response()->json([
            'status' => true,
            'message' => 'User Blocked successfully',
            'data' => $toUser,
        ]);
    }

    public function deleteReelCommentByModerator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'reel_comment_id' => 'required|exists:reel_comments,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::find($request->user_id);
        
        if ($user->is_moderator !== Constants::moderator) {
            return response()->json([
                'status' => false,
                'message' => 'User is not a Moderator',
            ]);
        }

        $reelComment = ReelComment::where('id', $request->reel_comment_id)->first();
        if (!$reelComment) {
            return response()->json([
                'status' => false,
                'message' => 'Reel Comment not found.',
            ]);
        }

        $reel = Reel::findOrFail($reelComment->reel_id);
        $reel->comments_count = max(0, $reel->comments_count - 1);
        $reel->save();

        SavedNotification::where('reel_comment_id', $request->comment_id)
                        ->where('type', Constants::notificationTypeAddReelComment)
                        ->delete();

        $reelComment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Reel Comment Delete Successfully By Moderator.',
            'data' => $reelComment,
        ]);
    }

    public function deleteReelByModerator(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'reel_id' => 'required|exists:reels,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::find($request->user_id);

        if ($user->is_moderator !== Constants::moderator) {
            return response()->json([
                'status' => false,
                'message' => 'User is not a Moderator',
            ]);
        }

        $reel = Reel::find($request->reel_id);

        $reelComments = ReelComment::where('reel_id', $request->reel_id)->get();
        foreach ($reelComments as $reelComment) {
            SavedNotification::where('reel_comment_id', $reelComment->id)
                ->whereIn('type', [
                    Constants::notificationTypeReelLike,
                    Constants::notificationTypeComment,
                    Constants::notificationTypeAddReelComment
                ])
                ->delete();
            $reelComment->delete();
        }

        Like::where('reel_id', $request->reel_id)->delete();

        GlobalFunction::deleteFile($reel->content);
        GlobalFunction::deleteFile($reel->thumbnail);

        SavedNotification::where('reel_id', $request->reel_id)
            ->whereIn('type', [
                Constants::notificationTypeReelLike,
                Constants::notificationTypeComment,
                Constants::notificationTypeAddReelComment
            ])
            ->delete();

        $reel->delete();

        return response()->json([
            'status' => true,
            'message' => 'Reel Deleted Successfully By Moderator.',
            'data' => $reel,
        ]);
    }

    public function deleteAvatarFromUserDetail(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        GlobalFunction::deleteFile($user->background_image);
        $user->background_image = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Delete Background Image',
            'data' => $user,
        ]);
    }

    public function deleteProfileFromUserDetail(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        GlobalFunction::deleteFile($user->profile);
        $user->profile = null;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Delete Background Image',
            'data' => $user,
        ]);
    }
}