<?php 

use App\Http\Controllers\AdminController;
use App\Http\Controllers\FAQsController;
use App\Http\Controllers\InterestController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileVerificationController;
use App\Http\Controllers\ReelController;
use App\Http\Controllers\RestrictionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [LoginController::class, 'login'])->name('/');
Route::post('login', [LoginController::class, 'checklogin'])->middleware(['checkLogin'])->name('login');
Route::get('index', [SettingsController::class, 'index'])->middleware(['checkLogin'])->name('index');
Route::get('logout', [LoginController::class, 'logout'])->middleware(['checkLogin'])->name('logout');

Route::get('fetchUsersForChart', [SettingsController::class, 'fetchUsersForChart'])->name('fetchUsersForChart');
Route::get('fetchPostsForChart', [SettingsController::class, 'fetchPostsForChart'])->name('fetchPostsForChart');
Route::get('fetchReelsForChart', [SettingsController::class, 'fetchReelsForChart'])->name('fetchReelsForChart');
Route::get('fetchRoomsForChart', [SettingsController::class, 'fetchRoomsForChart'])->name('fetchRoomsForChart');

// Notification
Route::get('notification', [AdminController::class, 'notification'])->middleware(['checkLogin'])->name('notification');
Route::post('sendNotification', [AdminController::class, 'sendNotification'])->middleware(['checkLogin'])->name('sendNotification');
Route::post('adminNotificationList', [AdminController::class, 'adminNotificationList'])->middleware(['checkLogin'])->name('adminNotificationList');
Route::post('updateNotification', [AdminController::class, 'updateNotification'])->middleware(['checkLogin'])->name('updateNotification');
Route::post('repeatNotification', [AdminController::class, 'repeatNotification'])->middleware(['checkLogin'])->name('repeatNotification');
Route::post('deleteNotification', [AdminController::class, 'deleteNotification'])->middleware(['checkLogin'])->name('deleteNotification');
Route::post('changePassword', [AdminController::class, 'changePassword'])->middleware(['checkLogin'])->name('changePassword');

Route::get('rooms', [RoomController::class, 'rooms'])->middleware(['checkLogin'])->name('rooms');
Route::post('roomsListWeb', [RoomController::class, 'roomsListWeb'])->middleware(['checkLogin'])->name('roomsListWeb');
Route::post('deleteThisRoom', [RoomController::class, 'deleteThisRoom'])->middleware(['checkLogin'])->name('deleteThisRoom');

Route::get('interests', [InterestController::class, 'interests'])->middleware(['checkLogin'])->name('interests');
Route::post('addInterest', [InterestController::class, 'addInterest'])->middleware(['checkLogin'])->name('addInterest');
Route::post('interestList', [InterestController::class, 'interestList'])->middleware(['checkLogin'])->name('interestList');
Route::post('updateInterest', [InterestController::class, 'updateInterest'])->middleware(['checkLogin'])->name('updateInterest');
Route::post('deleteInterest/{id}', [InterestController::class, 'deleteInterest'])->middleware(['checkLogin'])->name('deleteInterest');

// Pages Routes

Route::get('deleteStoryFromWeb', [PostController::class, 'deleteStoryFromWeb'])->name('deleteStoryFromWeb');

Route::get('viewPrivacy', [PagesController::class, 'viewPrivacy'])->middleware(['checkLogin'])->name('viewPrivacy');
Route::post('updatePrivacy', [PagesController::class, 'updatePrivacy'])->middleware(['checkLogin'])->name('updatePrivacy');
Route::post('addContentForm', [PagesController::class, 'addContentForm'])->middleware(['checkLogin'])->name('addContentForm');
Route::post('addTermsForm', [PagesController::class, 'addTermsForm'])->middleware(['checkLogin'])->name('addTermsForm');
Route::post('updateTerms', [PagesController::class, 'updateTerms'])->middleware(['checkLogin'])->name('updateTerms');
Route::get('viewTerms', [PagesController::class, 'viewTerms'])->middleware(['checkLogin'])->name('viewTerms');
Route::get('privacyPolicy', [PagesController::class, 'privacyPolicy'])->name('privacyPolicy');
Route::get('termsOfUse', [PagesController::class, 'termsOfUse'])->name('termsOfUse');

