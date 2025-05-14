$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".userSideA").addClass("activeLi");

  var id = $("#userId").val();
  
  $("#userTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: "_all",
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}userListWeb`,
      data: function (data) {},
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#verifiedUserTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}verifiedUserList`,
      data: {
        userId: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#verifiedUserBySubscriptionTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}verifiedUserBySubscriptionList`,
      data: {
        userId: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#moderatorsTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}moderatorsList`,
      data: function (data) {},
      error: (error) => {
        console.log(error);
      },
    },
  });

  $(document).on("click", ".blockUserBtn", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $(this).attr("rel");
      console.log(id);
      swal({
        title: "Are you sure?",
        icon: "error",
        buttons: true,
        dangerMode: true,
        buttons: ["Cancel", "Yes"],
      }).then((deleteValue) => {
        if (deleteValue) {
          if (deleteValue == true) {
            $.ajax({
              type: "POST",
              url: `${domainUrl}blockUserByAdmin/` + id,
              dataType: "json",
              success: function (response) {
                if (response.status == false) {
                  console.log(response.message);
                } else if (response.status == true) {
                  iziToast.show({
                    title: "Block",
                    message: "User is in blocklist",
                    color: "green",
                    position: "bottomCenter",
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#userTable").DataTable().ajax.reload(null, false);
                  $("#verifiedUserTable").DataTable().ajax.reload(null, false);
                  $("#moderatorsTable").DataTable().ajax.reload(null, false);
                  $("#verifiedUserBySubscription")
                    .DataTable()
                    .ajax.reload(null, false);
                  $("#reloadContent").load(
                    location.href + " #reloadContent>*",
                    ""
                  );
                  console.log(response.message);
                }
              },
            });
          }
        }
      });
    } else {
      iziToast.show({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: false,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $(document).on("click", ".unblockUserBtn", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $(this).attr("rel");
      console.log(id);
      swal({
        title: "Are you sure?",
        icon: "success",
        buttons: ["Cancel", "Yes"],
      }).then((deleteValue) => {
        if (deleteValue) {
          if (deleteValue == true) {
            $.ajax({
              type: "POST",
              url: `${domainUrl}unblockUserByAdmin/` + id,
              dataType: "json",
              success: function (response) {
                if (response.status == false) {
                  console.log(response.message);
                } else if (response.status == true) {
                  iziToast.show({
                    title: "Unblock",
                    message: "User is Unblock",
                    color: "green",
                    position: "bottomCenter",
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#userTable").DataTable().ajax.reload(null, false);
                  $("#verifiedUserTable").DataTable().ajax.reload(null, false);
                  $("#moderatorsTable").DataTable().ajax.reload(null, false);
                  $("#verifiedUserBySubscription")
                    .DataTable()
                    .ajax.reload(null, false);
                  $("#reloadContent").load(
                    location.href + " #reloadContent>*",
                    ""
                  );
                  console.log(response.message);
                }
              },
            });
          }
        }
      });
    } else {
      iziToast.show({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: false,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $(document).on("click", ".verifyUser", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $(this).attr("rel");
      swal({
        title: "Are you sure?",
        icon: "success",
        buttons: ["Cancel", "Yes"],
      }).then((deleteValue) => {
        if (deleteValue) {
          if (deleteValue == true) {
            $.ajax({
              type: "POST",
              url: `${domainUrl}verifyUser`,
              dataType: "json",
              data: {
                user_id: id,
              },
              success: function (response) {
                if (response.status == false) {
                  console.log(response.message);
                } else if (response.status == true) {
                  iziToast.show({
                    title: "Verified",
                    message: "User verified successfully",
                    color: "green",
                    position: "bottomCenter",
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  console.log(response.message);
                  $("#reloadContent").load(
                    location.href + " #reloadContent>*",
                    ""
                  );
                }
              },
            });
          }
        }
      });
    } else {
      iziToast.show({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: false,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#userPostTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}userPostsList`,
      data: {
        userId: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#userPostTable").on("click", ".deletePost", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $(this).attr("rel");
      swal({
        title: "Are you sure?",
        icon: "error",
        buttons: true,
        dangerMode: true,
        buttons: ["Cancel", "Yes"],
      }).then((deleteValue) => {
        if (deleteValue) {
          if (deleteValue == true) {
            $.ajax({
              type: "POST",
              url: `${domainUrl}deletePostFromUserPostTable`,
              dataType: "json",
              data: {
                post_id: id,
              },
              success: function (response) {
                if (response.status == false) {
                  console.log(response.message);
                } else if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "Post Delete Succesfully",
                    color: "green",
                    position: "bottomCenter",
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#userPostTable").DataTable().ajax.reload(null, false);
                  console.log(response.message);
                }
              },
            });
          }
        }
      });
    } else {
      iziToast.show({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: false,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $(document).on("submit", "#editProfileForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $("#user_id").val();
      let EditFormData = new FormData($("#editProfileForm")[0]);
      EditFormData.append("user_id", id);
      $.ajax({
        type: "POST",
        url: `${domainUrl}editProfileFormWeb`,
        data: EditFormData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Error",
              message: "Something went wrong",
              color: "red",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 1000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/x.svg`,
            });
          } else if (response.status == true) {
            iziToast.show({
              title: "Updated",
              message: "User Details Update Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 1000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            setTimeout(function () {
              window.location.reload();
            }, 1000);
            $("#userDetailReload").load(
              location.href + " #userDetailReload>*",
              ""
            );
            $("#reloadContent").load(location.href + " #reloadContent>*", "");
          }
        },
      });
    } else {
      iziToast.show({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: false,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#userRoomsOwnTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [0, 1],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}userRoomsOwnTable`,
      data: {
        user_id: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#userRoomInTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [0, 1],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}userRoomInTable`,
      data: {
        user_id: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#userStoryTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [0, 1],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}userStoryList`,
      data: {
        user_id: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#userStoryTable").on("click", ".deleteStory", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $(this).attr("rel");
      swal({
        title: "Are you sure?",
        icon: "error",
        buttons: true,
        dangerMode: true,
        buttons: ["Cancel", "Yes"],
      }).then((deleteValue) => {
        if (deleteValue) {
          if (deleteValue == true) {
            $.ajax({
              type: "POST",
              url: `${domainUrl}deleteStoryFromAdmin`,
              dataType: "json",
              data: {
                story_id: id,
              },
              success: function (response) {
                if (response.status == false) {
                  console.log(response.message);
                } else if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "Story Delete Succesfully",
                    color: "green",
                    position: "bottomCenter",
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#userStoryTable").DataTable().ajax.reload(null, false);
                }
              },
            });
          }
        }
      });
    } else {
      iziToast.show({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: false,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $(document).on("click", ".avatar-delete", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $(this).attr("rel");
      swal({
        title: "Are you sure?",
        icon: "error",
        buttons: true,
        dangerMode: true,
        buttons: ["Cancel", "Yes"],
      }).then((deleteValue) => {
        if (deleteValue) {
          if (deleteValue == true) {
            $.ajax({
              type: "POST",
              url: `${domainUrl}deleteAvatarFromUserDetail`,
              dataType: "json",
              data: {
                user_id: id,
              },
              success: function (response) {
                if (response.status == false) {
                  console.log(response.message);
                } else if (response.status == true) {
                  // iziToast.show({
                  //   title: "Deleted",
                  //   message: "Avatar Delete Succesfully",
                  //   color: "green",
                  //   position: "bottomCenter",
                  //   transitionIn: "fadeInUp",
                  //   transitionOut: "fadeOutDown",
                  //   timeout: 3000,
                  //   animateInside: false,
                  //   iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  // });
                  // $("#reloadContent").load(location.href + " #reloadContent>*");
                  location.reload();
                }
              },
            });
          }
        }
      });
    } else {
      iziToast.show({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: false,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $(document).on("click", ".profile-delete", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $(this).attr("rel");
      swal({
        title: "Are you sure?",
        icon: "error",
        buttons: true,
        dangerMode: true,
        buttons: ["Cancel", "Yes"],
      }).then((deleteValue) => {
        if (deleteValue) {
          if (deleteValue == true) {
            $.ajax({
              type: "POST",
              url: `${domainUrl}deleteProfileFromUserDetail`,
              dataType: "json",
              data: {
                user_id: id,
              },
              success: function (response) {
                if (response.status == false) {
                  console.log(response.message);
                } else if (response.status == true) {
                  // iziToast.show({
                  //   title: "Deleted",
                  //   message: "Avatar Delete Succesfully",
                  //   color: "green",
                  //   position: "bottomCenter",
                  //   transitionIn: "fadeInUp",
                  //   transitionOut: "fadeOutDown",
                  //   timeout: 3000,
                  //   animateInside: false,
                  //   iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  // });
                  // $("#reloadContent").load(location.href + " #reloadContent>*");
                  location.reload();
                }
              },
            });
          }
        }
      });
    } else {
      iziToast.show({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: false,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#userReelTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}userReelList`,
      data: {
        userId: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });

});
