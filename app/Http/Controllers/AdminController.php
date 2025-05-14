<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Models\GlobalFunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function adminNotificationList(Request $request)
    {
        $totalData = AdminNotification::count();
        $rows = AdminNotification::orderBy('id', 'DESC')->get();
        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'title',
            2 => 'description',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = AdminNotification::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = AdminNotification::Where('title', 'LIKE', "%{$search}%")->orWhere('description', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = AdminNotification::Where('title', 'LIKE', "%{$search}%")->orWhere('description', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {

            $description = '<span class="itemDescription">'. $item->description .'</span>';

            $repeat = '<a href="#" data-title="' . $item->title . '" data-description="' . $item->description . '" class="me-3 btn btn-info px-4 text-white repeat" rel=' . $item->id . ' data-tooltip="Send Repeat Notification" >' . __('<svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>') . '</a>';
            $edit = '<a href="#" data-title="' . $item->title . '" data-description="' . $item->description . '" class="me-3 btn btn-success px-4 text-white edit" rel=' . $item->id . ' data-tooltip="Edit" >' . __('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete" rel=' . $item->id . ' data-tooltip="Delete" >' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>') . '</a>';
            $action = '<span class="float-right">'. $repeat . $edit . $delete .' </span>' ;

            $data[] = [
                $item->title,
                $description,
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

    function sendNotification(Request $request)
    {

        $adminNotification = new AdminNotification;
        $adminNotification->title = $request->title;
        $adminNotification->description = $request->description;
        $adminNotification->save();

        $title = $request->title;
        $description = $request->description;
      
        GlobalFunction::sendPushNotificationToAllUsers($title, $description);

        return response()->json([
            'status' => true,
            'message' => 'Notification has been send',
        ]);

    }

    function updateNotification(Request $request)
    {
        $adminNotification = AdminNotification::where('id', $request->notificationID)->first();

        if ($adminNotification) {
            $adminNotification->title = $request->title;
            $adminNotification->description = $request->description;
            $adminNotification->save();
 
            return response()->json([
                'status' => true,
                'message' => 'Notification Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Notification Not Found',
            ]);
        }

    }

    function repeatNotification(Request $request)
    {
        $title = $request->title;
        $description = $request->description;
      
        GlobalFunction::sendPushNotificationToAllUsers($title, $description);
         
        return response()->json([
            'status' => true,
            'message' => 'Notification has been send again',
        ]);

    }

    public function deleteNotification(Request $request)
    {
        $notification = AdminNotification::where('id', $request->notification_id)->first();
 
        if ($notification) {
            $notification->delete();
            return response()->json([
                'status' => true,
                'message' => 'Notification Delete Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Notification Not Found',
            ]);
        }
    }

    function notification()
    {
        return view('notification');
    }

    public function changePassword(Request $request)
    {
        $admin = Admin::where('user_type', 1)->get()->first();
        if ($admin) {
            if ($request->has('user_password')) {
                if ($admin->user_password == $request->user_password) {
                    $admin->user_password = $request->new_password;
                    $admin->save();
                    return response()->json([
                        'status' => true,
                        'message' => 'Change Password',
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Old Password does not match',
                    ]);
                }
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Admin not found',
            ]);
        }
    }

    public function fetchPlatformNotification(Request $request)
    {
         $validator = Validator::make($request->all(), [
            'start' => 'required',
            'limit' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $notification = AdminNotification::offset($request->start)->limit($request->limit)->orderBy('id', 'DESC')->get();

        return response()->json([
            'status' => true,
            'message' => 'fetch plateform notification successfully',
            'data' => $notification,
        ]);
    }
}