Route::get('reports', [RoomController::class, 'reports'])->middleware(['checkLogin'])->name('reports');
Route::post('reportList', [RoomController::class, 'reportList'])->middleware(['checkLogin'])->name('reportList');
Route::post('deleteReport', [RoomController::class, 'deleteReport'])->middleware(['checkLogin'])->name('deleteReport');
Route::post('deleteReportWithRoom', [RoomController::class, 'deleteReportWithRoom'])->middleware(['checkLogin'])->name('deleteReportWithRoom');
Route::post('updatePrivateStatus', [RoomController::class, 'updatePrivateStatus'])->middleware(['checkLogin'])->name('updatePrivateStatus');
Route::post('updateJoinRequestStatus', [RoomController::class, 'updateJoinRequestStatus'])->middleware(['checkLogin'])->name('updateJoinRequestStatus');
Route::get('roomDetails/{id}', [RoomController::class, 'roomDetails'])->middleware(['checkLogin'])->name('roomDetails');
Route::post('allRoomUsersListTableWeb', [RoomController::class, 'allRoomUsersListTableWeb'])->middleware(['checkLogin'])->name('allRoomUsersListTableWeb');
Route::post('roomMembersListTableWeb', [RoomController::class, 'roomMembersListTableWeb'])->middleware(['checkLogin'])->name('roomMembersListTableWeb');
Route::post('roomCoAdminTableWeb', [RoomController::class, 'roomCoAdminTableWeb'])->middleware(['checkLogin'])->name('roomCoAdminTableWeb');
Route::post('userRoomsOwnTable', [RoomController::class, 'userRoomsOwnTable'])->middleware(['checkLogin'])->name('userRoomsOwnTable');
Route::post('userRoomInTable', [RoomController::class, 'userRoomInTable'])->middleware(['checkLogin'])->name('userRoomInTable');

Route::get('verificationRequests', [ProfileVerificationController::class, 'verificationRequests'])->middleware(['checkLogin'])->name('verificationRequests');
Route::post('profileVerificationList', [ProfileVerificationController::class, 'profileVerificationList'])->middleware(['checkLogin'])->name('profileVerificationList');
Route::post('approvedProfileVerification/{id}', [ProfileVerificationController::class, 'approvedProfileVerification'])->middleware(['checkLogin'])->name('approvedProfileVerification');
Route::post('rejectProfileVerification/{id}', [ProfileVerificationController::class, 'rejectProfileVerification'])->middleware(['checkLogin'])->name('rejectProfileVerification');

Route::get('setting', [SettingsController::class, 'settingView'])->middleware(['checkLogin'])->name('setting');
Route::post('addDocumentType', [SettingsController::class, 'addDocumentType'])->middleware(['checkLogin'])->name('addDocumentType');
Route::post('documentTypeList', [SettingsController::class, 'documentTypeList'])->middleware(['checkLogin'])->name('documentTypeList');
Route::post('updateDocumentType/{id}', [SettingsController::class, 'updateDocumentType'])->middleware(['checkLogin'])->name('updateDocumentType');
Route::post('deleteDocumentType/{id}', [SettingsController::class, 'deleteDocumentType'])->middleware(['checkLogin'])->name('deleteDocumentType');
Route::post('updateSettings', [SettingsController::class, 'updateSettings'])->middleware(['checkLogin'])->name('updateSettings');

Route::post('addreportReason', [SettingsController::class, 'addreportReason'])->middleware(['checkLogin'])->name('addreportReason');
Route::post('reportReasonList', [SettingsController::class, 'reportReasonList'])->middleware(['checkLogin'])->name('reportReasonList');
Route::post('updateReportReason/{id}', [SettingsController::class, 'updateReportReason'])->middleware(['checkLogin'])->name('updateReportReason');
Route::post('deleteReportReasonType', [SettingsController::class, 'deleteReportReasonType'])->middleware(['checkLogin'])->name('deleteReportReasonType');

Route::post('changeAppName', [SettingsController::class, 'changeAppName'])->middleware(['checkLogin'])->name('changeAppName');

