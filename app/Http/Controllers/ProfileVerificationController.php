<?php

namespace App\Http\Controllers;

use App\Models\GlobalFunction;
use App\Models\ProfileVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileVerificationController extends Controller
{
    public function verificationRequests()
    {
        return view('verificationRequests');
    }

    public function profileVerification(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();

        if ($user->is_verified == 0) {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'full_name' => 'required',
                'document_type' => 'required',
                'document' => 'required',
                'selfie' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->messages()->first(),
                ]);
            }

            $profileVerification = new ProfileVerification();
            $profileVerification->user_id = $request->user_id;

            if ($request->hasFile('selfie')) {
                $file = $request->file('selfie');
                $path = GlobalFunction::saveFileAndGivePath($file);
                $profileVerification->selfie = $path;
            }
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $path = GlobalFunction::saveFileAndGivePath($file);
                $profileVerification->document = $path;
            }
            $profileVerification->document_type = $request->document_type;
            $profileVerification->full_name = $request->full_name;
            $profileVerification->save();

            $is_verified = 1;

            $user->is_verified = $is_verified;
            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile verification added Successfully',
                'data' => $profileVerification,
            ]);
        } elseif ($user->is_verified == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Profile verification in pending',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Profile is already verified',
            ]);
        }
    }

    public function profileVerificationList(Request $request)
    {
        $totalData = ProfileVerification::count();
        $rows = ProfileVerification::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'user_id',
            2 => 'selfie',
            3 => 'document',
            4 => 'document_type',
            5 => 'full_name',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = ProfileVerification::offset($start)
                                            ->limit($limit)
                                            ->orderBy($order, $dir)
                                            ->get();
        } else {
            $search = $request->input('search.value');
            $result = ProfileVerification::Where('name', 'LIKE', "%{$search}%")
                                            ->offset($start)
                                            ->limit($limit)
                                            ->orderBy($order, $dir)
                                            ->get();
            $totalFiltered = ProfileVerification::Where('name', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {
            $userData = User::where('id', $item->user_id)->get()->first();

            $userName = '<a href="./usersDetail/' . $userData->id . '">'. $userData->username .'</a>';
            $fullName = '<span style="text-wrap: wrap;">'. $item->full_name .'</span>';

            $image = '<img src="' . $item->selfie . '" data-image="' . $item->selfie . '" data-modal_title="Selfie image"  width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';

            $documentImg = '<img src="' . $item->document . '"  data-image="' . $item->document . '" data-modal_title="Document image" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;" data-bs-toggle="modal">';

            if ($userData->profile == null) {
                $profileImage = '<img src="asset/image/default.png" data-image="asset/image/default.png" data-modal_title="User Profile image" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            } else {
                $profileImage = '<img src="' . $userData->profile . '" data-image="' . $userData->profile . '" data-modal_title="User Profile image" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            }

            $approved = '<a href="#" class="me-3 btn btn-success px-4 text-white d-flex align-items-center approved" rel=' . $item->user_id . ' >' . __(' Approve') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white d-flex align-items-center reject" rel=' . $item->user_id . ' >' . __('Reject') . '</a>';
            $action = '<span class="float-right d-flex">' . $approved . $delete . ' </span>';

            $data[] = [
                $profileImage, 
                $image, 
                $documentImg, 
                $item->document_type,
                $fullName,
                $userName, 
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

    public function approvedProfileVerification(Request $request, $id)
    {
        $user = User::where('id', $id)->get()->first();
        if ($user) {
            $user->is_verified = 2;
            $user->save();

            $profileVerification = ProfileVerification::where('user_id', $request->id)->get()->first();

            $path = GlobalFunction::deleteFile($profileVerification->selfie);
            $path = GlobalFunction::deleteFile($profileVerification->document);

            $profileVerification->delete();

            return response()->json([
                'status' => true,
                'message' => 'Record Approved Successfully',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Profile Not Found',
        ]);
    }
    
    public function rejectProfileVerification(Request $request, $id)
    {
        $user = User::where('id', $id)->get()->first();
        if ($user) {
            $user->is_verified = 0;
            $user->save();

            $profileVerification = ProfileVerification::where('user_id', $request->id)->get()->first();

            $path = GlobalFunction::deleteFile($profileVerification->selfie);
            $path1 = GlobalFunction::deleteFile($profileVerification->document);

            $profileVerification->delete();

            return response()->json([
                'status' => true,
                'message' => 'Record Rejected',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Profile Not Found',
        ]);
    }
}