<?php

namespace App\Http\Controllers;

use App\Models\Constants;
use App\Models\FollowingList;
use App\Models\GlobalFunction;
use App\Models\Interest;
use App\Models\Like;
use App\Models\Music;
use App\Models\MusicCategory;
use App\Models\Reel;
use App\Models\ReelComment;
use App\Models\Report;
use App\Models\SavedNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReelController extends Controller
{ 
    public function fetchMusicWithSearch(Request $request)
    {
        $data = $request->validate([
            'start' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
            'keyword' => 'nullable|string',
        ]);

        $query = Music::where('is_deleted', Constants::DeletedNo);

        if (!empty($data['keyword'])) {
            $query->where(function ($q) use ($data) {
                $q->where('title', 'like', "%{$data['keyword']}%")
                ->orWhere('artist', 'like', "%{$data['keyword']}%");
            });
        }

        $musics = $query->latest('id')
                        ->skip($data['start'])
                        ->take($data['limit'])
                        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Fetched music successfully.',
            'data' => $musics,
        ]);
    }

    public function fetchMusicCategories()
    {
        $musicCategories = MusicCategory::withCount('musics')
                                        ->where('is_deleted', Constants::DeletedNo)
                                        ->orderByDesc('id')
                                        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Fetch Musics Categories Successfully.',
            'data' => $musicCategories,
        ]);
    }

    public function fetchSavedMusic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::find($request->user_id);

        $savedMusicIds = explode(',', $user->saved_music_ids);

        $musics = Music::whereIn('id', $savedMusicIds)->get();

        return response()->json([
            'status' => true,
            'message' => 'Fetch User Saved Musics Successfully.',
            'data' => $musics,
        ]);
    }

    public function fetchMusicByCategory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
            'category_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $music = Music::where('is_deleted', Constants::DeletedNo)
                    ->where('category_id', $request->category_id)
                    ->orderByDesc('id')
                    ->offset($request->start)
                    ->limit($request->limit)
                    ->get();

        return response()->json([
            'status' => true,
            'message' => 'Fetch Musics By Categories Successfully.',
            'data' => $music,
        ]);
    }

    public function fetchReelsByMusic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
            'music_id' => 'required|integer|exists:musics,id',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $musicReels = Reel::where('music_id', $request->music_id)
                            ->orderByDesc('id')
                            ->offset($request->start)
                            ->limit($request->limit)
                            ->get();
        
        $reels = Reel::processReels($musicReels, $request->my_user_id);

        return response()->json([
            'status' => true,
            'message' => 'Fetch Reels By Music Successfully.',
            'data' => $reels,
        ]);
    }
    
    public function uploadReel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
            'interest_ids'  => 'nullable|string',
            'content'   => 'required',
            'thumbnail' => 'required',
            'music_id' => 'nullable|exists:musics,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found'
            ]);
        }

        $reel = Reel::create([
            'user_id'      => $request->user_id,
            'description'  => $request->description,
            'interest_ids' => $request->interest_ids,
            'hashtags'     => $request->hashtags,
            'content'      => GlobalFunction::saveFileAndGivePath($request->content),
            'thumbnail'    => GlobalFunction::saveFileAndGivePath($request->thumbnail),
            'music_id'     => $request->music_id,
        ]);

        $reel = $reel->fresh();

        return response()->json([
            'status' => true,
            'message' => 'Reel Added Successfully.',
            'data' => $reel,
        ]);
    }

    public function likeDislikeReel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'reel_id' => 'required|exists:reels,id'
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)->where('is_block', Constants::unblocked)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found.'
            ]);
        }

        $reel = Reel::with('user')->find($request->reel_id);
        if (!$reel) {
            return response()->json([
                'status' => false,
                'message' => 'Reel Not Found.'
            ]);
        }

        $likeRecord = Like::where('user_id', $request->user_id)->where('reel_id', $request->reel_id)->first();
        $type = Constants::notificationTypeReelLike;

        if ($likeRecord) {

            $reel->likes_count = max(0, $reel->likes_count - 1);
            $reel->save();

            SavedNotification::where('user_id', $request->user_id)
                                ->where('reel_id', $request->reel_id)
                                ->where('type', $type)
                                ->delete();

            $likeRecord->delete();

            return response()->json([
                'status' => true,
                'message' => 'Reel Dislike Successfully.',
                'data' => $reel,
            ]);
        } else {

            Like::create([
                'user_id' => $request->user_id,
                'reel_id' => $request->reel_id,
            ]);

            $reel->likes_count += 1;
            $reel->save();

            $notificationDesc = $user->full_name . ' has liked your Reel.';
            if ($reel->user->id != $user->id && $reel->user->is_push_notifications == Constants::pushNotification) {
                GlobalFunction::sendPushNotificationToUser($notificationDesc, $reel->user->device_token, $reel->user->device_type);
            }

            if ($user->id != $reel->user->id) {
                $savedNotification = new SavedNotification();
                $savedNotification->my_user_id = $reel->user->id;
                $savedNotification->user_id = $request->user_id;
                $savedNotification->reel_id = $request->reel_id;
                $savedNotification->type = $type;
                $savedNotification->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Reel Liked.',
                'data' => $reel,
            ]);
        }
    }

    public function increaseReelViewCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reel_id' => 'required|numeric|exists:reels,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $reel = Reel::find($request->reel_id);

        $reel->increment('views_count');

        return response()->json([
            'status' => true,
            'message' => 'Reel view count increased successfully.',
            'data' => [
                'reel_id' => $reel->id,
                'views_count' => $reel->views_count
            ],
        ]);
    }

    public function addReelComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'reel_id' => 'required|integer|exists:reels,id',
            'description' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('id', $request->user_id)
            ->where('is_block', Constants::unblocked)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found or Blocked.',
            ]);
        }

        $reel = Reel::find($request->reel_id);

        $reelComment = ReelComment::create([
            'user_id' => $user->id,
            'reel_id' => $reel->id,
            'description' => $request->description,
        ]);

        $reel->increment('comments_count');

        if ($reel->user_id !== $user->id) {
            $reelOwner = $reel->user;
            if ($reelOwner->is_push_notifications == Constants::pushNotification) {
                $notificationMessage = "{$user->full_name} has commented on your Reel: {$request->description}";
                GlobalFunction::sendPushNotificationToUser($notificationMessage, $reelOwner->device_token, $reelOwner->device_type);
            }

            SavedNotification::create([
                'my_user_id' => $user->id,
                'user_id' => $reelOwner->id,
                'reel_comment_id' => $reelComment->id,
                'type' => Constants::notificationTypeAddReelComment,
            ]);
        }

        $reelComment->user = $user;

        return response()->json([
            'status' => true,
            'message' => 'Reel Comment Added Successfully.',
            'data' => $reelComment,
        ]);
    }

    public function fetchReelComments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'reel_id' => 'required|integer|exists:reels,id',
            'start' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::find($request->user_id);

        $blockUserIds = array_filter(explode(',', $user->block_user_ids ?? ''));

        $reelComments = ReelComment::where('reel_id', $request->reel_id)
            ->whereNotIn('user_id', $blockUserIds)
            ->with([
                'user' => function ($query) {
                    $query->where('is_block', Constants::unblocked);
                }
            ])
            ->orderByDesc('id')
            ->offset($request->start)
            ->limit($request->limit)
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Reel Comment Fetch Successfully.',
            'data' => $reelComments,
        ]);
    }

    public function deleteReelComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required|integer|exists:users,id',
            'comment_id' => 'required|integer|exists:reel_comments,id',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $reelComment = ReelComment::where('id', $request->comment_id)
            ->where('user_id', $request->my_user_id)
            ->first();

        if (!$reelComment) {
            return response()->json([
                'status' => false,
                'message' => 'Reel Comment not found or you do not have permission to delete this comment.',
            ]);
        }

        $reel = Reel::findOrFail($reelComment->reel_id);
        $reel->comments_count = max(0, $reel->comments_count - 1);
        $reel->save();

        SavedNotification::where('my_user_id', $request->my_user_id)
            ->where('reel_comment_id', $request->comment_id)
            ->where('type', Constants::notificationTypeAddReelComment)
            ->delete();

        $reelComment->delete();

        return response()->json([
            'status' => true,
            'message' => 'Reel Comment Delete Successfully.',
            'data' => $reelComment,
        ]);
    }

    public function fetchReelsByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required|exists:users,id',
            'user_id' => 'required|exists:users,id',
            'start' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false, 
                'message' => $validator->errors()->first(),
            ]);
        }
    
        $user = User::find($request->user_id);
        $myUser = User::find($request->my_user_id);
    
        if (!$user || !$myUser) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found.',
            ]);
        }
    
        $blockUserIds = explode(',', $user->block_user_ids);
        $blockMyUserIds = explode(',', $myUser->block_user_ids);
    
        if (in_array($request->my_user_id, $blockUserIds) || in_array($request->user_id, $blockMyUserIds)) {
            return response()->json([
                'status' => false,
                'message' => 'User Blocked.',
            ]);
        }
    
        $reelsQuery = Reel::where('user_id', $request->user_id)
                          ->whereNotIn('user_id', $blockUserIds)
                          ->whereRelation('user', 'is_block', Constants::unblocked)
                          ->with('user')
                          ->orderByDesc('id');
    
        $reels = $reelsQuery->offset($request->start)
                             ->limit($request->limit)
                             ->get();
    
        $reels = Reel::processReels($reels, $request->my_user_id);
    
        return response()->json([
            'status' => true,
            'message' => 'Fetch Reels Successfully On Profile.',
            'data' => $reels,
        ]);
    }
    
    public function fetchReelsOnExplore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required|exists:users,id',
            'type' => 'required|numeric',
            'limit' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::find($request->my_user_id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User Not Found.']);
        }

        $blockUserIds = explode(',', $user->block_user_ids);

        $reelsQuery = Reel::with('user')
            ->whereNotIn('user_id', $blockUserIds)
            ->whereRelation('user', 'is_block', Constants::unblocked);

        if ($request->type == Constants::forYouReel) {
            $reelsQuery = $reelsQuery->inRandomOrder();
        } elseif ($request->type == Constants::followingReel) {
            $followingUserIds = FollowingList::where('my_user_id', $request->my_user_id)
                ->pluck('user_id')
                ->toArray();
            $reelsQuery = $reelsQuery->whereIn('user_id', $followingUserIds)->skip($request->start)->take($request->limit);
        } else {
            return response()->json(['status' => false, 'message' => 'Invalid "type" provided.']);
        }

        $reels = $reelsQuery->take($request->limit)->get();
        $reels = Reel::processReels($reels, $request->my_user_id);

        if ($reels->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'Reels Not Found.']);
        }

        return response()->json([
            'status' => true,
            'message' => 'Fetch Reels Successfully On Explore.',
            'data' => $reels,
        ]);
    }

    public function deleteReel(Request $request)
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

        $reel = Reel::where('id', $request->reel_id)->where('user_id', $request->user_id)->first();
        if (!$reel) {
            return response()->json([
                'status' => false,
                'message' => 'Reel Not Found.'
            ]);
        }

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
            'message' => 'Reel Deleted Successfully.',
            'data' => $reel,
        ]);
    }

    public function searchReelsByInterestId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'interest_id' => 'required',
            'user_id' => 'required|exists:users,id',
            'start' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
