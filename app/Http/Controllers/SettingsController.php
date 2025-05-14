<?php

namespace App\Http\Controllers;

use App\Classes\AgoraDynamicKey\RtcTokenBuilder;
use App\Models\AdminNotification;
use App\Models\Comment;
use App\Models\DocumentType;
use App\Models\FAQs;
use App\Models\GlobalFunction;
use App\Models\Interest;
use App\Models\Music;
use App\Models\Post;
use App\Models\PostContent;
use App\Models\ProfileVerification;
use App\Models\Reel;
use App\Models\ReelComment;
use App\Models\Report;
use App\Models\ReportReason;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\Setting;
use App\Models\User;
use App\Models\UsernameRestriction;
use Illuminate\Http\Request;
use Google\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Faker\Factory as Faker;

include "./app/Class/AgoraDynamicKey/RtcTokenBuilder.php";

class SettingsController extends Controller
{
    public function fetchUsersForChart()
    {
        $users = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->get()
                    ->keyBy('date'); // Index by date for easier lookup

        // Get the range of dates between the earliest and latest created_at dates
        $startDate = User::orderBy('created_at', 'asc')->value('created_at');
        $endDate = User::orderBy('created_at', 'desc')->value('created_at');

        if (!$startDate || !$endDate) {
            return response()->json([
                'status' => false,
                'message' => 'No User Data Found.',
            ]);
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = new \DateInterval('P1D'); // 1-day interval
        $dateRange = new \DatePeriod($start, $interval, $end->modify('+1 day'));

        $filledData = [];
        foreach ($dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $filledData[] = [
                'date' => $formattedDate,
                'count' => $users[$formattedDate]->count ?? 0,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Users fetched successfully.',
            'data' => $filledData,
        ]);
    }

    public function fetchPostsForChart()
    {
        $posts = Post::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->get()
                    ->keyBy('date');

        $startDate = Post::orderBy('created_at', 'asc')->value('created_at');
        $endDate = Post::orderBy('created_at', 'desc')->value('created_at');

        if (!$startDate || !$endDate) {
            return response()->json([
                'status' => false,
                'message' => 'No Post Data Found.',
            ]);
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($start, $interval, $end->modify('+1 day'));

        $filledData = [];
        foreach ($dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $filledData[] = [
                'date' => $formattedDate,
                'count' => $posts[$formattedDate]->count ?? 0,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Posts fetched successfully.',
            'data' => $filledData,
        ]);
    }

    public function fetchReelsForChart()
    {
        $reels = Reel::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->get()
                    ->keyBy('date');

        $startDate = Reel::orderBy('created_at', 'asc')->value('created_at');
        $endDate = Reel::orderBy('created_at', 'desc')->value('created_at');

        if (!$startDate || !$endDate) {
            return response()->json([
                'status' => false,
                'message' => 'No Reel Data Found.',
            ]);
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($start, $interval, $end->modify('+1 day'));

        $filledData = [];
        foreach ($dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $filledData[] = [
                'date' => $formattedDate,
                'count' => $reels[$formattedDate]->count ?? 0,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Reels fetched successfully.',
            'data' => $filledData,
        ]);
    }

    public function fetchRoomsForChart()
    {
        $rooms = Room::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                    ->groupBy('date')
                    ->get()
                    ->keyBy('date');

        $startDate = Room::orderBy('created_at', 'asc')->value('created_at');
        $endDate = Room::orderBy('created_at', 'desc')->value('created_at');

        if (!$startDate || !$endDate) {
            return response()->json([
                'status' => false,
                'message' => 'No Room Found.',
            ]);
        }

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($start, $interval, $end->modify('+1 day'));

        $filledData = [];
        foreach ($dateRange as $date) {
            $formattedDate = $date->format('Y-m-d');
            $filledData[] = [
                'date' => $formattedDate,
                'count' => $rooms[$formattedDate]->count ?? 0,
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Rooms fetched successfully.',
            'data' => $filledData,
        ]);
    }

    public function settingView()
    {
        $setting = Setting::get()->first();
        $awsConfig = [
            'AWS_ACCESS_KEY_ID' => env('AWS_ACCESS_KEY_ID'),
            'AWS_SECRET_ACCESS_KEY' => env('AWS_SECRET_ACCESS_KEY'),
            'AWS_DEFAULT_REGION' => env('AWS_DEFAULT_REGION'),
            'AWS_BUCKET' => env('AWS_BUCKET')
        ];
        $doConfig = [
            'DO_SPACE_ACCESS_KEY_ID' => env('DO_SPACE_ACCESS_KEY_ID'),
            'DO_SPACE_SECRET_ACCESS_KEY' => env('DO_SPACE_SECRET_ACCESS_KEY'),
            'DO_SPACE_REGION' => env('DO_SPACE_REGION'),
            'DO_SPACE_BUCKET' => env('DO_SPACE_BUCKET')
        ];

        return view('setting', compact('setting', 'awsConfig', 'doConfig'));
    }

    public function saveStorageSetting(Request $request)
    {
        $setting = Setting::first();

        if (!$setting) {
            return response()->json([
                'status' => false,
                'message' => 'setting Not Found',
            ]);
        }
          
        $setting->storage_type = $request->storage_type;
        $setting->save();

        return response()->json([
            'status' => true,
            'message' => 'Setting Updated Successfully',
        ]);
    }

    function index()
    {
        $user = User::count();
        $interest = Interest::count();
        $report = Report::count();
        $verificationRequests = ProfileVerification::count();
        $adminNotification = AdminNotification::count();
        $posts = Post::count();
        $rooms = Room::count();
        $faqs = FAQs::count();
        $reels = Reel::count();
        $music = Music::count();

        return view('index', [
            'user' => $user,
            'interest' => $interest,
            'report' => $report,
            'verificationRequests' => $verificationRequests,
            'adminNotification' => $adminNotification,
            'posts' => $posts,
            'rooms' => $rooms,
            'faqs' => $faqs,
            'reels' => $reels,
            'music' => $music,
        ]);
    }

    public function fetchSetting()
    {
        $data = Setting::first();
        $interests = Interest::get();
        $documentType = DocumentType::get();
        $reportReasons = ReportReason::get();
        $restrictedUsernames = UsernameRestriction::get();

        
        foreach ($interests as $fetchInterest) {

            $roomsCount = Room::whereRelation('user', 'is_block', 0)
                    ->whereRaw('find_in_set("' . $fetchInterest->id . '", interest_ids)')
                    ->where('is_private', 0)
                    ->where('total_member', '<>', (int) $data->setRoomUsersLimit)
                    ->count();
           
            // $interestRoomsCount = Room::whereIn('interest_ids', $fetchInterest->id)->count();
            $fetchInterest->totalRoomOfInterest = $roomsCount;
        }

        $data->interests = $interests;
        $data->documentType = $documentType;
        $data->reportReasons = $reportReasons;
        $data->restrictedUsernames = $restrictedUsernames;

        return response()->json([
            'status' => true,
            'message' => 'Fetch Setting',
            'data' => $data,
        ]);
    }

    public function documentTypeList(Request $request)
    {
        $totalData = DocumentType::count();
        $rows = DocumentType::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'title',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = DocumentType::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = DocumentType::Where('title', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = DocumentType::Where('title', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {
            $edit = '<a href="#" data-title="' . $item->title . '" class="me-3 btn btn-success px-4 text-white edit" rel=' . $item->id . ' >' . __('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete" rel=' . $item->id . ' >' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>') . '</a>';
            $action = '<span class="float-right">' . $edit . $delete . ' </span>';

            $data[] = [$item->title, $action];
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

    public function addDocumentType(Request $request)
    {
        $documentTypeList = DocumentType::where('title', $request->title)
            ->get()
            ->first();

        if ($documentTypeList != null) {
            return response()->json([
                'status' => false,
                'message' => 'Document Record Dublicate',
            ]);
        } else {
            $documentType = new DocumentType();
            $documentType->title = $request->title;
            $documentType->save();

            return response()->json([
                'status' => true,
                'message' => 'Document Added Successfully',
                'data' => $documentType,
            ]);
        }
    }

    public function updateDocumentType(Request $request, $id) 
    {
        $documentType = DocumentType::where('title', $request->title)
            ->get()
            ->first();

        if ($documentType != null) {
            return response()->json([
                'status' => false,
                'message' => 'Document Record Dublicate',
            ]);
        } else {
            $documentType = DocumentType::find($id);
            if ($documentType) {
                $documentType->title = $request->title;
                $documentType->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Document Type Updated Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Document Type Not Found',
                ]);
            }
        }
    }

    public function deleteDocumentType($id)
    {
        $documentType = DocumentType::find($id);

        if ($documentType) {
            $documentType->delete();
            return response()->json([
                'status' => true,
                'message' => 'Document Type Delete Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Document Type Not Found',
            ]);
        }
    }

    // Add Report Reason
    public function reportReasonList(Request $request)
    {
        $totalData = ReportReason::count();
        $rows = ReportReason::orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'title',
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;
        if (empty($request->input('search.value'))) {
            $result = ReportReason::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $result = ReportReason::Where('title', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = ReportReason::Where('title', 'LIKE', "%{$search}%")->count();
        }
        $data = [];
        foreach ($result as $item) {
            $edit = '<a href="#" data-title="' . $item->title . '" class="me-3 btn btn-success px-4 text-white edit" rel=' . $item->id . ' >' . __('<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete" rel=' . $item->id . ' >' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>') . '</a>';
            $action = '<span class="float-right">' . $edit . $delete . ' </span>';

            $data[] = [$item->title, $action];
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

    public function addreportReason(Request $request)
    {
        $reportReason = ReportReason::where('title', $request->title)
            ->get()
            ->first();

        if ($reportReason != null) {
            return response()->json([
                'status' => false,
                'message' => 'Report Reason Dublicate',
            ]);
        } else {
            $reportReason = new ReportReason();
            $reportReason->title = $request->title;
            $reportReason->save();

            return response()->json([
                'status' => true,
                'message' => 'Reason Added Successfully',
                'data' => $reportReason,
            ]);
        }
    }

    public function updateReportReason(Request $request, $id) 
    {
        $reportReason = ReportReason::where('title', $request->title)->get()->first();

        if ($reportReason != null) {
            return response()->json([
                'status' => false,
                'message' => 'Reason Already Exist',
            ]);
        } else {
            $reportReason = ReportReason::find($id);
            if ($reportReason) {
                $reportReason->title = $request->title;
                $reportReason->save();

                return response()->json([
                    'status' => true,
                    'message' => 'Report Reason Updated Successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => ' Reason Not Found',
                ]);
            }
        }
    }

    public function deleteReportReasonType(Request $request)
    {
        $reportReason = ReportReason::where('id', $request->reportReason_id)->first();
        if ($reportReason) {
            $reportReason->delete();
            return response()->json([
                'status' => true,
                'message' => 'Report Reason Delete Successfully',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Report Reason not found',
        ]);
    }

    public function updateSettings(Request $request)
    {
        $setting = Setting::first();

        if ($setting) {
            if ($request->has('app_name')) {
                $setting->app_name = $request->app_name;
                $request->session()->put('app_name', $setting['app_name']);
            }
            if ($request->has('setRoomUsersLimit')) {
                $setting->setRoomUsersLimit = $request->setRoomUsersLimit;
            }
            if ($request->has('support_email')) {
                $setting->support_email = $request->support_email;
            }
            if ($request->has('storage_type')) {
                $setting->storage_type = $request->storage_type;
            }
            if ($request->has('minute_limit_in_creating_story')) {
                $setting->minute_limit_in_creating_story = $request->minute_limit_in_creating_story;
            }
            if ($request->has('minute_limit_in_audio_post')) {
                $setting->minute_limit_in_audio_post = $request->minute_limit_in_audio_post;
            }
            if ($request->has('minute_limit_in_choosing_video_for_story')) {
                $setting->minute_limit_in_choosing_video_for_story = $request->minute_limit_in_choosing_video_for_story;
            }
            if ($request->has('minute_limit_in_choosing_video_for_post')) {
                $setting->minute_limit_in_choosing_video_for_post = $request->minute_limit_in_choosing_video_for_post;
            }
            if ($request->has('max_images_can_be_uploaded_in_one_post')) {
                $setting->max_images_can_be_uploaded_in_one_post = $request->max_images_can_be_uploaded_in_one_post;
            }
            if ($request->has('ad_banner_android')) {
                $setting->ad_banner_android = $request->ad_banner_android;
            }
            if ($request->has('ad_interstitial_android')) {
                $setting->ad_interstitial_android = $request->ad_interstitial_android;
            }
            if ($request->has('ad_banner_iOS')) {
                $setting->ad_banner_iOS = $request->ad_banner_iOS;
            }
            if ($request->has('ad_interstitial_iOS')) {
                $setting->ad_interstitial_iOS = $request->ad_interstitial_iOS;
            }
            if ($request->has('is_admob_on')) {
                $setting->is_admob_on = $request->is_admob_on;
            }
            if ($request->has('audio_space_hosts_limit')) {
                $setting->audio_space_hosts_limit = $request->audio_space_hosts_limit;
            }
            if ($request->has('audio_space_listeners_limit')) {
                $setting->audio_space_listeners_limit = $request->audio_space_listeners_limit;
            }
            if ($request->has('audio_space_duration_in_minutes')) {
                $setting->audio_space_duration_in_minutes = $request->audio_space_duration_in_minutes;
            }
            if ($request->has('is_sight_engine_enabled')) {
                $setting->is_sight_engine_enabled = $request->is_sight_engine_enabled;
            }
            if ($request->has('sight_engine_api_user')) {
                $setting->sight_engine_api_user = $request->sight_engine_api_user;
            }
            if ($request->has('sight_engine_api_secret')) {
                $setting->sight_engine_api_secret = $request->sight_engine_api_secret;
            }
            if ($request->has('sight_engine_image_workflow_id')) {
                $setting->sight_engine_image_workflow_id = $request->sight_engine_image_workflow_id;
            }
            if ($request->has('sight_engine_video_workflow_id')) {
                $setting->sight_engine_video_workflow_id = $request->sight_engine_video_workflow_id;
            }
            if ($request->has('fetch_post_type')) {
                $setting->fetch_post_type = $request->fetch_post_type;
            }
            if ($request->has('is_in_app_purchase_enabled')) {
                $setting->is_in_app_purchase_enabled = $request->is_in_app_purchase_enabled;
            }
            if ($request->has('duration_limit_in_reel')) {
                $setting->duration_limit_in_reel = $request->duration_limit_in_reel;
            }
            if ($request->has('favicon')) {
                $file = $request->file('favicon');
                GlobalFunction::saveFileInLocal($file, 'favicon.png');
            }
            $setting->save();

            return response()->json([
                'status' => true,
                'message' => 'Setting Updated Successfully',
            ]);
        }    
    }
    
    function admob()
    {
        $setting = Setting::first();
        return view('admob', [
            'setting' => $setting,
        ]);    
    }

    public function test(Request $request)
    {
        $roomUser = RoomUser::where('room_id', $request->room_id)->count();
        return response()->json([
            'status' => true,
            'message' => 'test',
            'data' => $roomUser,

        ]);
    }

    public function pushNotificationToSingleUser(Request $request)
    {
        $client = new Client();
        $client->setAuthConfig('googleCredentials.json');
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->fetchAccessTokenWithAssertion();
        $accessToken = $client->getAccessToken()['access_token'];

        $contents = File::get(base_path('googleCredentials.json'));
        $json = json_decode($contents, true);

        $url = 'https://fcm.googleapis.com/v1/projects/' . $json['project_id'] . '/messages:send';

        $fields = $request->json()->all();

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $fields);

        if ($response->failed()) {
            return response()->json([
                'status' => false,
                'message' => 'FCM Send Error',
                'error' => $response->body()
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Push Notification Sent Successfully',
            'result' => $response->json(),
            'fields' => $fields,
        ]);
    }

    function generateAgoraToken(Request $request)
    {
        $rules = [
            'channelName' => 'required'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $appID = env('AGORA_APP_ID');
        $appCertificate = env('AGORA_APP_CERT');
        $channelName = $request->channelName;
        $role = RtcTokenBuilder::RolePublisher;
        $expireTimeInSeconds = 7200;
        $currentTimestamp = now()->getTimestamp();
        $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;
        $token = RtcTokenBuilder::buildTokenWithUid($appID, $appCertificate, $channelName, 0, $role, $privilegeExpiredTs);

        return response()->json([
            'status' => true,
            'message' => 'generated successfully',
            'token' => $token
        ]);
    }

    public function addFakeData(Request $request)
    {
        $fetchInterests = Interest::pluck('id')->toArray();
        if (count($fetchInterests) > 0) {
            $faker = Faker::create();
            $numberOfUsers = $request->input('number_of_users', 1); 
            $fetchUsers = User::pluck('id')->toArray();

            for ($i = 0; $i < $numberOfUsers; $i++) {

                $profileImageUrl = $this->getRandomImage('https://randomuser.me/api/', 'picture', 'large');
                $backgroundImageUrl = $this->getRandomImage('https://random.imagecdn.app/800/500', '', '');
                $bio = $faker->sentence($nbWords = 10);
                $randomInterestIds = array_rand(array_flip($fetchInterests), rand(2, 4));
                $interest_ids = implode(',', (array)$randomInterestIds);
                $login_type = rand(0, 2);
                $device_type = rand(0, 1);
                $deviceToken = str()->random(20);

                $user = new User();
                $user->identity = $faker->unique()->safeEmail;
                $user->username = $faker->userName;
                $user->full_name = $faker->name;
                $user->login_type = (int) $login_type;
                $user->device_type = (int) $device_type;
                $user->device_token = $deviceToken;
                $user->profile = $profileImageUrl;
                $user->background_image = $backgroundImageUrl;
                $user->bio = $bio;
                $user->interest_ids = $interest_ids;
                $user->save();

                // Adding two posts for each user
                for ($j = 0; $j < 1; $j++) {
                    $post = new Post();
                    $post->user_id = $user->id;
                    $post->desc = $faker->sentence($nbWords = 20);
                    $post->tags = implode(',', $faker->words($nb = 3));
                    $post->link_preview_json = null; // or generate random JSON if needed
                    $randomInterestIds = array_rand(array_flip($fetchInterests), rand(2, 4));
                    $interest_ids = implode(',', (array)$randomInterestIds);
                    $post->interest_ids = $interest_ids;
                    $post->save();

                    // Adding post content
                    $count = rand(1, 3);
                    // $count = 1;

                    for ($k = 0; $k < $count; $k++) {
                        $postContent = new PostContent();
                        $postContent->post_id = $post->id;

                        // Determine content type
                        $contentType = rand(0, 3); // 0: image, 1: video, 2: audio, 3: text
                        // $contentType = 1; // 0: image, 1: video, 2: audio, 3: text
                        $postContent->content_type = $contentType;

                        switch ($contentType) {
                            case 0: // Image
                                $postContent->content = $this->getRandomImage('https://random.imagecdn.app/800/500', '', '');
                                $postContent->thumbnail = null;
                                $postContent->save();
                                break;
                            case 1: // Video
                                $videoData = [
                                    [
                                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-1.mp4',
                                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_1.png'
                                    ],
                                    [
                                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-2.mp4',
                                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_2.png'    
                                    ],
                                    [
                                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-3.mp4',
                                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_3.png'    
                                    ],
                                    [
                                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-4.mp4',
                                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_4.png'    
                                    ],
                                    [
                                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-5.mp4',
                                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_5.png'    
                                    ]
                                ];

                                $selectedVideo = $videoData[array_rand($videoData)];

                                $postContent->content = $selectedVideo['content'];
                                $postContent->thumbnail = $selectedVideo['thumbnail'];
                                $postContent->save();
                                break;

                            case 2: // Audio
                                $audioData = [
                                    [
                                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/audio/Audio-1.mp3',
                                        'audio_waves' => '[0.08184748888015747, 0.10807078331708908, 0.09583979845046997, 0.09155284613370895, 0.12370922416448593, 0.1499776542186737, 0.11449092626571655, 0.14323653280735016, 0.2251466065645218, 0.27705255150794983, 0.2549576759338379, 0.25315412878990173, 0.296523779630661, 0.3034026622772217, 0.30557388067245483, 0.26061493158340454, 0.29513025283813477, 0.3131752014160156, 0.23220890760421753, 0.2917553782463074, 0.28808358311653137, 0.27017998695373535, 0.2678169012069702, 0.29625755548477173, 0.30878347158432007, 0.28896617889404297, 0.30072206258773804, 0.30692988634109497, 0.25112205743789673, 0.3098297119140625, 0.31313836574554443, 0.2418593168258667, 0.26257383823394775, 0.3265695869922638, 0.2824242413043976, 0.33044496178627014, 0.3135311007499695, 0.31068336963653564, 0.25024789571762085, 0.2798200249671936, 0.3045760989189148, 0.2370680570602417, 0.2734418213367462, 0.2880588173866272, 0.23500216007232666, 0.28380095958709717, 0.30158787965774536, 0.2622164189815521, 0.2856462001800537, 0.28011929988861084, 0.2917855978012085, 0.2755967080593109, 0.30014896392822266, 0.31128865480422974, 0.2829914689064026, 0.29973548650741577, 0.24869978427886963, 0.13031022250652313, 0.253538578748703, 0.28159192204475403, 0.22461989521980286, 0.28523164987564087, 0.2690601050853729, 0.21094228327274323, 0.25546979904174805, 0.269223690032959, 0.19638323783874512, 0.2221246361732483, 0.2677556276321411, 0.25151002407073975, 0.2370360791683197, 0.27337178587913513, 0.2698308229446411, 0.23240309953689575, 0.2631607949733734, 0.28692832589149475, 0.17039775848388672, 0.2401604950428009, 0.2442096471786499, 0.2359628677368164, 0.19544997811317444, 0.2606539726257324, 0.26545119285583496, 0.19709855318069458, 0.2640990912914276, 0.2943023443222046, 0.2053651362657547, 0.20032820105552673, 0.19616547226905823, 0.20774847269058228, 0.2080516517162323, 0.16359351575374603, 0.08977597951889038, 0.0175998043268919, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0]'
                                    ],
                                    [
                                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/audio/Audio-2.mp3',
                                        'audio_waves' => '[0.011457201093435287, 0.03828534483909607, 0.0912167876958847, 0.20904481410980225, 0.2901274263858795, 0.40689602494239807, 0.4275849163532257, 0.34412479400634766, 0.3129078149795532, 0.24853718280792236, 0.17725498974323273, 0.10684053599834442, 0.2347744107246399, 0.20815595984458923, 0.26940038800239563, 0.3277045488357544, 0.3306405544281006, 0.4156420826911926, 0.2973843812942505, 0.31789863109588623, 0.29078853130340576, 0.28079819679260254, 0.23833110928535461, 0.21931220591068268, 0.18453794717788696, 0.34265458583831787, 0.40759962797164917, 0.39737409353256226, 0.5622303485870361, 0.42237961292266846, 0.39086371660232544, 0.374802827835083, 0.39777201414108276, 0.35579586029052734, 0.3151523768901825, 0.43419212102890015, 0.4538576304912567, 0.429801344871521, 0.38329261541366577, 0.5200920701026917, 0.44027453660964966, 0.2909179627895355, 0.4928620457649231, 0.41936105489730835, 0.4704045057296753, 0.3030032515525818, 0.3312544822692871, 0.35272830724716187, 0.4464510679244995, 0.48214203119277954, 0.5188660621643066, 0.5268720388412476, 0.48153334856033325, 0.5394608974456787, 0.47737330198287964, 0.4466075897216797, 0.3596869111061096, 0.35789141058921814, 0.3958171606063843, 0.4528438448905945, 0.44896459579467773, 0.4698582589626312, 0.49054837226867676, 0.44359803199768066, 0.4654596447944641, 0.46551620960235596, 0.4824334979057312, 0.30006346106529236, 0.09456278383731842, 0.27880382537841797, 0.3183293342590332, 0.4928697347640991, 0.14963319897651672, 0.32148855924606323, 0.13151980936527252, 0.05719594284892082, 0.037199653685092926, 0.026388026773929596, 0.03293128311634064, 0.04808985814452171, 0.07444903254508972, 0.18563556671142578, 0.13391777873039246, 0.15258745849132538, 0.13661664724349976, 0.04699770361185074, 0.003448240924626589, 0.000006461368229793152, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0, 0.0]'
                                    ],
                                    [
                                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/audio/Audio-3.mp3',
                                        'audio_waves' => '[0.0000016311403214785969,0.00003996141458628699,0.00043688237201422453,0.0009435145766474307,0.0015353778144344687,0.001818690332584083,0.0017460280796512961,0.0017602031584829092,0.0015133607666939497,0.0017283152556046844,0.0018532442627474666,0.0015001024585217237,0.0011840316001325846,0.00112135149538517,0.0013951268047094345,0.0013570912415161729,0.0013611745089292526,0.0013401631731539965,0.0010522411903366446,0.0012152562849223614,0.0012430408969521523,0.0013263300061225891,0.001360311172902584,0.001282856916077435,0.0012563628843054175,0.0015413932269439101,0.0016746814362704754,0.00157244224101305,0.0013282009167596698,0.0013157310895621777,0.0013945671962574124,0.0014795196475461125,0.0011262445477768779,0.0011281492188572884,0.001131462980993092,0.004355320241302252,0.0013752687955275178,0.0013958088820800185,0.0012838600669056177,0.0010619423119351268,0.001318461843766272,0.0013956903712823987,0.0012609193800017238,0.0013195032952353358,0.001428712159395218,0.0053961072117090225,0.008150821551680565,0.007884091697633266,0.010785479098558426,0.01113205961883068,0.012815075926482677,0.014443485997617245,0.016095802187919617,0.016161341220140457,0.014727802015841007,0.011742810718715191,0.008512943051755428,0.007833434268832207,0.00694770272821188,0.005949417594820261,0.0038207746110856533,0.003699113614857197,0.00506107322871685,0.00494314543902874,0.0066454652696847916,0.007217736914753914,0.004124845843762159,0.002919122576713562,0.002629511756822467,0.0025671902112662792,0.0030102792661637068,0.0037297955714166164,0.012052836827933788,0.018505355343222618,0.014306367374956608,0.010919204913079739,0.007636097725480795,0.004817943088710308,0.00453618448227644,0.003244958585128188,0.0024992122780531645,0.002311893505975604,0.002168283797800541,0.004731529857963324,0.0029250644147396088,0.001610150677151978,0.0015656277537345886,0.0016005560755729675,0.0014579303096979856,0.0014668606454506516,0.001656576874665916,0.0017128287581726909,0.0017595274839550257,0.0013989859726279974,0.001194340642541647,0.0015285605331882834,0.0012560703326016665,0.0016363703180104494,0.0019889401737600565,0.0022326705511659384,0.0012712256284430623,0.0009835155215114355]'
                                    ]
                                ];

                                $selectedAudio = $audioData[array_rand($audioData)];
                                $postContent->content = $selectedAudio['content'];
                                $postContent->audio_waves = $selectedAudio['audio_waves'];
                                $postContent->save();
                                break;
                            case 3: // Text
                                $postContent->content = $faker->paragraph($nbSentences = 3);
                                $postContent->save();
                                break;
                        }

                       

                        for ($m = 0; $m < rand(1, 2); $m++) {
                            $postComment = new Comment();

                            // Get a random user ID
                            $randomUserIdKey = array_rand($fetchUsers);
                            $randomUserId = $fetchUsers[$randomUserIdKey];

                            $postComment->user_id = (int) $randomUserId;
                            $postComment->post_id = (int) $post->id;
                            $postComment->desc = $faker->sentence($nbWords = 10);
                            $postComment->save();

                            $post->comments_count += 1;
                            $post->save();
                        }
                    }
                }

                $reelData = [
                    [
                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-6.mp4',
                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_6.png'
                    ],
                    [
                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-7.mp4',
                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_7.png'    
                    ],
                    [
                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-8.mp4',
                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_8.png'    
                    ],
                    [
                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-9.mp4',
                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_9.png'    
                    ],
                    [
                        'content' => 'https://chatter.retrytech.site/public/asset/fake_data/video/Video-10.mp4',
                        'thumbnail' => 'https://chatter.retrytech.site/public/asset/fake_data/thumbnail/videoframe_10.png'    
                    ]
                ];
                
                shuffle($reelData); // Shuffle once before the loop
                
                for ($l = 0; $l < 2; $l++) {
                    if (empty($reelData)) break; // Prevent errors if the array gets exhausted
                
                    $selectedReel = array_pop($reelData); // Pick a unique reel for each iteration
                
                    $randomInterestIds = array_rand(array_flip($fetchInterests), rand(2, 4));
                    $reelInterest_ids = implode(',', (array)$randomInterestIds);
                
                    $reel = Reel::create([
                        'user_id'      => $user->id,
                        'description'  => $faker->sentence(15),
                        'interest_ids' => $reelInterest_ids,
                        'content'      => $selectedReel['content'],
                        'thumbnail'    => $selectedReel['thumbnail'],
                    ]);

                    for ($m = 0; $m < rand(1, 2); $m++) {

                        $randomUserIdKey = array_rand($fetchUsers);
                        $randomUserId = $fetchUsers[$randomUserIdKey];
                        
                        ReelComment::create([
                            'user_id' => (int) $randomUserId,
                            'reel_id' => (int) $reel->id,
                            'description' => $faker->sentence($nbWords = 10),
                        ]);
                
                        $reel->increment('comments_count');
                    }
                }
                

                // new room crete
                $isPrivate = rand(0, 1);
                $is_join_request_enable = rand(0, 1);

                $room = new Room();
                $room->admin_id = (int) $user->id;
                $roomImageUrl = $this->getRandomImage('https://random.imagecdn.app/200/200', '', '');
                $room->photo = $roomImageUrl;
                
                $room->title = $faker->title;
                $room->desc = $faker->sentence($nbWords = 10);
                $room->interest_ids = $interest_ids;
                $room->is_private = (int) $isPrivate;
                $room->is_join_request_enable = (int) $is_join_request_enable;
                $room->total_member += 1;
                $room->save();

                $roomUser = new RoomUser();
                $roomUser->room_id = $room->id;
                $roomUser->user_id = $room->admin_id;
                $roomUser->type = 5;
                $roomUser->save();





            }

            return response()->json([
                'status' => true,
                'message' => "{$numberOfUsers} fake users and their posts and reel added successfully!",
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => "First add Interests.",
        ]);

    }

    private function getRandomImage($apiUrl, $jsonKey, $subKey)
    {
        try {
            $response = Http::get($apiUrl);
            if ($response->successful()) {
                return $jsonKey ? $response->json()['results'][0][$jsonKey][$subKey] : $response->effectiveUri();
            }
        } catch (\Exception $e) {
            return 'default_image_url';
        }
    }

}