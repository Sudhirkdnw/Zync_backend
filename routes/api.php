<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FAQsController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileVerificationController;
use App\Http\Controllers\ReelController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::post('fetchUserList', [UserController::class, 'fetchUserList'])->middleware('checkHeader');
Route::post('addUser', [UserController::class, 'addUser'])->middleware('checkHeader');
Route::post('editProfile', [UserController::class, 'editProfile'])->middleware('checkHeader');
Route::post('followUser', [UserController::class, 'followUser'])->middleware('checkHeader');
Route::post('fetchFollowingList', [UserController::class, 'fetchFollowingList'])->middleware('checkHeader');
Route::post('fetchFollowersList', [UserController::class, 'fetchFollowersList'])->middleware('checkHeader');
Route::post('unfollowUser', [UserController::class, 'unfollowUser'])->middleware('checkHeader');
Route::post('searchFollower', [UserController::class, 'searchFollower'])->middleware('checkHeader');
Route::post('checkUsername', [UserController::class, 'checkUsername'])->middleware('checkHeader');
Route::post('fetchRandomProfile', [UserController::class, 'fetchRandomProfile'])->middleware('checkHeader');
Route::post('reportUser', [UserController::class, 'reportUser'])->middleware('checkHeader');
Route::post('fetchBlockedUserList', [UserController::class, 'fetchBlockedUserList'])->middleware('checkHeader');
Route::post('fetchPostByUser', [UserController::class, 'fetchPostByUser'])->middleware('checkHeader');
Route::post('fetchProfile', [UserController::class, 'fetchProfile'])->middleware('checkHeader');
Route::post('deleteUser', [UserController::class, 'deleteUser'])->middleware('checkHeader');
Route::post('searchProfile', [UserController::class, 'searchProfile'])->middleware('checkHeader');

// Moderator
Route::prefix('Moderator')->group(function () {
    Route::post('deletePostByModerator', [UserController::class, 'deletePostByModerator'])->middleware('checkHeader');
    Route::post('deleteCommentByModerator', [UserController::class, 'deleteCommentByModerator'])->middleware('checkHeader');
    Route::post('deleteRoomByModerator', [UserController::class, 'deleteRoomByModerator'])->middleware('checkHeader');
    Route::post('deleteStoryByModerator', [UserController::class, 'deleteStoryByModerator'])->middleware('checkHeader');
    Route::post('userBlockByModerator', [UserController::class, 'userBlockByModerator'])->middleware('checkHeader');
    Route::post('deleteReelCommentByModerator', [UserController::class, 'deleteReelCommentByModerator'])->middleware('checkHeader');
    Route::post('deleteReelByModerator', [UserController::class, 'deleteReelByModerator'])->middleware('checkHeader');
});
 
Route::post('uploadReel', [ReelController::class, 'uploadReel'])->middleware('checkHeader');
Route::post('likeDislikeReel', [ReelController::class, 'likeDislikeReel'])->middleware('checkHeader');
Route::post('increaseReelViewCount', [ReelController::class, 'increaseReelViewCount'])->middleware('checkHeader');
Route::post('addReelComment', [ReelController::class, 'addReelComment'])->middleware('checkHeader');
Route::post('fetchReelComments', [ReelController::class, 'fetchReelComments'])->middleware('checkHeader');
Route::post('deleteReelComment', [ReelController::class, 'deleteReelComment'])->middleware('checkHeader');
Route::post('deleteReel', [ReelController::class, 'deleteReel'])->middleware('checkHeader');
Route::post('fetchReelsByUserId', [ReelController::class, 'fetchReelsByUserId'])->middleware('checkHeader');
Route::post('fetchReelsOnExplore', [ReelController::class, 'fetchReelsOnExplore'])->middleware('checkHeader');
Route::post('searchReelsByInterestId', [ReelController::class, 'searchReelsByInterestId'])->middleware('checkHeader');
Route::post('fetchReelsByHashtag', [ReelController::class, 'fetchReelsByHashtag'])->middleware('checkHeader');
Route::post('reportReel', [ReelController::class, 'reportReel'])->middleware('checkHeader');
Route::post('fetchSavedReels', [ReelController::class, 'fetchSavedReels'])->middleware('checkHeader');
Route::post('fetchReelById', [ReelController::class, 'fetchReelById'])->middleware('checkHeader');

