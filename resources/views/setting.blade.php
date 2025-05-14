@extends('include.app')
@section('header')
<script src="{{ asset('asset/script/setting.js') }}"></script>
<script>
   document.addEventListener('DOMContentLoaded', function() {

      const awsConfig = @json($awsConfig);
      const doConfig = @json($doConfig);

      const isAwsConfigComplete = Object.values(awsConfig).every(value => value !== null && value !== '');
      const awsRadio = document.getElementById('awsRadio');
      const awsNote = document.getElementById('awsNote');
      if (!isAwsConfigComplete) {
         awsRadio.disabled = true;
         $('#awsNote').removeClass('d-none');
      } else {
         $('#localRadio').attr('checked');
         $('#awsNote').addClass('d-none');
      }

      const isDoConfigComplete = Object.values(doConfig).every(value => value !== null && value !== '');
      const doRadio = document.getElementById('doRadio');
      if (!isDoConfigComplete) {
         doRadio.disabled = true;
         $('#doNote').removeClass('d-none');
      } else {
         $('#localRadio').attr('checked');
         $('#doNote').addClass('d-none');
      }
   });

   document.addEventListener("DOMContentLoaded", function() {
      const radioButtons = document.querySelectorAll(
         'input[name="fetch_post_type"]'
      );

      radioButtons.forEach((radio) => {
         radio.addEventListener("change", function() {
            // Remove the active class from all labels
            radioButtons.forEach((rb) =>
               rb.parentElement.classList.remove("active")
            );

            // Add the active class to the selected radio button's label
            if (radio.checked) {
               radio.parentElement.classList.add("active");
            }
         });
      });

      // Trigger change event on page load to set the initial state
      document
         .querySelector('input[name="fetch_post_type"]:checked')
         .parentElement.classList.add("active");
   });
</script>
@endsection
@section('content')
<div class="mb-3 card-tab">
   <ul class="nav nav-tabs" id="myTab" role="tablist">
      <li class="nav-item " role="presentation">
         <button class="nav-link active" id="setting-Tab" data-bs-toggle="tab" data-bs-target="#setting-pane" type="button" role="tab" aria-controls="setting-panel" aria-selected="true"> {{ __('setting')}} </button>
      </li>
      <li class="nav-item" role="presentation">
         <button class="nav-link" id="limit-tab" data-bs-toggle="tab" data-bs-target="#limit-pane" type="button" role="tab" aria-controls="limit-pane" aria-selected="false"> {{ __('limits')}} </button>
      </li>
      <li class="nav-item" role="presentation">
         <button class="nav-link " id="documentType-Tab" data-bs-toggle="tab" data-bs-target="#documentType-pane" type="button" role="tab" aria-controls="documentType-panel" aria-selected="true"> {{ __('documentType')}} </button>
      </li>
      <li class="nav-item" role="presentation">
         <button class="nav-link " id="reportReason-Tab" data-bs-toggle="tab" data-bs-target="#reportReason-pane" type="button" role="tab" aria-controls="reportReason-panel" aria-selected="true"> {{ __('reportReason')}} </button>
      </li>
      <li class="nav-item" role="presentation">
         <button class="nav-link " id="sightEngine-Tab" data-bs-toggle="tab" data-bs-target="#sightEngine-pane" type="button" role="tab" aria-controls="sightEngine-panel" aria-selected="true"> {{ __('sightEngine')}} </button>
      </li>
   </ul>
