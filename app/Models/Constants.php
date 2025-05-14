<?php

namespace App\Models;

use Illuminate\Validation\Rules\Enum;

final class Constants
{

    const FAQsType = 0;
    const isDeletedFAQsType = 1;

    const invitedForRoom = 4;

    const pushNotification = 1;
    
    const is_verified = 2;
    const is_subscribe_verified = 3;
    
    const moderator_false = 0;
    const moderator = 1;

    const unblocked = 0;
    const blocked = 1;

    const RandomPosts = 0;
    const LatestPosts = 1;
    
    const DeletedNo = 0;
    const Deleted = 1;

    const notificationTypeFollow = 1;
    const notificationTypeComment = 2;
    const notificationTypePostLike = 3;
    const notificationTypeInviteRoom = 4;
    const notificationTypeAcceptInvitationRoom = 5;
    const notificationTypejoinRoom = 6;
    const notificationTypeDirectjoinRoom = 7;
    const notificationTypeAcceptRoomRequest = 8;
    const notificationTypeReelLike = 9;
    const notificationTypeAddReelComment = 10;
    
    const followingReel = 0;
    const forYouReel = 1;

    const android = 0;
    const iOS = 1;

}