Route::post('fetchMusicWithSearch', [ReelController::class, 'fetchMusicWithSearch'])->middleware('checkHeader');
Route::post('fetchMusicCategories', [ReelController::class, 'fetchMusicCategories'])->middleware('checkHeader');
Route::post('fetchSavedMusic', [ReelController::class, 'fetchSavedMusic'])->middleware('checkHeader');
Route::post('fetchMusicByCategory', [ReelController::class, 'fetchMusicByCategory'])->middleware('checkHeader');
Route::post('fetchReelsByMusic', [ReelController::class, 'fetchReelsByMusic'])->middleware('checkHeader');

Route::post('logOut', [UserController::class, 'logOut'])->middleware('checkHeader');
Route::post('UserBlockedByUser', [UserController::class, 'UserBlockedByUser'])->middleware('checkHeader');
Route::post('UserUnblockedByUser', [UserController::class, 'UserUnblockedByUser'])->middleware('checkHeader');
Route::post('fetchUserNotification', [UserController::class, 'fetchUserNotification'])->middleware('checkHeader');

Route::post('profileVerification', [ProfileVerificationController::class, 'profileVerification'])->middleware('checkHeader');

Route::post('addPost', [PostController::class, 'addPost'])->middleware('checkHeader');
Route::post('fetchPosts', [PostController::class, 'fetchPosts'])->middleware('checkHeader');
Route::post('addComment', [PostController::class, 'addComment'])->middleware('checkHeader');
Route::post('fetchComments', [PostController::class, 'fetchComments'])->middleware('checkHeader');
Route::post('deleteComment', [PostController::class, 'deleteComment'])->middleware('checkHeader');
Route::post('likePost', [PostController::class, 'likePost'])->middleware('checkHeader');
Route::post('dislikePost', [PostController::class, 'dislikePost'])->middleware('checkHeader');
Route::post('reportPost', [PostController::class, 'reportPost'])->middleware('checkHeader');
Route::post('deleteMyPost', [PostController::class, 'deleteMyPost'])->middleware('checkHeader');
Route::post('fetchPostByPostId', [PostController::class, 'fetchPostByPostId'])->middleware('checkHeader');
Route::post('fetchPostsByHashtag', [PostController::class, 'fetchPostsByHashtag'])->middleware('checkHeader');
Route::post('createStory', [PostController::class, 'createStory'])->middleware('checkHeader');
Route::post('viewStory', [PostController::class, 'viewStory'])->middleware('checkHeader');
Route::post('fetchStory', [PostController::class, 'fetchStory'])->middleware('checkHeader');
Route::post('deleteStory', [PostController::class, 'deleteStory'])->middleware('checkHeader');
Route::post('fetchStoryByID', [PostController::class, 'fetchStoryByID'])->middleware('checkHeader');
Route::post('uploadFile', [PostController::class, 'uploadFile'])->middleware('checkHeader');
Route::post('fetchUsersWhoLikedPost', [PostController::class, 'fetchUsersWhoLikedPost'])->middleware('checkHeader');
Route::post('searchHashtag', [PostController::class, 'searchHashtag'])->middleware('checkHeader');
Route::post('searchPost', [PostController::class, 'searchPost'])->middleware('checkHeader');
Route::post('likeDislikeComment', [PostController::class, 'likeDislikeComment'])->middleware('checkHeader');
Route::post('searchPostByInterestId', [PostController::class, 'searchPostByInterestId'])->middleware('checkHeader');

Route::post('fetchInterests', [InterestController::class, 'fetchInterests'])->middleware('checkHeader');