if($request->interest_id != null) {
        $fetchInterest = Interest::find($request->interest_id);
        if (!$fetchInterest) {
            return response()->json([
                'status' => false,
                'message' => 'Interest Not Found.',
            ]);
        }
    }

        $user = User::where('is_block', Constants::unblocked)
                    ->where('id', $request->user_id)
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found.',
            ]);
        }

        $blockUserIds = explode(',', $user->block_user_ids);

        $reelsQuery = Reel::whereRelation('user', 'is_block', Constants::unblocked)
                        ->whereNotIn('user_id', $blockUserIds)
                        ->orderByDesc('id');

                        if($request->interest_id != null) {
                            $reelsQuery->whereRaw('find_in_set(?, interest_ids)', [$request->interest_id]);
                        }

        if (!empty($request->keyword)) {
            $reelsQuery->where('description', 'like', '%' . $request->keyword . '%');
        }

        $reels = $reelsQuery->offset($request->start)
                            ->limit($request->limit)
                            ->get();

        $reels = Reel::processReels($reels, $request->user_id);

        return response()->json([
            'status' => true,
            'message' => 'Search Reels By Interest.',
            'data' => $reels,
        ]);
    }

    public function fetchReelsByHashtag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'tag' => 'required|string',
            'start' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }
    
        $user = User::where('is_block', Constants::unblocked)
                    ->where('id', $request->user_id)
                    ->first();
    
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ]);
        }
    
        $blockUserIds = explode(',', $user->block_user_ids);
    
        $reelsQuery = Reel::whereRelation('user', 'is_block', Constants::unblocked)
                          ->whereNotIn('user_id', $blockUserIds)
                          ->whereRaw('find_in_set(?, hashtags)', [$request->tag])
                          ->orderByDesc('id');
    
        $reels = $reelsQuery->offset($request->start)
                             ->limit($request->limit)
                             ->get();
    
        $reels = Reel::processReels($reels, $request->user_id);
    
        return response()->json([
            'status' => true,
            'message' => 'Fetched reels by hashtag successfully.',
            'data' => $reels,
        ]);
    }
    
    public function reportReel(Request $request)
    {
        {
            $validator = Validator::make($request->all(), [
                'reel_id' => 'required',
                'reason' => 'required',
                'desc' => 'required',
            ]);
    
            if ($validator->fails()) {
                $messages = $validator->errors()->all();
                $msg = $messages[0];
                return response()->json(['status' => false, 'message' => $msg]);
            }
            $reel = Reel::where('id', $request->reel_id)->first();
    
            if (!$reel) {
                return response()->json([
                    'status' => false,
                    'message' => 'Reel Not Found',
                ]);
            }

            $reportType = 3;

            $report = new Report();
            $report->type = $reportType;
            $report->reel_id = (int) $request->reel_id;
            $report->reason = $request->reason;
            $report->desc = $request->desc;
            $report->save();

            return response()->json([
                'status' => true,
                'message' => 'Report Added Successfully.',
                'data' => $report,
            ]);
        }
    }

    public function fetchSavedReels(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'start' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::find($request->user_id);

        $savedReelIds = explode(',', $user->saved_reel_ids);

        $blockUserIds = explode(',', $user->block_user_ids);
      
        $reelsQuery = Reel::whereIn('id', $savedReelIds)
                          ->whereNotIn('user_id', $blockUserIds)
                          ->whereRelation('user', 'is_block', Constants::unblocked)
                          ->with('user')
                          ->orderByDesc('id');
    
        $reels = $reelsQuery->offset($request->start)
                             ->limit($request->limit)
                             ->get();
    
        $reels = Reel::processReels($reels, $request->user_id);

        return response()->json([
            'status' => true,
            'message' => 'Fetch Saved Reels Successfully.',
            'data' => $reels,
        ]);
    }

    public function fetchReelById(Request $request) 
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

        $blockUserIds = explode(',', $user->block_user_ids);
      
        $reel = Reel::where('id', $request->reel_id)
                          ->whereNotIn('user_id', $blockUserIds)
                          ->whereRelation('user', 'is_block', Constants::unblocked)
                          ->with('user')
                          ->get();
    
        $reelsById = Reel::processReels($reel, $request->user_id)->first();

        return response()->json([
            'status' => true,
            'message' => 'Fetch Reel By Id Successfully.',
            'data' => $reelsById,
        ]);
    }

    public function viewReels()
    {
        return view('viewReels');
    }

    public function reelList(Request $request)
    {
        $query = Reel::query();
        $totalData = $query->count();

        $columns = 'id';
        $orderDir = 'desc';
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumn = $columns;
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'LIKE', "%{$searchValue}%")
                    ->orWhere('hashtags', 'LIKE', "%{$searchValue}%")
                    ->orWhereHas('user', function ($q) use ($searchValue) {
                        $q->where('full_name', 'LIKE', "%{$searchValue}%");
                    });
            });
        }

        $totalFiltered = $query->count();

        $result = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $fetchInterests = Interest::get();

        $data = $result->map(function ($item) use ($fetchInterests) {

            if ($item->music_id != null) {
                $music = $item->music;
            } else {
                $music = "null";
            }

            $user = '<a href="usersDetail/' . $item->user_id . '">' .  $item->user->full_name . '</a>';
            $thumbnailUrl = $item->thumbnail ? $item->thumbnail : asset('assets/img/placeholder.png');

            $contentUrl = $item->content ? $item->content : asset('assets/img/placeholder.png');

            $profileUrl = $item->user->profile ? $item->user->profile : asset('assets/img/placeholder.png');

            $description = "<div class='itemDescription'>" . $item->description . "</div>";

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

            $thumbnail = "<div class='reelThumbnail viewReelModal cursor-pointer' 
                        rel='{$item->id}'
                        data-content='{$contentUrl}'
                        data-description='{$item->description}'
                        data-user_id='{$item->user_id}'
                        data-user_profile='{$profileUrl}'
                        data-music='{$music}'
                        data-interests='{$interest_titles_string}'
                        data-username='{$item->user->username}'>
                        <img src='{$thumbnailUrl}' alt='table-user' class='object-fit-cover rounded border img-fluid'>
                        <svg viewBox='0 0 24 24' width='24' height='24' stroke='currentColor' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round' class='css-i6dzq1'><circle cx='12' cy='12' r='10'></circle><polygon points='10 8 16 12 10 16 10 8'></polygon></svg>
                    </div>";

            $counts = "<div class='counts-div'>
                            <span class=''>" . __('Likes') . " <b>: {$item->likes_count} </b></span>
                            <br>
                            <span class=''>" . __('Comments') . " <b>: {$item->comments_count} </b></span>
                            <br>
                            <span class=''>" . __('Views') . " <b>: {$item->views_count} </b></span>
                        </div>";

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deleteReelByAdmin d-flex align-items-center" rel="' . $item->id . '" data-tooltip="Delete Reel">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';

            $action = "<span class='d-flex justify-content-start align-items-center'>{$delete}</span>";

            return [
                $thumbnail,
                $description,
                $item->hashtags,
                $counts,
                $user,
                $action
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }

    public function userReelList(Request $request)
    {
        $query = Reel::where('user_id', $request->userId);
        $totalData = $query->count();

        $columns = 'id';
        $orderDir = 'desc';
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumn = $columns;
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('description', 'LIKE', "%{$searchValue}%")
                    ->orWhere('hashtags', 'LIKE', "%{$searchValue}%")
                    ->orWhereHas('user', function ($q) use ($searchValue) {
                        $q->where('full_name', 'LIKE', "%{$searchValue}%");
                    });
            });
        }

        $totalFiltered = $query->count();

        $result = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $fetchInterests = Interest::get();

        $data = $result->map(function ($item) use ($fetchInterests) {

            if ($item->music_id != null) {
                $music = $item->music;
            } else {
                $music = "null";
            }

            $thumbnailUrl = $item->thumbnail ? $item->thumbnail : asset('assets/img/placeholder.png');

            $contentUrl = $item->content ? $item->content : asset('assets/img/placeholder.png');

            $profileUrl = $item->user->profile ? $item->user->profile : asset('assets/img/placeholder.png');

            $description = "<div class='itemDescription'>" . $item->description . "</div>";

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

            $thumbnail = "<div class='reelThumbnail viewReelModal cursor-pointer' 
                        rel='{$item->id}'
                        data-content='{$contentUrl}'
                        data-description='{$item->description}'
                        data-user_id='{$item->user_id}'
                        data-user_profile='{$profileUrl}'
                        data-music='{$music}'
                        data-interests='{$interest_titles_string}'
                        data-username='{$item->user->username}'>
                        <img src='{$thumbnailUrl}' alt='table-user' class='object-fit-cover rounded border img-fluid'>
                        <svg viewBox='0 0 24 24' width='24' height='24' stroke='currentColor' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round' class='css-i6dzq1'><circle cx='12' cy='12' r='10'></circle><polygon points='10 8 16 12 10 16 10 8'></polygon></svg>
                    </div>";

            $counts = "<div class='counts-div'>
                            <span class=''>" . __('Likes') . " <b>: {$item->likes_count} </b></span>
                            <br>
                            <span class=''>" . __('Comments') . " <b>: {$item->comments_count} </b></span>
                            <br>
                            <span class=''>" . __('Views') . " <b>: {$item->views_count} </b></span>
                        </div>";

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deleteReelByAdmin d-flex align-items-center" rel="' . $item->id . '" data-tooltip="Delete Reel">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';

            $action = "<span class='d-flex justify-content-start align-items-center'>{$delete}</span>";

            return [
                $thumbnail,
                $description,
                $item->hashtags,
                $counts,
                $action
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }

    public function reelReportList(Request $request)
    {
        $reportType = 3;

        $query = Report::where('type', $reportType);
        $totalData = $query->count();

        $columns = 'id';
        $orderDir = 'desc';
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumn = $columns;
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('desc', 'LIKE', "%{$searchValue}%")
                    ->orWhere('hashtags', 'LIKE', "%{$searchValue}%")
                    ->orWhereHas('user', function ($q) use ($searchValue) {
                        $q->where('full_name', 'LIKE', "%{$searchValue}%");
                    });
            });
        }

        $totalFiltered = $query->count();

        $result = $query->orderBy($orderColumn, $orderDir)
            ->offset($start)
            ->limit($limit)
            ->get();

        $fetchInterests = Interest::get();

        $data = $result->map(function ($item) use ($fetchInterests) {

            if ($item->reel->music_id != null) {
                $music = $item->music;
            } else {
                $music = "null";
            } 

            $thumbnailUrl = $item->reel->thumbnail ? $item->reel->thumbnail : asset('assets/img/placeholder.png');

            $contentUrl = $item->reel->content ? $item->reel->content : asset('assets/img/placeholder.png');

            $profileUrl = $item->reel->user->profile ? $item->reel->user->profile : asset('assets/img/placeholder.png');

            $interest_ids = explode(',', $item->reel->interest_ids);
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

            $thumbnail = "<div class='reelThumbnail viewReelModal cursor-pointer' 
                        rel='{$item->reel->id}'
                        data-content='{$contentUrl}'
                        data-description='{$item->reel->description}'
                        data-user_id='{$item->reel->user_id}'
                        data-user_profile='{$profileUrl}'
                        data-music='{$music}'
                        data-interests='{$interest_titles_string}'
                        data-username='{$item->reel->user->username}'>
                        <img src='{$thumbnailUrl}' alt='table-user' class='object-fit-cover rounded border img-fluid'>
                        <svg viewBox='0 0 24 24' width='24' height='24' stroke='currentColor' stroke-width='2' fill='none' stroke-linecap='round' stroke-linejoin='round' class='css-i6dzq1'><circle cx='12' cy='12' r='10'></circle><polygon points='10 8 16 12 10 16 10 8'></polygon></svg>
                    </div>"; 

            $rejectReport = '<a href="#" class="me-3 btn btn-orange px-4 text-white rejectReport d-flex align-items-center" rel=' . $item->id . ' data-tooltip="Reject Report" >' . __(' <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> <span class="ms-2"> Reject </span>') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deleteReel d-flex align-items-center" rel=' . $item->reel->id . ' data-tooltip="Delete Reel">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $rejectReport . $delete . ' </span>';

            return [
                $thumbnail,
                $item->reason,
                $item->desc,
                $action
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }
    
    public function deleteReelReport(Request $request)
    {
        $reports = Report::where('id', $request->report_id)->first();
        $deleteReelReports = Report::where('reel_id', $reports->reel_id)->get();

        if (!$deleteReelReports) {
            return response()->json([
                'status' => false,
                'message' => 'Report Not Found',
            ]);
        }
        $deleteReelReports->each->delete();

        return response()->json([
            'status' => true,
            'message' => 'Report Reject Successfully.',
        ]);
    }

    public function deleteReelFromReport(Request $request)
    {
        $reel = Reel::where('id', $request->reel_id)->first();

        if (!$reel) {
            return response()->json([
                'status' => false,
                'message' => 'Reel Not Found',
            ]);
        }

        $comments = $reel->comments ?? collect();

        foreach ($comments as $comment) {
            SavedNotification::where('reel_comment_id', $comment->id)
                ->whereIn('type', [
                    Constants::notificationTypeReelLike,
                    Constants::notificationTypeAddReelComment
                ])
                ->delete();
            $comment->delete();
        }

        Like::where('reel_id', $request->reel_id)->delete();

        Report::where('reel_id', $request->reel_id)->delete();

        GlobalFunction::deleteFile($reel->thumbnail);
        GlobalFunction::deleteFile($reel->content);

        SavedNotification::where('reel_id', $request->reel_id)
            ->whereIn('type', [
                Constants::notificationTypeReelLike,
                Constants::notificationTypeAddReelComment
            ])
            ->delete();

        $reel->delete();
 
        return response()->json([
            'status' => true,
            'message' => 'Reel Deleted Successfully.',
            'data' => $reel,
        ]);
    }

    public function fetchCommentsInReelModal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reel_id' => 'required|integer|exists:reels,id',
            'start' => 'required|integer',
            'limit' => 'required|integer',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $comments = ReelComment::where('reel_id', $request->reel_id)
            ->with('user')
            ->orderByDesc('id')
            ->skip($request->start)
            ->take($request->limit)
            ->get();

        $totalComments = ReelComment::where('reel_id', $request->reel_id)->count();
        $hasMoreComments = $totalComments > ($request->start + $request->limit);

        return response()->json([
            'status' => true,
            'message' => 'Fetch Comments.',
            'data' => $comments,
            'hasMore' => $hasMoreComments,
        ]);
    }

    public function deleteReelCommentFromAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'comment_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $comment = ReelComment::where('id', $request->comment_id)->where('user_id', $request->user_id)->first();
        if (!$comment) {
            return response()->json([
                'status' => false,
                'message' => 'Comment not found'
            ]);
        }
        $commentCount = Reel::where('id', $comment->reel_id)->first();
        $commentCount->comments_count = max(0, $commentCount->comments_count - 1);
        $commentCount->save();

        SavedNotification::where('user_id', $request->user_id)
            ->where('comment_id', $comment->id)
            ->where('type', Constants::notificationTypeComment)
            ->delete();

        $comment->delete();


        return response()->json([
            'status' => true,
            'message' => 'Delete comment successfully',
            'data' => $comment
        ]);
    }

    public function deleteReelByAdmin(Request $request)
    {
        $reel = Reel::find($request->reel_id);
        if (!$reel) {
            return response()->json([
                'status' => false,
                'message' => 'Reel Not Found.'
            ]);
        }

        $comments = $reel->comments ?? collect();

        foreach ($comments as $comment) {
            SavedNotification::where('reel_comment_id', $comment->id)
                ->whereIn('type', [
                    Constants::notificationTypeReelLike,
                    Constants::notificationTypeAddReelComment
                ])
                ->delete();
            $comment->delete();
        }

        Like::where('reel_id', $request->reel_id)->delete();

        Report::where('reel_id', $request->reel_id)->delete();

        GlobalFunction::deleteFile($reel->thumbnail);
        GlobalFunction::deleteFile($reel->content);

        SavedNotification::where('reel_id', $request->reel_id)
            ->whereIn('type', [
                Constants::notificationTypeReelLike,
                Constants::notificationTypeAddReelComment
            ])
            ->delete();

        $reel->delete();

        return response()->json([
            'status' => true,
            'message' => 'Reel Deleted Successfully.',
            'data' => $reel
        ]);
    }

    public function musics()
    {
        $categories = MusicCategory::where('is_deleted', Constants::DeletedNo)->orderByDesc('id')->get();
        return view('musics', [
            'categories' => $categories
        ]);
    }

    public function categoryList(Request $request)
    {
        $query = MusicCategory::where('is_deleted', Constants::DeletedNo);
        $totalData = $query->count();

        $columns = 'id';
        $orderDir = 'desc';
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumn = $columns;
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where('title', 'LIKE', "%{$searchValue}%");
        }

        $totalFiltered = $query->count();

        $result = $query->orderBy($orderColumn, $orderDir)
                        ->offset($start)
                        ->limit($limit)
                        ->get();

        $data = $result->map(function ($item) {

            $edit = '<a href="#" class="ms-3 btn btn-success px-4 text-white edit" rel="'.$item->id.'" data-title="'.$item->title.'" data-tooltip="Edit">'
            . '<svg data-name="Layer 1" height="200" id="Layer_1" viewBox="0 0 200 200" width="200" xmlns="http://www.w3.org/2000/svg">'
            . '<title></title><path d="M170,70.5a10,10,0,0,0-10,10V140a20.06,20.06,0,0,1-20,20H60a20.06,20.06,0,0,1-20-20V60A20.06,20.06,0,0,1,60,40h59.5a10,10,0,0,0,0-20H60A40.12,40.12,0,0,0,20,60v80a40.12,40.12,0,0,0,40,40h80a40.12,40.12,0,0,0,40-40V80.5A10,10,0,0,0,170,70.5Zm-77,39a9.67,9.67,0,0,0,14,0L164.5,52a9.9,9.9,0,0,0-14-14L93,95.5A9.67,9.67,0,0,0,93,109.5Z" fill="#fff"></path></svg>'
            . '</a>';
        
            $delete = '<a href="#" class="ms-3 btn btn-danger px-4 text-white delete" rel=' . $item->id . ' data-tooltip="Delete" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>';
            $action = '<span class="float-right">' . $edit . $delete . ' </span>';

            return [
                $item->title,
                $item->musics->count(),
                $action
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }

    public function addCategory(Request $request)
    {
        $category = new MusicCategory();
        $category->title = $request->title;
        $category->save();

        return response()->json([
            'status' => true,
            'message' => 'Category Added Successfully',
            'data' => $category,
        ]);
    }

    public function updateCategory(Request $request) 
    {
        $category = MusicCategory::where('id', $request->category_id)->first();

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category Not Found.',
            ]);
        }

        $category->title = $request->title;
        $category->save();

        return response()->json([
            'status' => true,
            'message' => 'Category Updated Successfully',
        ]);
    }

    public function deleteCategory(Request $request) 
    {
        $category = MusicCategory::find($request->category_id);

        if (!$category) {
            return response()->json([
                'status' => false,
                'message' => 'Category Not Found.',
            ]);
        }

        $category->is_deleted = Constants::Deleted;
        $category->save();

        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully.',
        ]);
    }

    public function musicList(Request $request)
    {
        $query = Music::where('is_deleted', Constants::DeletedNo);
        $totalData = $query->count();

        $columns = 'id';
        $orderDir = 'desc';
        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumn = $columns;
        $searchValue = $request->input('search.value');

        if (!empty($searchValue)) {
            $query->where('title', 'LIKE', "%{$searchValue}%");
        }

        $totalFiltered = $query->count();

        $result = $query->orderBy($orderColumn, $orderDir)
                        ->offset($start)
                        ->limit($limit)
                        ->get();

        $data = $result->map(function ($item) {

            if ($item->image == null) {
                $image = '<img src="asset/image/default.png" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            } else {
                $image = '<img src="' . $item->image . '" width="70" height="70" style="object-fit: cover;border-radius: 10px;box-shadow: 0px 10px 10px -8px #acacac;">';
            }

            $music = '<div class="d-flex align-items-center">'. $image . '<audio id="show_music" class="ms-3" src='. $item->sound .' controls="" ></audio></div>';

            $edit = '<a href="#" class="ms-3 btn btn-success px-4 text-white edit" 
                rel="'.$item->id.'" 
                data-title="'.$item->title.'" 
                data-category_id="'.$item->category_id.'" 
                data-sound="'.$item->sound.'" 
                data-duration="'.$item->duration.'" 
                data-artist="'.$item->artist.'" 
                data-tooltip="Edit">'
            . '<svg data-name="Layer 1" height="200" id="Layer_1" viewBox="0 0 200 200" width="200" xmlns="http://www.w3.org/2000/svg">'
            . '<path d="M170,70.5a10,10,0,0,0-10,10V140a20.06,20.06,0,0,1-20,20H60a20.06,20.06,0,0,1-20-20V60A20.06,20.06,0,0,1,60,40h59.5a10,10,0,0,0,0-20H60A40.12,40.12,0,0,0,20,60v80a40.12,40.12,0,0,0,40,40h80a40.12,40.12,0,0,0,40-40V80.5A10,10,0,0,0,170,70.5Zm-77,39a9.67,9.67,0,0,0,14,0L164.5,52a9.9,9.9,0,0,0-14-14L93,95.5A9.67,9.67,0,0,0,93,109.5Z" fill="#fff"></path></svg>'
            . '</a>';
        
            $delete = '<a href="#" class="ms-3 btn btn-danger px-4 text-white delete" rel=' . $item->id . ' data-tooltip="Delete" ><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>';

            $action = '<span class="float-right">' . $edit . $delete . ' </span>';

            return [
                $music,
                $item->title,
                $item->category->title,
                $item->duration,
                $item->artist,
                $action
            ];
        });

        $json_data = [
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data,
        ];

        return response()->json($json_data);
    }

    public function addMusic(Request $request)
    {
        $music = new Music();
        $music->title = $request->title;
        if ($request->hasFile("image")) {
            $music->image = GlobalFunction::saveFileAndGivePath($request->file("image"));
        }
        $music->category_id = $request->category_id;
        if ($request->hasFile("sound")) {
            $music->sound = GlobalFunction::saveFileAndGivePath($request->file("sound"));
        }
        $music->duration = $request->duration;
        $music->artist = $request->artist;
        $music->save();

        return response()->json([
            'status' => true,
            'message' => 'Music Added Successfully',
            'data' => $music,
        ]);
    }

    public function updateMusic(Request $request) 
    {
        $music = Music::find($request->music_id);

        if (!$music) {
            return response()->json([
                'status' => false,
                'message' => 'Music Not Found.',
            ]);
        }

        if ($request->has('title')) {
            $music->title = $request->title;
        }
        if ($request->hasFile("image")) {
            GlobalFunction::deleteFile($music->image);
            $music->image = GlobalFunction::saveFileAndGivePath($request->file("image"));
        }
        if ($request->has('category_id')) {
            $music->category_id = $request->category_id;
        }
        if ($request->hasFile("sound")) {
            GlobalFunction::deleteFile($music->sound);
            $music->sound = GlobalFunction::saveFileAndGivePath($request->file("sound"));
        }
        if ($request->has('duration')) {
            $music->duration = $request->duration;
        }
        if ($request->has('artist')) {
            $music->artist = $request->artist;
        }
        $music->save();

        return response()->json([
            'status' => true,
            'message' => 'Music Updated Successfully',
        ]);
    }

    public function deleteMusic(Request $request) 
    {
        $music = Music::find($request->music_id);

        if (!$music) {
            return response()->json([
                'status' => false,
                'message' => 'Music Not Found.',
            ]);
        }

        // GlobalFunction::deleteFile($music->image);
        // GlobalFunction::deleteFile($music->sound);

        $music->is_deleted = Constants::Deleted;
        $music->save();

        return response()->json([
            'status' => true,
            'message' => 'Music Deleted Successfully.',
        ]);
    }
    
}