</div>
<div class="tab-content" id="myTabContent">
   <div class="tab-pane show active" id="setting-pane" role="tabpanel" tabindex="0">
      <div class="row">
         <div class="col-lg-6">
            <form id="settingForm" method="POST">
               <div class="card">
                  <div class="card-body">
                     <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                           <div class="form-group">
                              <label for="appName" class="form-label">{{ __('changeAppName') }}</label>
                              <input type="text" class="form-control" name="app_name" id="app_name" required="" value="{{ $setting->app_name }}">
                           </div>
                           <div class="form-group">
                              <label for="appName" class="form-label">{{ __('fetchPostTypeOnHomePage') }}</label>
                              <div class="form">
                                 <section class="plan mb-3">
                                    <label class="free-label four col">
                                       <input type="radio" name="fetch_post_type" id="random" value="0" {{ $setting->fetch_post_type == 0 ? 'checked' : '' }}>
                                       {{ __('random') }}
                                    </label>
                                    <label class="basic-label four col">
                                       <input type="radio" name="fetch_post_type" id="latest" value="1" {{ $setting->fetch_post_type == 1 ? 'checked' : '' }}>
                                       {{ __('latest') }}
                                    </label>
                                 </section>
                              </div>
                           </div>
                           <div class="form-group">
                              <label for="support_email" class="form-label">{{ __('supportEmail') }}</label>
                              <input type="text" class="form-control" name="support_email" id="support_email" value="{{ $setting->support_email }}">
                           </div>
                           <div class="form-group">
                              <label for="favicon" class="form-label">{{ __('uploadFavicon') }}</label>
                              <div class="input-group">
                                 <label class="input-group-btn">
                                    <span class="btn btn-primary">
                                       Choose File <input type="file" id="favicon" name="favicon" accept="image/*" style="display: none;" onchange="previewFavicon()">
                                    </span>
                                 </label>
                                 <input type="text" class="form-control d-none" readonly>
                                 <div class="ms-3" id="favicon-preview">
                                    <img id="faviconImage" src="" alt="Image Preview" class="img-fluid" style="display: none; width: 100px; height: 100px; object-fit:cover; border: 1px solid #ddd; padding: 5px;">
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                           <div class="mb-3">
                              <div class="page-title w-100">
                                 <div class="d-flex align-items-center justify-content-between">
                                    <h5 class="mb-0 fw-normal">{{ __('storageSetting') }}</h5>
                                 </div>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-12">
                                 <div class="card w-auto p-3">
                                    <div class="checkbox-slider d-flex align-items-center justify-content-between">
                                       <span class="me-3">
                                          {{ __('local') }}
                                       </span>
                                       <label class="switch m-0">
                                          <input type="radio" name="storage_type" value="0" id="localRadio" {{ $setting->storage_type == 0 ? 'checked' : '' }}>
                                          <span class="slider"></span>
                                       </label>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-12">
                                 <div class="card w-auto p-3">
                                    <div class="checkbox-slider d-flex align-items-center justify-content-between">
                                       <span class="me-3">
                                          {{ __('AWSS3') }}
                                          <br>
                                          <span id="awsNote" class="text-danger"> {{ __('pleaseAddValuesInEnvFile') }} </span>
                                       </span>
                                       <label class="switch m-0">
                                          <input type="radio" name="storage_type" value="1" id="awsRadio" {{ $setting->storage_type == 1 ? 'checked' : '' }}>
                                          <span class="slider"></span>
                                       </label>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-12">
                                 <div class="card w-auto p-3">
                                    <div class="checkbox-slider d-flex align-items-center justify-content-between">
                                       <span class="me-3">
                                          {{ __('digitalOceanSpace') }}
                                          <br>
                                          <span id="doNote" class="text-danger"> {{ __('pleaseAddValuesInEnvFile')}} </span>
                                       </span>
                                       <label class="switch m-0">
                                          <input type="radio" name="storage_type" value="2" id="doRadio" {{ $setting->storage_type == 2 ? 'checked' : '' }}>
                                          <span class="slider"></span>
                                       </label>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <hr>
                           <div class="mb-3">
                              <div class="row">
                                 <div class="col-12">
                                    <div class="card w-auto p-3">
                                       <div class="checkbox-slider d-flex align-items-center justify-content-between">
                                          <span class="me-3">
                                             {{ __('isInAppPurchaseEnabled') }}
                                          </span>
                                          <label class="switch m-0">
                                             <input type="hidden" name="is_in_app_purchase_enabled" id="is_in_app_purchase_enabled_hidden" value="{{ $setting->is_in_app_purchase_enabled }}">
                                             <input type="checkbox" id="is_in_app_purchase_enabled" {{ $setting->is_in_app_purchase_enabled == 1 ? 'checked' : '' }}>
                                             <span class="slider"></span>
                                          </label>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="modal-footer p-0">
                           <button type="button" class="btn"></button>
                           <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <div class="col-md-3">
            <div class="card">
               <div class="card-header">
                  <div class="page-title w-100">
                     <div class="d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 fw-normal">{{ __('changePassword') }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-body">
                  <form id="changePasswordForm" method="POST">
                     <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                           <div class="form-group">
                              <label for="appName" class="form-label">{{ __('oldPassword') }}</label>
                              <input type="password" class="form-control" name="user_password" id="userPassword" required="">
                              <div class="password-icon">
                                 <i data-feather="eye"></i>
                                 <i data-feather="eye-off"></i>
                              </div>
                           </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                           <div class="form-group">
                              <label for="appName" class="form-label">{{ __('newPassword') }}</label>
                              <input type="password" class="form-control" name="new_password" id="newPassword" required="">
                              <div class="password-icon">
                                 <i data-feather="eye" class="eye1"></i>
                                 <i data-feather="eye-off" class="eye-off1"></i>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="modal-footer p-0">
                        <button type="button" class="btn"></button>
                        <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="limit-pane" role="tabpanel" tabindex="0">
      <div class="row same-height-card">
         <div class="col-lg-4 col-md-12 col-sm-12">
            <div class="card">
               <div class="card-header">
                  <div class="page-title w-100">
                     <div class="d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 fw-normal">{{ __('setRoomUsersLimit') }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-body">
                  <form id="setRoomUsersLimit" method="POST">
                     <div class="form-group">
                        <label for="setRoomUsersLimitInput" class="form-label">{{ __('setRoomUsersLimit') }}</label>
                        <input type="number" class="form-control" name="setRoomUsersLimit" id="setRoomUsersLimitInput" required="" value="{{ $setting->setRoomUsersLimit }}">
                     </div>
                     <div class="modal-footer p-0">
                        <button type="button" class="btn"></button>
                        <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <div class="col-lg-4 col-md-12 col-sm-12">
            <div class="card">
               <div class="card-header">
                  <div class="page-title w-100">
                     <div class="d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 fw-normal">{{ __('limits') }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-body">
                  <form id="minuteLimitForm" method="POST">
                     <h6 class="fw-normal">{{ __('story') }}</h6>
                     <div class="row">
                        <div class="col-12">
                           <div class="w-auto ps-4 py-1 pe-1 mb-2 border rounded-5">
                              <div class=" d-flex align-items-center justify-content-between m-0">
                                 <label for="minuteLimitInStories" class="form-label m-0 text-nowrap">{{ __('createStoryDuration') }} <span class="text-dark opacity-25"> {{ __('minutes') }}</span></label>
                                 <input type="number" class="form-control ms-3" name="minute_limit_in_creating_story" id="minuteLimitInStories" required="" value="{{ $setting->minute_limit_in_creating_story }}">
                              </div>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="w-auto ps-4 py-1 pe-1 mb-2 border rounded-5">
                              <div class=" d-flex align-items-center justify-content-between m-0">
                                 <label for="minuteLimitInChoosingVideoForStory" class="form-label m-0 text-nowrap">{{ __('videoLimitforStories') }} <span class="text-dark opacity-25"> {{ __('minutes') }} </span></label>
                                 <input type="number" class="form-control ms-3" name="minute_limit_in_choosing_video_for_story" id="minuteLimitInChoosingVideoForStory" required="" value="{{ $setting->minute_limit_in_choosing_video_for_story }}">
                              </div>
                           </div>
                        </div>
                     </div>
                     <hr>
                     <h6 class="fw-normal">{{ __('post') }}</h6>
                     <div class="row">
                        <div class="col-12">
                           <div class="w-auto ps-4 py-1 pe-1 mb-2 border rounded-5">
                              <div class=" d-flex align-items-center justify-content-between m-0">
                                 <label for="minuteLimitInChoosingVideoForPost" class="form-label m-0 text-nowrap">{{ __('videoLimitforPosts') }} <span class="text-dark opacity-25"> {{ __('minutes') }} </span></label>
                                 <input type="number" class="form-control ms-3" name="minute_limit_in_choosing_video_for_post" id="minuteLimitInChoosingVideoForPost" required="" value="{{ $setting->minute_limit_in_choosing_video_for_post }}">
                              </div>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="w-auto ps-4 py-1 pe-1 mb-2 border rounded-5">
                              <div class=" d-flex align-items-center justify-content-between m-0">
                                 <label for="uploadMaxPerPost" class="form-label m-0 text-nowrap">{{ __('maxImagesPerPost') }}</label>
                                 <input type="number" class="form-control ms-3" name="max_images_can_be_uploaded_in_one_post" id="uploadMaxPerPost" required="" value="{{ $setting->max_images_can_be_uploaded_in_one_post }}">
                              </div>
                           </div>
                        </div>
                        <div class="col-12">
                           <div class="w-auto ps-4 py-1 pe-1 mb-2 border rounded-5">
                              <div class=" d-flex align-items-center justify-content-between m-0">
                                 <label for="minuteLimitInAudioPost" class="form-label m-0 text-nowrap">{{ __('audioLimitforPosts') }} <span class="text-dark opacity-25"> {{ __('minutes') }} </span></label>
                                 <input type="number" class="form-control ms-3" name="minute_limit_in_audio_post" id="minuteLimitInAudioPost" required="" value="{{ $setting->minute_limit_in_audio_post }}">
                              </div>
                           </div>
                        </div>
                     </div>
                     <hr>
                     <h6 class="fw-normal">{{ __('reels') }}</h6>
                     <div class="row">
                        <div class="col-12">
                           <div class="w-auto ps-4 py-1 pe-1 mb-2 border rounded-5">
                              <div class=" d-flex align-items-center justify-content-between m-0">
                                 <label for="durationLimitInReel" class="form-label m-0 text-nowrap">{{ __('durationLimitInReel') }} <span class="text-dark opacity-25"> {{ __('second') }} </span></label>
                                 <input type="number" class="form-control ms-3" name="duration_limit_in_reel" id="durationLimitInReel" required="" value="{{ $setting->duration_limit_in_reel }}">
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="modal-footer p-0">
                        <button type="button" class="btn"></button>
                        <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
         <div class="col-lg-4 col-md-12 col-sm-12">
            <div class="card">
               <div class="card-header">
                  <div class="page-title w-100">
                     <div class="d-flex align-items-center justify-content-between">
                        <h4 class="mb-0 fw-normal">{{ __('audioSpaceLimits') }}</h4>
                     </div>
                  </div>
               </div>
               <div class="card-body">
                  <form id="audioLimitForm" method="POST">
                     <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                           <div class="form-group">
                              <label for="audio_space_hosts_limit" class="form-label">{{ __('audioSpaceHostsLimit') }} <span class="text-dark opacity-25"> {{ __('enter0ForNoLimit') }} </span></label>
                              <input type="number" class="form-control" name="audio_space_hosts_limit" id="audio_space_hosts_limit" required="" value="{{ $setting->audio_space_hosts_limit }}">
                           </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                           <div class="form-group">
                              <label for="audio_space_listeners_limit" class="form-label">{{ __('audioSapceListenersLimit') }} <span class="text-dark opacity-25"> {{ __('enter0ForNoLimit') }} </span> </label>
                              <input type="number" class="form-control" name="audio_space_listeners_limit" id="audio_space_listeners_limit" required="" value="{{ $setting->audio_space_listeners_limit }}">
                           </div>
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12">
                           <div class="form-group">
                              <label for="audio_space_duration_in_minutes" class="form-label">{{ __('audioSpaceDurationInMinutes') }} <span class="text-dark opacity-25"> {{ __('enter0ForNoLimit') }} </span> </label>
                              <input type="number" class="form-control" name="audio_space_duration_in_minutes" id="audio_space_duration_in_minutes" required="" value="{{ $setting->audio_space_duration_in_minutes }}">
                           </div>
                        </div>
                     </div>
                     <div class="modal-footer p-0">
                        <button type="button" class="btn"></button>
                        <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="documentType-pane" role="tabpanel" tabindex="0">
      <div class="card">
         <div class="card-header">
            <div class="page-title w-100">
               <div class="d-flex align-items-center justify-content-between">
                  <h4 class="mb-0 fw-normal">{{ __('documentType') }}</h4>
                  <button type="button" class="btn theme-bg theme-btn text-white" data-bs-toggle="modal" data-bs-target="#documentTypeModal">
                     {{ __('addDocumentType') }}
                  </button>
               </div>
            </div>
         </div>
         <div class="card-body">
            <table class="table table-striped w-100" id="documentTypeTable">
               <thead>
                  <tr>
                     <th>{{ __('documentType') }}</th>
                     <th width="250px" style="text-align: right">{{ __('action') }}</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="reportReason-pane" role="tabpanel" tabindex="0">
      <div class="card">
         <div class="card-header">
            <div class="page-title w-100">
               <div class="d-flex align-items-center justify-content-between">
                  <h4 class="mb-0 fw-normal">{{ __('reportReason') }}</h4>
                  <button type="button" class="btn theme-bg theme-btn text-white" data-bs-toggle="modal" data-bs-target="#reportReasonModal">
                     {{ __('addReportReason') }}
                  </button>
               </div>
            </div>
         </div>
         <div class="card-body">
            <table class="table table-striped w-100" id="reportReasonTable">
               <thead>
                  <tr>
                     <th>{{ __('reportReason') }}</th>
                     <th width="250px" style="text-align: right">{{ __('action') }}</th>
                  </tr>
               </thead>
            </table>
         </div>
      </div>
   </div>
   <div class="tab-pane" id="sightEngine-pane" role="tabpanel" tabindex="0">
      <form id="sightEngineForm" method="POST">
         <div class="card">
            <div class="card-header">
               <div class="page-title w-100">
                  <div class="d-flex align-items-center justify-content-between">
                     <h4 class="mb-0 fw-normal">{{ __('contentModerationSightEngine') }}</h4>
                     <label class="switch m-0">
                        <input type="checkbox" name="is_sight_engine_enabled" id="is_sight_engine_enabled" value="{{ $setting->is_sight_engine_enabled }}" {{ $setting->is_sight_engine_enabled == 1 ? 'checked' : ''}}>
                        <span class="slider"></span>
                     </label>
                  </div>
                  <p class="m-0 text-muted fw-normal">If You keep this on and add all required credentials from SightEngine, App will refuse sensitive videos to be uploaded.</p>
               </div>
            </div>
            <div class="card-body">
               <div class="row" id="sight_engine_hide_show">
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="form-group">
                        <label for="sight_engine_api_user" class="form-label">{{ __('apiUser') }}</label>
                        <input type="text" class="form-control" name="sight_engine_api_user" id="sight_engine_api_user" required value="{{ $setting->sight_engine_api_user }}">
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="form-group">
                        <label for="sight_engine_api_secret" class="form-label">{{ __('apiSecret') }}</label>
                        <input type="text" class="form-control" name="sight_engine_api_secret" id="sight_engine_api_secret" required value="{{ $setting->sight_engine_api_secret }}">
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="form-group">
                        <label for="sight_engine_image_workflow_id" class="form-label">{{ __('imageWorkflowId') }}</label>
                        <input type="text" class="form-control" name="sight_engine_image_workflow_id" id="sight_engine_image_workflow_id" required value="{{ $setting->sight_engine_image_workflow_id }}">
                     </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6">
                     <div class="form-group">
                        <label for="sight_engine_video_workflow_id" class="form-label">{{ __('videoWorkflowId') }}</label>
                        <input type="text" class="form-control" name="sight_engine_video_workflow_id" id="sight_engine_video_workflow_id" required value="{{ $setting->sight_engine_video_workflow_id }}">
                     </div>
                  </div>
               </div>
               <div class="modal-footer p-0">
                  <button type="button" class="btn"></button>
                  <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
               </div>
            </div>
         </div>
      </form>
   </div>
</div>
<div class="">
   <button class="btn btn-danger addFakeData">
      {{ __('addFakeData')}}
   </button>
</div>
<!-- Document Type Modal -->
<div class="modal fade" id="documentTypeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('addDocumentType') }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="addDocumentTypeForm" method="post">
            <div class="modal-body">
               <div class="form-group">
                  <label for="documentType" class="form-label">{{ __('title') }}</label>
                  <input type="text" class="form-control" name="title" id="documentType" required="">
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
               <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- Edit Document Type Modal -->
<div class="modal fade" id="editDocumentTypeModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('editDocumentType') }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="editDocumentTypeForm" method="post">
            <input type="hidden" name="" id="documentTypeId">
            <div class="modal-body">
               <div class="form-group">
                  <label for="editDocumentType" class="form-label">{{ __('title') }}</label>
                  <input type="text" class="form-control" name="title" id="editDocumentType" required="">
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
               <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- Report Reason Modal -->
<div class="modal fade" id="reportReasonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('addReportReason') }}</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="reportReasonForm" method="post">
            <div class="modal-body">
               <div class="form-group">
                  <label for="reportReason" class="form-label">{{ __('title') }}</label>
                  <input type="text" class="form-control" name="title" id="reportReason" required="">
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
               <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
            </div>
         </form>
      </div>
   </div>
</div>
<!-- Edit Report Reason Modal -->
<div class="modal fade" id="editReportReasonModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h1 class="modal-title fs-5 fw-normal" id="exampleModalLabel">{{ __('editReportReason') }} </h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <form id="editReportReasonForm" method="post">
            <input type="hidden" name="" id="reportReasonId">
            <div class="modal-body">
               <div class="form-group">
                  <label for="editReportReason" class="form-label">{{ __('title') }}</label>
                  <input type="text" class="form-control" name="title" id="editReportReason" required="">
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('close') }}</button>
               <button type="submit" class="btn theme-btn text-white">{{ __('save') }}</button>
            </div>
         </form>
      </div>
   </div>
</div>

<script>
   function previewFavicon() {
      const input = document.getElementById('favicon');
      const textInput = input.closest('.input-group').querySelector('input[type="text"]');
      const preview = document.getElementById('faviconImage');

      if (input.files && input.files[0]) {
         const reader = new FileReader();

         textInput.value = input.files[0].name; // Show file name in the input field

         reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
         };

         reader.readAsDataURL(input.files[0]);
      } else {
         textInput.value = '';
         preview.src = '';
         preview.style.display = 'none';
      }
   }

   function toggleSightEngineFields() {
      const isEnabled = document.getElementById('is_sight_engine_enabled').checked;
      if (isEnabled) {
         $('#sight_engine_hide_show').slideDown();
      } else {
         $('#sight_engine_hide_show').slideUp();
      }
   }

   document.getElementById('is_sight_engine_enabled').addEventListener('change', function() {
      var isEnabled = this.checked ? 1 : 0;
      this.value = isEnabled;
      toggleSightEngineFields();
   });

   // Initialize the visibility of the fields on page load
   document.addEventListener('DOMContentLoaded', function() {
      toggleSightEngineFields();
   });
</script>
@endsection