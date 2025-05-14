------------------------------------
# Date: 06 May 2025

## Summary
- Delete room function improved in admin panel.
- Bug fixes and improvements

#### Updated Files 
- [PostController.php](app/Http/Controllers/PostController.php)
- [RoomController.php](app/Http/Controllers/RoomController.php)
- [SettingsController.php](app/Http/Controllers/SettingsController.php)
- [UserController.php](app/Http/Controllers/UserController.php)

- [style.css](public/asset/css/style.css)

- [env.js](public/asset/script/env.js)
- [setting.js](public/asset/script/setting.js)

- [en.json](resources/lang/en.json)

- [setting.blade.php](resources/views/setting.blade.php)
- [app.blade.php](resources/views/include/app.blade.php)

#### Added Files
- [firebase-init.js](public/asset/script/firebase-init.js)

#### Deleted Files
None

#### Database
- settings : Fields Added : is_in_app_purchase_enabled, duration_limit_in_reel
 
------------------------------------
# Date: 28 March 2025

## Summary

- Add Reel Section
- Search With Interests (Posts, Reels, Users)
- Interest attach with posts and reels
- Code Optimized
- Bug Fixed

#### Updated Files

- [text](app/Http/Controllers/PostController.php)
- [text](app/Http/Controllers/RoomController.php)
- [text](app/Http/Controllers/SettingsController.php)
- [text](app/Http/Controllers/UserController.php)

- [text](app/Models/Constants.php)
- [text](app/Models/Like.php)
- [text](app/Models/Post.php)
- [text](app/Models/Report.php)
- [text](app/Models/SavedNotification.php)
- [text](app/Models/User.php)

- [text](public/asset/css/style.css)

- [text](public/asset/script/app.js)
- [text](public/asset/script/env.js)
- [text](public/asset/script/index.js)
- [text](public/asset/script/report.js)
- [text](public/asset/script/setting.js)
- [text](public/asset/script/user.js)

- [text](resources/lang/en.json)

- [text](resources/views/include/app.blade.php)
- [text](resources/views/index.blade.php)
- [text](resources/views/reports.blade.php)
- [text](resources/views/setting.blade.php)
- [text](resources/views/userDetails.blade.php)

- [text](routes/api.php)
- [text](routes/web.php)

#### Added Files

- [text](app/Http/Controllers/ReelController.php)

- [text](app/Models/Music.php)
- [text](app/Models/MusicCategory.php)
- [text](app/Models/Reel.php)
- [text](app/Models/ReelComment.php)

- [text](public/asset/script/music.js)
- [text](public/asset/script/reel.js)

- [text](resources/views/musics.blade.php)
- [text](resources/views/viewReels.blade.php)

#### Deleted Files
None

#### Database
- reels : New table added
- reel_comments : New table added
- musics : New table added
- music_categories : New table added

- posts : Fields Remove : content_type 

- posts : Fields Added : interest_ids 
- saved_notifications : Fields Added : reel_id, reel_comment_id
- likes : Fields Added : reel_id | post_id -> null (Change Default value) |
- users : Fields Added : saved_music_ids, saved_reel_ids
- reports : Fields Added : reel_id 

------------------------------------
# Date: 03 December 2024

## Summary
- Bug Fixes and improvement in Backend

#### Updated Files
- [GlobalFunction.php](app/Models/GlobalFunction.php)

#### Added Files
None

#### Deleted Files
None

#### Database
None

------------------------------------
# Date: 28 October 2024

## Summary

- Bug Fixes and improvement
- Comment will be shown in admin panel

#### Updated Files

<!-- First replace the composer.json file -->
<!-- Now open your terminal and paste the command line "Composer update" and then make all below changes  -->
- [composer.json](composer.json)

- [PostController.php](app/Http/Controllers/PostController.php)
- [RoomController.php](app/Http/Controllers/RoomController.php)
- [SettingsController.php](app/Http/Controllers/SettingsController.php)
- [UserController.php](app/Http/Controllers/UserController.php)

- [GlobalFunction.php](app/Models/GlobalFunction.php)

- [app.js](public/asset/script/app.js)
- [env.js](public/asset/script/env.js)
- [notification.js](public/asset/script/notification.js)
- [report.js](public/asset/script/report.js)
- [user.js](public/asset/script/user.js)

- [en.json](resources/lang/en.json)

- [app.blade.php](resources/views/include/app.blade.php)

- [admob.blade.php](resources/views/admob.blade.php)
- [faqs.blade.php](resources/views/faqs.blade.php)
- [interest.blade.php](resources/views/interest.blade.php)
- [login.blade.php](resources/views/login.blade.php)
- [notification.blade.php](resources/views/notification.blade.php)
- [reports.blade.php](resources/views/reports.blade.php)
- [restrictions.blade.php](resources/views/restrictions.blade.php)
- [setting.blade.php](resources/views/setting.blade.php)
- [userDetails.blade.php](resources/views/userDetails.blade.php)

- [web.php](routes/web.php)

#### Added Files


#### Deleted Files
None

#### Database
- saved_notifications : fields added : comment_id 
 