Route::get('viewPosts', [PostController::class, 'viewPosts'])->middleware(['checkLogin'])->name('viewPosts');
Route::post('reportPostList', [PostController::class, 'reportPostList'])->middleware(['checkLogin'])->name('reportPostList');
Route::post('deletePostReport', [PostController::class, 'deletePostReport'])->middleware(['checkLogin'])->name('deletePostReport');
Route::post('deletePost', [PostController::class, 'deletePost'])->middleware(['checkLogin'])->name('deletePost');
Route::post('allPostsList', [PostController::class, 'allPostsList'])->middleware(['checkLogin'])->name('allPostsList');
Route::post('updatePostRestrictionStatus', [PostController::class, 'updatePostRestrictionStatus'])->middleware(['checkLogin'])->name('updatePostRestrictionStatus');
Route::get('fetchPostComment', [PostController::class, 'fetchPostComment'])->middleware(['checkLogin'])->name('fetchPostComment');
Route::post('deleteCommentFromAdmin', [PostController::class, 'deleteCommentFromAdmin'])->middleware(['checkLogin'])->name('deleteCommentFromAdmin');

Route::get('viewReels', [ReelController::class, 'viewReels'])->middleware(['checkLogin'])->name('viewReels');
Route::post('reelList', [ReelController::class, 'reelList'])->middleware(['checkLogin'])->name('reelList');
Route::get('fetchCommentsInReelModal', [ReelController::class, 'fetchCommentsInReelModal'])->name('fetchCommentsInReelModal');
Route::post('deleteReelCommentFromAdmin', [ReelController::class, 'deleteReelCommentFromAdmin'])->middleware(['checkLogin'])->name('deleteReelCommentFromAdmin');
Route::post('deleteReelByAdmin', [ReelController::class, 'deleteReelByAdmin'])->middleware(['checkLogin'])->name('deleteReelByAdmin');
Route::post('userReelList', [ReelController::class, 'userReelList'])->middleware(['checkLogin'])->name('userReelList');
Route::post('reelReportList', [ReelController::class, 'reelReportList'])->middleware(['checkLogin'])->name('reelReportList');
Route::post('deleteReelReport', [ReelController::class, 'deleteReelReport'])->middleware(['checkLogin'])->name('deleteReelReport');
Route::post('deleteReelFromReport', [ReelController::class, 'deleteReelFromReport'])->middleware(['checkLogin'])->name('deleteReelFromReport');

Route::get('musics', [ReelController::class, 'musics'])->middleware(['checkLogin'])->name('musics');
Route::post('categoryList', [ReelController::class, 'categoryList'])->middleware(['checkLogin'])->name('categoryList');
Route::post('addCategory', [ReelController::class, 'addCategory'])->middleware(['checkLogin'])->name('addCategory');
Route::post('updateCategory', [ReelController::class, 'updateCategory'])->middleware(['checkLogin'])->name('updateCategory');
Route::post('deleteCategory', [ReelController::class, 'deleteCategory'])->middleware(['checkLogin'])->name('deleteCategory');
Route::post('musicList', [ReelController::class, 'musicList'])->middleware(['checkLogin'])->name('musicList');
Route::post('addMusic', [ReelController::class, 'addMusic'])->middleware(['checkLogin'])->name('addMusic');
Route::post('updateMusic', [ReelController::class, 'updateMusic'])->middleware(['checkLogin'])->name('updateMusic');
Route::post('deleteMusic', [ReelController::class, 'deleteMusic'])->middleware(['checkLogin'])->name('deleteMusic');

Route::post('userReportList', [UserController::class, 'userReportList'])->middleware(['checkLogin'])->name('userReportList');
Route::post('deleteUserReport', [UserController::class, 'deleteUserReport'])->middleware(['checkLogin'])->name('deleteUserReport');
Route::post('blockUserFromReport', [UserController::class, 'blockUserFromReport'])->middleware(['checkLogin'])->name('blockUserFromReport');
Route::get('users', [UserController::class, 'users'])->middleware(['checkLogin'])->name('users');
Route::post('userListWeb', [UserController::class, 'userListWeb'])->middleware(['checkLogin'])->name('userListWeb');
Route::post('verifiedUserList', [UserController::class, 'verifiedUserList'])->middleware(['checkLogin'])->name('verifiedUserList');
Route::post('verifiedUserBySubscriptionList', [UserController::class, 'verifiedUserBySubscriptionList'])->middleware(['checkLogin'])->name('verifiedUserBySubscriptionList');
Route::post('blockUserByAdmin/{id}', [UserController::class, 'blockUserByAdmin'])->middleware(['checkLogin'])->name('blockUserByAdmin');
Route::post('unblockUserByAdmin/{id}', [UserController::class, 'unblockUserByAdmin'])->middleware(['checkLogin'])->name('unblockUserByAdmin');
Route::get('usersDetail/{id}', [UserController::class, 'usersDetail'])->middleware(['checkLogin'])->name('usersDetail');
Route::post('verifyUser', [UserController::class, 'verifyUser'])->middleware(['checkLogin'])->name('verifyUser');
Route::post('userPostsList', [UserController::class, 'userPostsList'])->middleware(['checkLogin'])->name('userPostsList');
Route::post('deletePostFromUserPostTable', [UserController::class, 'deletePostFromUserPostTable'])->middleware(['checkLogin'])->name('deletePostFromUserPostTable');
Route::post('editProfileFormWeb', [UserController::class, 'editProfileFormWeb'])->middleware(['checkLogin'])->name('editProfileFormWeb');
Route::post('updateModeratorStatus', [UserController::class, 'updateModeratorStatus'])->middleware(['checkLogin'])->name('updateModeratorStatus');
Route::post('deleteAvatarFromUserDetail', [UserController::class, 'deleteAvatarFromUserDetail'])->middleware(['checkLogin'])->name('deleteAvatarFromUserDetail');
Route::post('deleteProfileFromUserDetail', [UserController::class, 'deleteProfileFromUserDetail'])->middleware(['checkLogin'])->name('deleteProfileFromUserDetail');
Route::post('moderatorsList', [UserController::class, 'moderatorsList'])->middleware(['checkLogin'])->name('moderatorsList');

