<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Constants;
use App\Models\FollowingList;
use App\Models\GlobalFunction;
use App\Models\Interest;
use App\Models\Like;
use App\Models\LikeComment;
use App\Models\Post;
use App\Models\PostContent;
use App\Models\Report;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\SavedNotification;
use App\Models\Setting;
use App\Models\Story;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    private function getSuggestedRooms($user)
    {
        $myRoomIds = RoomUser::where('user_id', $user->id)->pluck('room_id');
        $blockedUserIds = User::where('is_block', 1)->pluck('id');
        $setting = Setting::first();

        $suggestedRooms = [];

        if (empty($user->interest_ids)) {
            return Room::where('admin_id', '!=', $user->id)
                    ->where('is_private', 0)
                    ->whereNotIn('id', $myRoomIds)
                    ->whereNotIn('admin_id', $blockedUserIds)
                    ->inRandomOrder()
                    ->limit(2)
                    ->get();
        }

        $interestIds = explode(',', $user->interest_ids);
        shuffle($interestIds);

        foreach ($interestIds as $interestId) {
            $rooms = Room::where('admin_id', '!=', $user->id)
                        ->whereRaw('find_in_set("' . $interestId . '", interest_ids)')
                        ->where('is_private', 0)
                        ->whereNotIn('id', $myRoomIds)
                        ->whereNotIn('admin_id', $blockedUserIds)
                        ->inRandomOrder()
                        ->limit(2)
                        ->get();

            foreach ($rooms as $room) {
                if ($room->total_member < $setting->setRoomUsersLimit && count($suggestedRooms) < 2) {
                    $suggestedRooms[] = $room;
                }
            }
        }

        // If less than 2 rooms found, get more random rooms
        if (count($suggestedRooms) < 2) {
            $extraRooms = Room::where('admin_id', '!=', $user->id)
                            ->where('is_private', 0)
                            ->whereNotIn('id', $myRoomIds)
                            ->whereNotIn('admin_id', $blockedUserIds)
                            ->inRandomOrder()
                            ->limit(2 - count($suggestedRooms))
                            ->get();
            $suggestedRooms = array_merge($suggestedRooms, $extraRooms->toArray());
        }

        foreach ($suggestedRooms as &$suggestedRoom) {
            $roomUser = RoomUser::where('user_id', $user->id)->where('room_id', $suggestedRoom['id'])->first();
            $suggestedRoom['userRoomStatus'] = $roomUser ? $roomUser->type : 0;
        }

        return $suggestedRooms;
    }

    public function addPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'       => 'required|exists:users,id',
            'desc'          => 'nullable|string',
            'tags'          => 'nullable|string',
            'link_preview_json' => 'nullable|string',
            'interest_ids'  => 'nullable',
            'content'       => 'nullable|array',
            'content.*'     => 'file',
            'content_type'  => 'required_if:content,!null',
            'thumbnail'     => 'nullable|array',
            'thumbnail.*'   => 'file|mimes:jpeg,png',
            'audio_waves'   => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

        $post = new Post();
        $post->user_id = (int) $request->user_id;
        $post->desc = $request->desc;
        $post->tags = $request->tags;
        $post->link_preview_json = $request->link_preview_json;
        $post->interest_ids = $request->interest_ids;
        $post->save();

        // Handle file uploads if present
        if ($request->hasFile('content')) {
            foreach ($request->file('content') as $index => $file) {
                $postContent = new PostContent();
                $postContent->post_id = $post->id;
                $postContent->content = GlobalFunction::saveFileAndGivePath($file);
                $postContent->content_type = $request->content_type;

                if ($request->hasFile("thumbnail.{$index}")) {
                    $postContent->thumbnail = GlobalFunction::saveFileAndGivePath($request->file("thumbnail.{$index}"));
                }

                if ($request->filled('audio_waves')) {
                    $postContent->audio_waves = $request->audio_waves;
                }

                $postContent->save();
            }
        }

        return response()->json([
            'status'  => true,
            'message' => 'Post Uploaded',
            'data'    => $post->load('content'),
        ]);
    }

    public function fetchPosts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required|exists:users,id',
            'limit' => 'required|integer|min:1',
            'should_send_suggested_room' => 'required|boolean',
            'fetch_post_type' => 'required',
            'start' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $user = User::where('is_block', 0)->find($request->my_user_id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }

        $blockUserIds = explode(',', $user->block_user_ids);
        $fetchPostsQuery = Post::with(['content', 'user'])
            ->whereRelation('user', 'is_block', 0)
            ->whereNotIn('user_id', $blockUserIds)
            ->where('is_restricted', 0);

        if ($request->fetch_post_type == Constants::RandomPosts) {
            $fetchPostsQuery->inRandomOrder();
        } else {
            $fetchPostsQuery->latest();
            if ($request->start) {
                $fetchPostsQuery->offset($request->start);
            }
        }

        $fetchPosts = $fetchPostsQuery->limit($request->limit)->get();
        $fetchPosts = Post::processPosts($fetchPosts, $request->my_user_id);

        $suggestedRooms = $request->should_send_suggested_room ? $this->getSuggestedRooms($user) : [];

        return response()->json([
            'status' => true,
            'message' => 'Fetch posts successfully',
            'data' => $fetchPosts,
            'suggestedRooms' => $suggestedRooms,
        ]);
    }

    public function addComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'post_id' => 'required',
            'desc' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)->where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

        $post = Post::where('id', $request->post_id)->with(['user', 'content'])->first();
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post Not Found',
            ]);
        }

        $comment = new Comment();
        $comment->user_id = (int) $request->user_id;
        $comment->post_id = (int) $request->post_id;
        $comment->desc = $request->desc;
        $comment->save();

        $post->comments_count += 1;
        $post->save();

        $toUser = $post->user;

        if ($toUser->id != $request->user_id) {
            if ($toUser->is_push_notifications == Constants::pushNotification) {
                $notificationDesc = $user->full_name . ' has commented: ' . $request->desc;
                GlobalFunction::sendPushNotificationToUser($notificationDesc, $toUser->device_token, $toUser->device_type);
            }
        }

        $comment->post = $post;

        if ($user->id != $post->user_id) {
            $type = Constants::notificationTypeComment;

            $savedNotification = new SavedNotification();
            $savedNotification->my_user_id = (int) $post->user->id;
            $savedNotification->user_id = (int) $request->user_id;
            $savedNotification->post_id = (int) $request->post_id;
            $savedNotification->comment_id = (int) $comment->id;
            $savedNotification->type = $type;
            $savedNotification->save();
        }

        return response()->json([
            'status' => true,
            'message' => 'Comment Placed',
            'data' => $comment
        ]);
    }

    public function fetchComments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'my_user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $fetchComments = Comment::where('post_id', $request->post_id)
            ->with(['user' => function ($query) {
                $query->where('is_block', 0);
            }, 'likes' => function ($query) use ($request) {
                $query->where('user_id', $request->my_user_id);
            }])
            ->withCount('likes as comment_like_count')
            ->orderBy('id', 'DESC')
            ->offset($request->start)
            ->limit($request->limit)
            ->get();

        foreach ($fetchComments as $fetchComment) {
            $fetchComment->is_like = $fetchComment->likes->isNotEmpty() ? 1 : 0;
        }

        return response()->json([
            'status' => true,
            'message' => 'Fetch Comments',
            'data' => $fetchComments,
        ]);
    }

    public function deleteComment(Request $request)
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

        $comment = Comment::where('id', $request->comment_id)->where('user_id', $request->user_id)->first();

        if ($comment) {

            $commentCount = Post::where('id', $comment->post_id)->first();
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

        return response()->json([
            'status' => false,
            'message' => 'Comment not found'
        ]);
    }

    public function likePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)
            ->where('id', $request->user_id)
            ->first();
        if ($user) {
            $post = Post::where('id', $request->post_id)->with(['user', 'content'])->first();
            if ($post) {
                $likeRecord = Like::where('user_id', $request->user_id)->where('post_id', $request->post_id)->first();
                if ($likeRecord) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Already Liked',
                    ]);
                } else {
                    $like = new Like();
                    $like->user_id = (int) $request->user_id;
                    $like->post_id = (int) $request->post_id;
                    $like->save();

                    $post->likes_count += 1;
                    $post->save();

                    $postUser = $post->user;

                    if ($postUser->id != $request->user_id) {
                        if ($postUser->is_push_notifications == 1) {
                            $notificationDesc = $user->full_name . ' has liked your post.';
                            GlobalFunction::sendPushNotificationToUser($notificationDesc, $postUser->device_token, $postUser->device_type);
                        }
                    }

                    if ($user->id != $post->user_id) {
                        $type = Constants::notificationTypePostLike;

                        $savedNotification = new SavedNotification();
                        $savedNotification->my_user_id = (int) $post->user->id;
                        $savedNotification->user_id = (int) $request->user_id;
                        $savedNotification->post_id = (int) $request->post_id;
                        $savedNotification->type = $type;
                        $savedNotification->save();
                    }

                    $like->post = $post;

                    return response()->json([
                        'status' => true,
                        'message' => 'Post Liked',
                        'data' => $like,
                    ]);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Post Not Found',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }
    }

    public function dislikePost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $user = User::where('is_block', 0)
            ->where('id', $request->user_id)
            ->first();
        if ($user) {
            $likedPost = Like::where('user_id', $request->user_id)->where('post_id', $request->post_id)->first();
            if ($likedPost) {
                $likeCount = Post::where('id', $request->post_id)->first();
                $likeCount->likes_count = max(0, $likeCount->likes_count - 1);
                $likeCount->save();

                $likedPost->delete();

                $userNotification = SavedNotification::where('post_id', $request->post_id)
                    ->where('user_id', $request->user_id)
                    ->where('type', Constants::notificationTypePostLike)
                    ->get();
                $userNotification->each->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Post Dislike',
                    'data' => $likedPost,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Post Already Dislike',
                ]);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
    }

    public function reportPostList(Request $request)
    {
        $reportType = 1;
        $totalData = Report::where('type', $reportType)->count();
        $rows = Report::where('type', $reportType)->orderBy('id', 'DESC')->get();

        $result = $rows;

        $columns = [
            0 => 'id',
            1 => 'post_id',
            2 => 'reason',
            3 => 'desc',
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

            $post = Post::where('id', $item->post_id)->first();

            $postContent = PostContent::where('post_id', $item->post_id)->get();
            $contentType = $postContent->count() == 0 ? 3 : $postContent->first()->content_type;
            $firstContent = $postContent->pluck('content');

            if ($item->desc == null) {
                $item->desc = 'Note: Post has no description';
            }

            $profile = $item->post->user->profile ?? "null";
            if ($contentType == 0) {
                $viewPost = '<button type="button" class="btn btn-primary viewPost commonViewBtn" data-bs-toggle="modal" data-username=' . $item->post->user->username . ' data-profile=' . $profile  . ' data-image=' . $firstContent . ' data-userid=' . $item->post->user->id . '  data-desc="' . $post->desc . '" data-postid="' . $post->id . '" rel="' . $item->id . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg> View Post</button>';
            } elseif ($contentType == 1) {
                $viewPost = '<button type="button" class="btn btn-primary viewVideoPost commonViewBtn" data-bs-toggle="modal" data-username=' . $item->post->user->username . ' data-profile=' . $profile  . ' data-userid=' . $item->post->user->id . ' data-image=' . $firstContent . ' data-desc="' . $post->desc . '" data-postid="' . $post->id . '" rel="' . $item->id . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg> View Post</button>';
            } elseif ($contentType == 2) {
                $viewPost = '<button type="button" class="btn btn-primary viewAudioPost commonViewBtn" data-bs-toggle="modal" data-username=' . $item->post->user->username . ' data-profile=' . $profile  . ' data-audio=' . $firstContent . ' data-userid=' . $item->post->user->id . ' data-desc="' . $post->desc . '" data-postid="' . $post->id . '" rel="' . $item->id . '">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"></path><path d="M19 10v2a7 7 0 0 1-14 0v-2"></path><line x1="12" y1="19" x2="12" y2="23"></line><line x1="8" y1="23" x2="16" y2="23"></line></svg> View Post</button>';
            } else {
                $viewPost = '<button type="button" class="btn btn-primary viewDescPost commonViewBtn" data-bs-toggle="modal" data-username=' . $item->post->user->username . ' data-profile=' . $profile  . ' data-desc="' . $post->desc . '" data-userid=' . $item->post->user->id . ' data-postid="' . $post->id . '" rel="' . $item->id . '">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-type"><polyline points="4 7 4 4 20 4 20 7"></polyline><line x1="9" y1="20" x2="15" y2="20"></line><line x1="12" y1="4" x2="12" y2="20"></line></svg> View Post</button>';
            }

            $rejectReport = '<a href="#" class="me-3 btn btn-orange px-4 text-white rejectReport d-flex align-items-center" rel=' . $item->id . ' data-tooltip="Reject Report" >' . __(' <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg> <span class="ms-2"> Reject </span>') . '</a>';
            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deletePost d-flex align-items-center" rel=' . $item->id . ' data-tooltip="Delete Post">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $rejectReport . $delete . ' </span>';

            $data[] = [
                $viewPost,
                $item->reason,
                $item->desc,
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

    public function reportPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'reason' => 'required',
            'desc' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }
        $post = Post::where('id', $request->post_id)
            ->get()
            ->first();

        if ($post != null) {
            $validator = Validator::make($request->all(), [
                'post_id' => 'required',
                'reason' => 'required',
                'desc' => 'required',
            ]);

            if ($validator->fails()) {
                $messages = $validator->errors()->all();
                $msg = $messages[0];
                return response()->json(['status' => false, 'message' => $msg]);
            }

            $reportType = 1;

            $report = new Report();
            $report->type = $reportType;
            $report->post_id = $request->post_id;
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
                'message' => 'Post Not Found',
            ]);
        }
    }

    public function deleteMyPost(Request $request)
    {
        $post = Post::where('id', $request->post_id)->where('user_id', $request->user_id)->first();
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post Not Found',
            ]);
        }

        $postComments = Comment::where('post_id', $request->post_id)->get();
        foreach ($postComments as $comment) {
            LikeComment::where('comment_id', $comment->id)->delete();
        }
        $postComments->each->delete();

        $postContents = PostContent::where('post_id', $request->post_id)->get();
        foreach ($postContents as $postContent) {
            GlobalFunction::deleteFile($postContent->content);
            GlobalFunction::deleteFile($postContent->thumb);
        }
        $postContents->each->delete();

        Like::where('post_id', $request->post_id)->delete();
        SavedNotification::where('post_id', $request->post_id)->delete();
        Report::where('post_id', $request->post_id)->where('type', 1)->delete();

        $post->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post Delete Successfully',
            'data' => $post,
        ]);
    }

    public function createStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'type' => 'required',
            'content' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $story = new Story();
        $story->user_id = (int) $request->user_id;
        $story->duration = (float) $request->duration;
        $story->type = (int) $request->type;
        if ($request->hasFile('content')) {
            $files = $request->file('content');
            $path = GlobalFunction::saveFileAndGivePath($files);
            $story->content = $path;
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $path = GlobalFunction::saveFileAndGivePath($file);
            $story->thumbnail = $path;
        }

        $story->save();

        return response()->json([
            'status' => true,
            'message' => 'Story Added Successfully',
            'data' => $story,
        ]);
    }

    public function viewStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'story_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)
            ->where('id', $request->user_id)
            ->first();
        if ($user) {

            $viewStory = Story::where('id', $request->story_id)->first();

            $viewStoryByUserIds = explode(',', $viewStory->view_by_user_ids);

            if ($viewStory) {

                if (in_array($request->user_id, $viewStoryByUserIds)) {

                    return response()->json([
                        'status' => true,
                        'message' => 'Story Viewed',
                        'data' => $viewStory,
                    ]);
                } else {

                    $viewStory->view_by_user_ids = $viewStory->view_by_user_ids . $request->user_id . ',';
                    $viewStory->save();

                    return response()->json([
                        'status' => true,
                        'message' => 'Story Viewed',
                        'data' => $viewStory,
                    ]);
                }
            }
            return response()->json([
                'status' => false,
                'message' => 'Story not found',
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);
    }

    public function fetchStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $followingUsers = FollowingList::where('my_user_id', $request->my_user_id)
            ->whereHas('user', function ($query) {
                $query->where('is_block', Constants::unblocked);
            })
            ->with(['user' => function ($query) {
                $query->with(['stories' => function ($storyQuery) {
                    $storyQuery->where('created_at', '>=', now()->subDay());
                }]);
            }])
            ->get()
            ->pluck('user')
            ->filter(function ($user) {
                return $user->stories->isNotEmpty();
            })
            ->values();

        return response()->json([
            'status' => true,
            'message' => 'Story fetched successfully.',
            'data' => $followingUsers,
        ]);
    }

    public function uploadFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uploadFile' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        if ($request->hasFile('uploadFile')) {
            $file = $request->file('uploadFile');

            $path = GlobalFunction::saveFileAndGivePath($file);

            return response()->json([
                'status' => true,
                'message' => "Uploaded file path",
                'data' => $path,
            ]);
        }
    }

    public function fetchUsersWhoLikedPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'post_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)->where('id', $request->user_id)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

        $blockUserIds = explode(',', $user->block_user_ids);

        $likes = Like::with('user')
            ->where('post_id', $request->post_id)
            // ->whereHas('user', function ($query) use ($blockUserIds) {
            //     $query->where('is_block', 0)
            //     ->whereNotIn('id', $blockUserIds);
            // })
            ->get();

        return response()->json([
            'status' => true,
            'message' => "Fetch Liked By User",
            'data' => $likes,
        ]);
    }

    public function searchHashtag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)
            ->where('id', $request->user_id)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

        $blockUserIds = explode(',', $user->block_user_ids);

        $posts = Post::whereRelation('user', 'is_block', 0)
            ->whereNotIn('user_id', $blockUserIds)
            ->orderBy('id', 'DESC')
            ->get();

        $tagCounts = [];

        foreach ($posts as $post) {
            $tags = array_map('trim', explode(',', $post->tags));
            foreach ($tags as $tag) {
                if (!empty($tag)) {
                    if (!array_key_exists($tag, $tagCounts)) {
                        $tagCounts[$tag] = 0;
                    }
                    $tagCounts[$tag]++;
                }
            }
        }

        $formattedTags = [];
        foreach ($tagCounts as $tag => $count) {
            $formattedTags[] = ['tag' => $tag, 'post_count' => $count];
        }

        return response()->json([
            'status' => true,
            'message' => 'Search Hashtag Successfully',
            'data' => $formattedTags
        ]);
    }

    public function searchPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|integer|min:0',
            'limit' => 'required|integer|min:1',
            'keyword' => 'nullable|string',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)
            ->where('id', $request->user_id)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

        $blockUserIds = explode(',', $user->block_user_ids);

        $fetchPosts = Post::with(['content', 'user'])
                        ->where('desc', 'like', '%' . $request->keyword . '%')
                        ->offset($request->start)
                        ->limit($request->limit)
                        ->whereRelation('user', 'is_block', 0)
                        ->whereNotIn('user_id', $blockUserIds)
                        ->orderByDesc('id')
                        ->get();

        foreach ($fetchPosts as $fetchPost) {
            $isPostLike = Like::where('user_id', $request->user_id)->where('post_id', $fetchPost->id)->first();
            if ($isPostLike) {
                $fetchPost->is_like = 1;
            } else {
                $fetchPost->is_like = 0;
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Search Post Successfully',
            'data' => $fetchPosts,
        ]);
    }

    public function likeDislikeComment(Request $request)
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

        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ]);
        }

        $comment = Comment::where('id', $request->comment_id)->first();
        if (!$comment) {
            return response()->json([
                'status' => false,
                'message' => 'Comment not found'
            ]);
        }

        $fetchLikeComment = LikeComment::where('user_id', $request->user_id)->where('comment_id', $request->comment_id)->first();

        if ($fetchLikeComment) {

            $fetchLikeComment->delete();

            return response()->json([
                'status' => true,
                'message' => 'Dislike Comment Successfully.',
                'data' => $fetchLikeComment
            ]);
        } else {

            $likeComment = new LikeComment();
            $likeComment->user_id = (int) $request->user_id;
            $likeComment->comment_id = (int) $request->comment_id;
            $likeComment->save();

            return response()->json([
                'status' => true,
                'message' => 'Like Comment Successfully',
                'data' => $likeComment
            ]);
        }
    }

    public function searchPostByInterestId(Request $request)
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

        $fetchInterest = Interest::find($request->interest_id);
        if (!$fetchInterest) {
            return response()->json([
                'status' => false,
                'message' => 'Interest Not Found.',
            ]);
        }

        $user = User::where('is_block', Constants::unblocked)
                    ->where('id', $request->user_id)
                    ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User Not Found',
            ]);
        }

        $blockUserIds = explode(',', $user->block_user_ids);

        $posts = Post::with(['content', 'user'])
                        ->where('desc', 'like', '%' . $request->keyword . '%')
                        ->whereRelation('user', 'is_block', Constants::unblocked)
                        ->whereNotIn('user_id', $blockUserIds)
                        ->whereRaw('find_in_set("' . $request->interest_id . '", interest_ids)')
                        ->offset($request->start)
                        ->limit($request->limit)
                        ->orderByDesc('id')
                        ->get();

        return response()->json([
            'status' => true,
            'message' => 'Search Posts By Interest.',
            'data' => $posts,
        ]);
    }


    // Web
    public function deletePostReport(Request $request)
    {
        $reports = Report::where('id', $request->report_id)->first();
        $deletePostReports = Report::where('post_id', $reports->post_id)->get();

        if ($deletePostReports) {
            $deletePostReports->each->delete();

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

    public function deletePost(Request $request)
    {

        $report = Report::where('id', $request->report_id)->first();

        if ($report) {
            $postContents = PostContent::where('post_id', $report->post_id)->get();
            foreach ($postContents as $postContent) {
                GlobalFunction::deleteFile($postContent->content);
                GlobalFunction::deleteFile($postContent->thumb);
            }
            $postContents->each->delete();

            $post = Post::where('id', $report->post_id)->first();
            $post->delete();

            $postComments = Comment::where('post_id', $request->post_id)->get();
            $postComments->each->delete();

            $postLikes = Like::where('post_id', $request->post_id)->get();
            $postLikes->each->delete();

            $deleteReportRecords = Report::where('post_id', $report->post_id)->get();
            $deleteReportRecords->each->delete();

            $userNotification = SavedNotification::where('post_id', $request->post_id)->get();
            $userNotification->each->delete();

            $report->delete();

            return response()->json([
                'status' => true,
                'message' => 'Post Delete Successfully',
                'data' => $postContents,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'Report Not Found',
        ]);
    }

    public function fetchPostsByHashtag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'tag' => 'required',
            'start' => 'required',
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

        if ($user) {
            $blockUserIds = explode(',', $user->block_user_ids);

            $hashtag = Post::whereRelation('user', 'is_block', 0)
                ->whereRaw('find_in_set("' . $request->tag . '", tags)')
                ->whereNotIn('user_id', $blockUserIds)
                ->with(['content', 'user'])
                ->orderBy('id', 'DESC')
                ->offset($request->start)
                ->limit($request->limit)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Fetch posts by hashtag successfully',
                'data' => $hashtag,
            ]);
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);
    }

    public function fetchPostByPostId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'post_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $user = User::where('is_block', 0)
            ->where('id', $request->my_user_id)
            ->first();

        if ($user) {

            $blockUserIds = explode(',', $user->block_user_ids);

            $fetchPost = Post::where('id', $request->post_id)->with(['content', 'user'])
                ->whereRelation('user', 'is_block', 0)
                ->whereNotIn('user_id', $blockUserIds)
                ->first();

            if ($fetchPost) {
                $isPostLike = Like::where('user_id', $request->my_user_id)->where('post_id', $fetchPost->id)->first();
                $fetchPost->is_like = $isPostLike ? 1 : 0;

                return response()->json([
                    'status' => true,
                    'message' => 'Fetch posts',
                    'data' => $fetchPost,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Posts not Available',
                ]);
            }
        }
        return response()->json([
            'status' => false,
            'message' => 'User not found',
        ]);
    }

    public function viewPosts()
    {
        return view('viewPosts');
    }

    public function allPostsList(Request $request)
    {
        $totalData = Post::count();
        $rows = Post::orderBy('id', 'DESC')->get();

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
        $searchValue = $request->input('search.value');

        $query = Post::query();

        if (!empty($searchValue)) {
            $query->whereHas('user', function ($q) use ($searchValue) {
                $q->where('full_name', 'LIKE', "%{$searchValue}%")
                    ->orWhere('username', 'LIKE', "%{$searchValue}%");
            });
            $totalFiltered = $query->count();
        }

        $result = $query->with('user')
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $data = [];

        $fetchInterests = Interest::get();

        foreach ($result as $item) {

            $postContent = PostContent::where('post_id', $item->id)->get();
            $contentType = $postContent->count() == 0 ? 3 : $postContent->first()->content_type;
            $firstContent = $postContent->pluck('content');

            if ($item->desc == null) {
                $item->desc = 'Note: Post has no description';
            }

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

            $userName = '<a href="./usersDetail/' . $item->user->id . '"> ' . $item->user->username . ' </a>';

            $restricted = '<label class="switch"><input type="checkbox" name="restricted" rel="' . $item->id . '" value="' . $item->is_restricted . '" id="postRestricted" class="postRestricted"' . ($item->is_restricted == 1 ? ' checked' : '') . '><span class="slider"></span> </label>';

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deletePost d-flex align-items-center" rel=' . $item->id . ' data-tooltip="Delete Post">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $delete . ' </span>';

            $data[] = [
                $viewPost,
                $userName,
                $item->user->full_name,
                $item->comments_count,
                $item->likes_count,
                $restricted,
                $item->created_at->format('d-m-Y'),
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

    public function deleteStory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'my_user_id' => 'required',
            'story_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $story = Story::where('id', $request->story_id)->where('user_id', $request->my_user_id)->first();

        if ($story) {

            GlobalFunction::deleteFile($story->content);
            if ($story->type == 1) {
                GlobalFunction::deleteFile($story->thumbnail);
            }
            $story->delete();

            return response()->json([
                'status' => true,
                'message' => 'Story delete successfully',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Story not found'
        ]);
    }

    function fetchStoryByID(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'story_id' => 'required',
        ]);

        if ($validator->fails()) {
            $messages = $validator->errors()->all();
            $msg = $messages[0];
            return response()->json(['status' => false, 'message' => $msg]);
        }

        $story = Story::with('user')->where('id', $request->story_id)->first();
        if (!$story) {
            return response()->json([
                'status' => false,
                'message' => 'Story not found'
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Fetch Story By ID successfully',
            'data' => $story,
        ]);
    }

    public function updatePostRestrictionStatus(Request $request)
    {
        $post = Post::where('id', $request->id)->first();
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found',
            ]);
        }

        $post->is_restricted = $request->is_restricted;
        $post->save();

        return response()->json([
            'status' => true,
            'message' => 'Post Status Updated successfully',
        ]);
    }

    // web Admin panel post modal
    public function fetchPostComment(Request $request)
    {
        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'post_id' => 'required|integer',
            'start' => 'required|integer',
            'limit' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $fetchComments = Comment::where('post_id', $request->post_id)
                                ->with('user')
                                ->withCount('likes as comment_like_count')
                                ->orderBy('id', 'DESC')
                                ->offset($request->start)
                                ->limit($request->limit)
                                ->get();

        $hasMoreComments = Comment::where('post_id', $request->post_id)->count() > ($request->start + $request->limit);

        return response()->json([
            'status' => true,
            'message' => 'Fetch Comments',
            'data' => $fetchComments,
            'hasMore' => $hasMoreComments,
        ]);
    }

    public function deleteCommentFromAdmin(Request $request)
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

        $comment = Comment::where('id', $request->comment_id)->where('user_id', $request->user_id)->first();
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

    // CronJob start
    public function deleteStoryFromWeb()
    {
        $stories = Story::where('created_at', '<=', now()->subDay()->toDateTimeString())->get();

        if ($stories) {
            foreach ($stories as $story) {
                GlobalFunction::deleteFile($story->content);
                if ($story->type == 1) {
                    GlobalFunction::deleteFile($story->thumbnail);
                }
                $story->delete();
            }
        }
    }
    // CronJob End

    public function userStoryList(Request $request)
    {
        $twentyFourHoursAgo = Carbon::now()->subDay();

        $totalData = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->where('user_id', $request->user_id)
            ->count();

        $rows = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->where('user_id', $request->user_id)
            ->orderBy('id', 'DESC')
            ->get();

        $result = $rows;

        $columns = [
            0 => 'id'
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;

        if (!empty($request->input('search.value'))) {
            $search = $request->input('search.value');
            $result = Story::where('created_at', '>=', $twentyFourHoursAgo)
                ->where('created_at', '<=', Carbon::now())
                ->where('user_id', $request->user_id)
                ->where(function ($query) use ($search) {
                    $query->whereHas('user', function ($q) use ($search) {
                        $q->where('full_name', 'like', "%{$search}%");
                        // Add more conditions for searching other user fields if needed
                    });
                    // Add more conditions for searching other story fields if needed
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
            $totalFiltered = $result->count(); // Count filtered result
        } else {
            $result = Story::where('created_at', '>=', $twentyFourHoursAgo)
                ->where('created_at', '<=', Carbon::now())
                ->where('user_id', $request->user_id)
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        }

        $data = [];


        foreach ($result as $item) {
            $contentType = $item->type;

            $timeAgo = Carbon::parse($item->created_at)->diffForHumans();

            $viewStory = ($contentType == 0) ? '<button type="button" class="btn btn-primary viewStory commonViewBtn" data-bs-toggle="modal" data-image="' . $item->content . '" rel="' . $item->id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg> View Story</button>'
                : '<button type="button" class="btn btn-primary viewStoryVideo commonViewBtn" data-bs-toggle="modal" data-image="' . $item->content . '" rel="' . $item->id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg> View Story</button>';

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deleteStory d-flex align-items-center" rel="' . $item->id . '" data-tooltip="Delete Story">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $delete . ' </span>';

            $data[] = [
                $viewStory,
                $timeAgo,
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

    public function viewStories()
    {
        return view('viewStories');
    }

    public function deleteStoryFromAdmin(Request $request)
    {
        $story = Story::where('id', $request->story_id)->first();

        if ($story) {

            GlobalFunction::deleteFile($story->content);
            $story->delete();

            return response()->json([
                'status' => true,
                'message' => 'Story delete successfully',
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Story not found'
        ]);
    }

    public function allStoriesList(Request $request)
    {

        $twentyFourHoursAgo = Carbon::now()->subDay();

        $totalData = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->count();

        $rows = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now())
            ->orderBy('id', 'DESC')
            ->get();

        $result = $rows;

        $columns = [
            0 => 'id'
        ];

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        $totalFiltered = $totalData;

        $searchValue = $request->input('search.value');

        $query = Story::where('created_at', '>=', $twentyFourHoursAgo)
            ->where('created_at', '<=', Carbon::now());

        if (!empty($searchValue)) {
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('user', function ($q) use ($searchValue) {
                    $q->where('full_name', 'LIKE', "%{$searchValue}%");
                });
            });
        }

        $result = $query->with('user') // Eager load the user relationship if needed
            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        if (!empty($searchValue)) {
            $totalFiltered = $result->count();
        }

        $data = [];


        foreach ($result as $item) {
            $userName = '<a href="usersDetail/' . $item->user_id . '">' .  $item->user->full_name . '</a>';
            $contentType = $item->type;

            $timeAgo = Carbon::parse($item->created_at)->diffForHumans();

            $viewStory = ($contentType == 0) ? '<button type="button" class="btn btn-primary viewStory commonViewBtn" data-bs-toggle="modal" data-image="' . $item->content . '" rel="' . $item->id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-image"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg> View Story</button>'
                : '<button type="button" class="btn btn-primary viewStoryVideo commonViewBtn" data-bs-toggle="modal" data-image="' . $item->content . '" rel="' . $item->id . '">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-video"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg> View Story</button>';

            $delete = '<a href="#" class="btn btn-danger px-4 text-white delete deleteStory d-flex align-items-center" rel="' . $item->id . '" data-tooltip="Delete Story">' . __('<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg> ') . '</a>';
            $action = '<span class="float-right d-flex">' . $delete . ' </span>';

            $data[] = [
                $viewStory,
                $userName,
                $timeAgo,
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

    public function test(Request $request)
    {
        $roomUser = RoomUser::where('room_id', $request->room_id)
            ->where(function ($query) {
                $query->where('type', 2)
                    ->orWhere('type', 3);
            })->count();
        return response()->json([
            'status' => true,
            'message' => 'Room User',
            'data' => $roomUser,
        ]);
    }


}

