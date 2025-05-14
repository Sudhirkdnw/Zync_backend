$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".notificationSideA").addClass("activeLi");

  $("#NotificationTable").dataTable({
    processing: true,
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
      url: `${domainUrl}adminNotificationList`,
      data: function (data) {},
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#addNotificationForm").on("submit", function (event) {
    event.preventDefault();
    if (user_type == 1) {
      var formdata = new FormData($("#addNotificationForm")[0]);
      console.log(formdata);

      $.ajax({
        url: `${domainUrl}sendNotification`,
        type: "POST",
        data: formdata,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
          console.log(response);
          $("#notificationModal").modal("hide");
          $(".loader").hide();
          $("#addNotificationForm")[0].reset();
          $("#NotificationTable").DataTable().ajax.reload(null, false);
          if (response.status == false) {
            iziToast.show({
              title: "Error!",
              message: response.message,
              color: "red",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
          } else {
            iziToast.show({
              title: "Success!",
              message: response.message,
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
          }
        },
        error: function (err) {
          console.log(err);
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
      // iziToast.error({
      //     title: app.Error,
      //     message: app.tester,
      //     position: toastPosition,
      //     transitionIn: "fadeInUp",
      //     transitionOut: "fadeOutDown",
      // });
    }
  });

  $("#NotificationTable").on("click", ".edit", function (e) {
    e.preventDefault();

    var id = $(this).attr("rel");
    var title = $(this).data("title");
    var description = $(this).data("description");

    $("#notificationID").val(id);
    $("#editNotificationTitle").val(title);
    $("#editNotificationDesc").val(description);

    $("#editNotificationModal").modal("show");
  });

  $(document).on("submit", "#editNotificationForm", function (e) {
    e.preventDefault();
    var id = $("#notificationID").val();
    // console.log(id);
    if (user_type == 1) {
      let EditformData = new FormData($("#editNotificationForm")[0]);
      EditformData.append("notificationID", id);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateNotification`,
        data: EditformData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Same as Previous",
              message: "Notification Not Update",
              color: "red",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/copy.svg`,
            });
          } else if (response.status == true) {
            iziToast.show({
              title: "Updated",
              message: "Notification Update Succesfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#NotificationTable").DataTable().ajax.reload(null, false);
            $("#editNotificationModal").modal("hide");
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

  $("#NotificationTable").on("click", ".delete", function (e) {
    e.preventDefault();
    var id = $(this).attr("rel");
    console.log(id);
    if (user_type == 1) {
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
              url: `${domainUrl}deleteNotification`,
              dataType: "json",
              data: {
                notification_id: id,
              },
              success: function (response) {
                if (response.status == false) {
                  console.log(response.message);
                } else if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "Notification Delete Succesfully",
                    color: "green",
                    position: toastPosition,
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#NotificationTable").DataTable().ajax.reload(null, false);
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

  $("#NotificationTable").on("click", ".repeat", function (e) {
    e.preventDefault();
    var id = $(this).attr("rel");
    var title = $(this).data("title");
    var description = $(this).data("description");

    var button = $(this);
    button.addClass("disabled");

    if (user_type == 1) {
      let editformData = new FormData();
      editformData.append("title", title);
      editformData.append("description", description);
      for (var pair of editformData.entries()) {
        console.log(pair[0] + ", " + pair[1]);
      }
      $.ajax({
        type: "POST",
        url: `${domainUrl}repeatNotification`,
        data: editformData,
        contentType: false,
        processData: false,
        success: function (response) {
          console.log(response);
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Same as Previous",
              message: response.message,
              color: "red",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/copy.svg`,
            });
          } else if (response.status == true) {
             button.removeClass("disabled");
            iziToast.show({
              title: "Success",
              message: "Notification send successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            
          }
        },
      });
    } else {
      button.removeClass("disabled");
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
});