Route::post('createRoom', [RoomController::class, 'createRoom'])->middleware('checkHeader');
Route::post('inviteUserToRoom', [RoomController::class, 'inviteUserToRoom'])->middleware('checkHeader');
Route::post('joinOrRequestRoom', [RoomController::class, 'joinOrRequestRoom'])->middleware('checkHeader');
Route::post('getInvitationList', [RoomController::class, 'getInvitationList'])->middleware('checkHeader');
Route::post('acceptInvitation', [RoomController::class, 'acceptInvitation'])->middleware('checkHeader');
Route::post('acceptRoomRequest', [RoomController::class, 'acceptRoomRequest'])->middleware('checkHeader');
Route::post('rejectInvitation', [RoomController::class, 'rejectInvitation'])->middleware('checkHeader');
Route::post('fetchRoomRequestList', [RoomController::class, 'fetchRoomRequestList'])->middleware('checkHeader');
Route::post('rejectRoomRequest', [RoomController::class, 'rejectRoomRequest'])->middleware('checkHeader');
Route::post('removeUserFromRoom', [RoomController::class, 'removeUserFromRoom'])->middleware('checkHeader');
Route::post('makeRoomAdmin', [RoomController::class, 'makeRoomAdmin'])->middleware('checkHeader');
Route::post('fetchRoomUsersList', [RoomController::class, 'fetchRoomUsersList'])->middleware('checkHeader');
Route::post('reportRoom', [RoomController::class, 'reportRoom'])->middleware('checkHeader');
Route::post('leaveThisRoom', [RoomController::class, 'leaveThisRoom'])->middleware('checkHeader');
Route::post('fetchRoomDetail', [RoomController::class, 'fetchRoomDetail'])->middleware('checkHeader');
Route::post('deleteRoom', [RoomController::class, 'deleteRoom'])->middleware('checkHeader');
Route::post('fetchMyOwnRooms', [RoomController::class, 'fetchMyOwnRooms'])->middleware('checkHeader');
Route::post('editRoom', [RoomController::class, 'editRoom'])->middleware('checkHeader');
Route::post('fetchSuggestedRooms', [RoomController::class, 'fetchSuggestedRooms'])->middleware('checkHeader');
Route::post('fetchRoomsByInterest', [RoomController::class, 'fetchRoomsByInterest'])->middleware('checkHeader');
Route::post('searchUserForInvitation', [RoomController::class, 'searchUserForInvitation'])->middleware('checkHeader');
Route::post('fetchRandomRooms', [RoomController::class, 'fetchRandomRooms'])->middleware('checkHeader');
Route::post('fetchRoomAdmins', [RoomController::class, 'fetchRoomAdmins'])->middleware('checkHeader');
Route::post('removeAdminFromRoom', [RoomController::class, 'removeAdminFromRoom'])->middleware('checkHeader');
Route::post('fetchRoomsList', [RoomController::class, 'fetchRoomsList'])->middleware('checkHeader');
Route::post('muteUnmuteRoomNotification', [RoomController::class, 'muteUnmuteRoomNotification'])->middleware('checkHeader');
Route::post('fetchRoomsIAmIn', [RoomController::class, 'fetchRoomsIAmIn'])->middleware('checkHeader');

Route::post('fetchSetting', [SettingsController::class, 'fetchSetting'])->middleware('checkHeader');
Route::post('pushNotificationToSingleUser', [SettingsController::class, 'pushNotificationToSingleUser'])->middleware('checkHeader');
Route::post('generateAgoraToken', [SettingsController::class, 'generateAgoraToken'])->middleware('checkHeader');

Route::post('fetchPlatformNotification', [AdminController::class, 'fetchPlatformNotification'])->middleware('checkHeader');

Route::post('fetchFAQs', [FAQsController::class, 'fetchFAQs'])->middleware('checkHeader');


Route::post('test', [PostController::class, 'test'])->middleware('checkHeader');