--------------------------------------------------
------------------------------------
# Date: 18 August 2024

## Summary
- Add Storage (AWS , Digital Ocean)
- Audio Post
- Username Restriction
- Dummy Data : Add Fake Data (Admin Panel)
- Add Moderator 
- SightEngine

#### Updated Files
.env

- [InterestController.php](app/Http/Controllers/InterestController.php) 
- [PostController.php](app/Http/Controllers/PostController.php) 
- [ProfileVerificationController.php](app/Http/Controllers/ProfileVerificationController.php) 
- [RoomController.php](app/Http/Controllers/RoomController.php) 
- [SettingsController.php](app/Http/Controllers/SettingsController.php) 
- [UserController.php](app/Http/Controllers/UserController.php)

- [Comment.php](app/Models/Comment.php) 
- [Constants.php](app/Models/Constants.php) 
- [GlobalFunction.php](app/Models/GlobalFunction.php)

- [filesystems.php](config/filesystems.php)

- [style.css](public/asset/css/style.css)

- [admob.js](public/asset/script/admob.js) 
- [app.js](public/asset/script/app.js) 
- [env.js](public/asset/script/env.js) 
- [index.js](public/asset/script/index.js) 
- [setting.js](public/asset/script/setting.js) 
- [user.js](public/asset/script/user.js)

- [en.json](resources/lang/en.json)

- [app.blade.php](resources/views/include/app.blade.php)

- [admob.blade.php](resources/views/admob.blade.php) 
- [roomDetails.blade.php](resources/views/roomDetails.blade.php) 
- [rooms.blade.php](resources/views/rooms.blade.php) 
- [setting.blade.php](resources/views/setting.blade.php) 
- [userDetails.blade.php](resources/views/userDetails.blade.php) 
- [users.blade.php](resources/views/users.blade.php) 
- [viewPosts.blade.php](resources/views/viewPosts.blade.php) 
- [viewStories.blade.php](resources/views/viewStories.blade.php)

- [api.php](routes\api.php)
- [web.php](routes\web.php)


#### Added Files
- [RestrictionController.php](app/Http/Controllers/RestrictionController.php)

- [LikeComment.php](app/Models/LikeComment.php) 
- [UsernameRestriction.php](app/Models/UsernameRestriction.php)

- [restrictions.js](public/asset/script/restrictions.js)

- [restrictions.blade.php](resources/views/restrictions.blade.php)


#### Deleted Files
None

#### Database

- like_comments : Table Added
- username_restrictions : Table added 

- comments : fields added : desc (varchar to Text), 
- posts : fields added : link_preview_json, is_restricted, content_type
- post_contents : fields added : audio_waves
- stories : fields added : thumbnail
- settings : fields added : minute_limit_in_audio_post, is_sight_engine_enabled, sight_engine_api_user, sight_engine_api_secret, 
                            sight_engine_image_workflow_id, sight_engine_video_workflow_id, storage_type, fetch_post_type, support_email
- users : fields added : is_moderator

--------------------------------------------------

## Date: 03/07/2024 ##

--> Updated Files <--
.env
app\Http\Controllers\RoomController.php (acceptInvitation Notification Changes)

app\Http\Controllers\PostController.php
app\Http\Controllers\ProfileVerificationController.php
app\Http\Controllers\RoomController.php
app\Http\Controllers\SettingsController.php
app\Http\Controllers\UserController.php


public\asset\css\style.css

public\asset\script\setting.js

resources\lang\en.json

resources\views\setting.blade.php
resources\views\userDetails.blade.php

routes\api.php
routes\web.php

--> Database <--

- settings : fields added : audio_space_hosts_limit, audio_space_listeners_limit, audio_space_duration_in_minutes


## Date: 26/04/2024 ##

--> Updated Files <--
.env
app\Http\Controllers\AdminController.php
app\Http\Controllers\PostController.php
app\Http\Controllers\ProfileVerificationController.php
app\Http\Controllers\RoomController.php
app\Http\Controllers\SettingsController.php
app\Http\Controllers\UserController.php

app\Models\Constants.php
app\Models\GlobalFunction.php

public\asset\css\style.css
public\asset\image\verified.svg

public\asset\script\env.js
public\asset\script\notification.js
public\asset\script\setting.js
public\asset\script\user.js

resources\lang\en.json

resources\views\index.blade.php
resources\views\setting.blade.php
resources\views\users.blade.php
resources\views\include\app.blade.php

routes\api.php
routes\web.php


--> Added Files <--
public\asset\script\admob.js
app\Models\Users.php

public\asset\script\verificationRequests.js

resources\views\admob.blade.php
resources\views\verificationRequests.blade.php


--> Remove Files <--
public\asset\script\profileVerification.js
resources\views\profileVerification.blade.php


--> Database <--
- room_users : fields added : is_mute
- settings : fields added : minute_limit_in_creating_story, minute_limit_in_choosing_video_for_story, minute_limit_in_choosing_video_for_post,              
max_images_can_be_uploaded_in_one_post, ad_banner_android, ad_interstitial_android, ad_banner_iOS, ad_interstitial_iOS, is_admob_on