Route::get('faqs', [FAQsController::class, 'faqs'])->middleware(['checkLogin'])->name('faqs');
Route::post('faqsList', [FAQsController::class, 'faqsList'])->middleware(['checkLogin'])->name('faqsList');
Route::post('addFAQsType', [FAQsController::class, 'addFAQsType'])->middleware(['checkLogin'])->name('addFAQsType');
Route::post('updateFAQsType', [FAQsController::class, 'updateFAQsType'])->middleware(['checkLogin'])->name('updateFAQsType');
Route::post('deleteFAQsType', [FAQsController::class, 'deleteFAQsType'])->middleware(['checkLogin'])->name('deleteFAQsType');
Route::post('faqsTypeList', [FAQsController::class, 'faqsTypeList'])->middleware(['checkLogin'])->name('faqsTypeList');
Route::post('addFAQs', [FAQsController::class, 'addFAQs'])->middleware(['checkLogin'])->name('addFAQs');
Route::post('updateFAQs', [FAQsController::class, 'updateFAQs'])->middleware(['checkLogin'])->name('updateFAQs');
Route::post('deleteFAQs', [FAQsController::class, 'deleteFAQs'])->middleware(['checkLogin'])->name('deleteFAQs');

Route::get('viewStories', [PostController::class, 'viewStories'])->middleware(['checkLogin'])->name('viewStories');
Route::post('userStoryList', [PostController::class, 'userStoryList'])->middleware(['checkLogin'])->name('userStoryList');
Route::post('allStoriesList', [PostController::class, 'allStoriesList'])->middleware(['checkLogin'])->name('allStoriesList');
Route::post('deleteStoryFromAdmin', [PostController::class, 'deleteStoryFromAdmin'])->middleware(['checkLogin'])->name('deleteStoryFromAdmin');

Route::get('restrictions', [RestrictionController::class, 'restrictions'])->middleware(['checkLogin'])->name('restrictions');
Route::post('usernameRestrictionsList', [RestrictionController::class, 'usernameRestrictionsList'])->middleware(['checkLogin'])->name('usernameRestrictionsList');
Route::post('addUsernameRestrict', [RestrictionController::class, 'addUsernameRestrict'])->middleware(['checkLogin'])->name('addUsernameRestrict');
Route::post('updateUsernameRestrict', [RestrictionController::class, 'updateUsernameRestrict'])->middleware(['checkLogin'])->name('updateUsernameRestrict');
Route::post('deleteUsernameRestrictions', [RestrictionController::class, 'deleteUsernameRestrictions'])->middleware(['checkLogin'])->name('deleteUsernameRestrictions');



Route::get('admob', [SettingsController::class, 'admob'])->middleware(['checkLogin'])->name('admob');
Route::post('admobAndroid', [SettingsController::class, 'admobAndroid'])->middleware(['checkLogin'])->name('admobAndroid');
Route::post('admobiOS', [SettingsController::class, 'admobiOS'])->middleware(['checkLogin'])->name('admobAndroid');

Route::post('addFakeData', [SettingsController::class, 'addFakeData'])->middleware(['checkLogin'])->name('addFakeData');