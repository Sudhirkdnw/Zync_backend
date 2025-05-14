$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".settingSideA").addClass("activeLi");

  const eye = document.querySelector(".feather-eye");
  const eyeoff = document.querySelector(".feather-eye-off");
  const passwordField = document.querySelector("input[type=password]");

  eye.addEventListener("click", () => {
    eye.style.display = "none";
    eyeoff.style.display = "block";
    passwordField.type = "text";
  });

  eyeoff.addEventListener("click", () => {
    eyeoff.style.display = "none";
    eye.style.display = "block";
    passwordField.type = "password";
  });

  const eye1 = document.querySelector(".eye1");
  const eyeoff1 = document.querySelector(".eye-off1");
  const passwordField1 = document.querySelector(
    "input#newPassword[type=password]"
  );

  eye1.addEventListener("click", () => {
    eye1.style.display = "none";
    eyeoff1.style.display = "block";
    passwordField1.type = "text";
  });

  eyeoff1.addEventListener("click", () => {
    eyeoff1.style.display = "none";
    eye1.style.display = "block";
    passwordField1.type = "password";
  });

  $("#documentTypeTable").dataTable({
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
      url: `${domainUrl}documentTypeList`,
      data: function (data) {},
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#is_in_app_purchase_enabled").on("change", function () {
    let isInAppPurchaseEnabled = $(this).is(":checked") ? 1 : 0;
    $(this).val(isInAppPurchaseEnabled);
    $("#is_in_app_purchase_enabled_hidden").val(isInAppPurchaseEnabled);
  });

  $(document).on("submit", "#settingForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#settingForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateSettings`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == true) {
            window.location.reload();
          } else if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Error",
              message: "Setting Not Found",
              color: "red",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/copy.svg`,
            });
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

  $(document).on("submit", "#setRoomUsersLimit", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#setRoomUsersLimit")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateSettings`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Error",
              message: "Setting Not Found",
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
              title: "Success",
              message: "Room users limit set successfully",
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

  $(document).on("submit", "#changePasswordForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#changePasswordForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}changePassword`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Error",
              message: "Old Password does not match",
              color: "red",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/x.svg`,
            });
          } else if (response.status == true) {
            iziToast.show({
              title: "Success",
              message: "New password set successfully",
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

  $(document).on("submit", "#minuteLimitForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#minuteLimitForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateSettings`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Error",
              message: "Setting Not Found",
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
              title: "Success",
              message: "Updated Successfully",
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

  $(document).on("submit", "#audioLimitForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#audioLimitForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateSettings`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Error",
              message: "Setting Not Found",
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
              title: "Success",
              message: "Updated Successfully",
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

  $(document).on("submit", "#sightEngineForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#sightEngineForm")[0]);
      formData.append(
        "is_sight_engine_enabled",
        $("#is_sight_engine_enabled").is(":checked") ? 1 : 0
      );

      $.ajax({
        type: "POST",
        url: `${domainUrl}updateSettings`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Error",
              message: "Setting Not Found",
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
              title: "Success",
              message: "Updated Successfully",
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

  $(document).on("submit", "#addDocumentTypeForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#addDocumentTypeForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}addDocumentType`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Dublicate Entry",
              message: "Document Type Dublicate Entry",
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
              title: "Success",
              message: "Document Type Added Succesfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#documentTypeTable").DataTable().ajax.reload(null, false);
            $("#documentTypeModal").modal("hide");
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

  $("#documentTypeTable").on("click", ".edit", function (e) {
    e.preventDefault();

    var id = $(this).attr("rel");
    var title = $(this).data("title");
    $("#documentTypeId").val(id);
    $("#editDocumentType").val(title);

    $("#editDocumentTypeModal").modal("show");
  });

  $(document).on("submit", "#editDocumentTypeForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $("#documentTypeId").val();
      let EditformData = new FormData($("#editDocumentTypeForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateDocumentType/` + id,
        data: EditformData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Same as Previous",
              message: "Document Type Dublicate Entry",
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
              message: "Document Type Update Succesfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#documentTypeTable").DataTable().ajax.reload(null, false);
            $("#editDocumentTypeModal").modal("hide");
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

  $("#documentTypeTable").on("click", ".delete", function (e) {
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
              url: `${domainUrl}deleteDocumentType/` + id,
              dataType: "json",
              success: function (response) {
                if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "Document Type Delete Succesfully",
                    color: "green",
                    position: toastPosition,
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#documentTypeTable").DataTable().ajax.reload(null, false);
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

  // Report Reason
  $("#reportReasonTable").dataTable({
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
      url: `${domainUrl}reportReasonList`,
      data: function (data) {},
      error: (error) => {
        console.log(error);
      },
    },
  });

  $(document).on("submit", "#reportReasonForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#reportReasonForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}addreportReason`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Dublicate Entry",
              message: "Reason Dublicate Entry",
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
              title: "Success",
              message: "Report Added Succesfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#reportReasonTable").DataTable().ajax.reload(null, false);
            $("#reportReasonModal").modal("hide");
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

  $("#reportReasonTable").on("click", ".edit", function (e) {
    e.preventDefault();
    var id = $(this).attr("rel");
    var title = $(this).data("title");
    $("#reportReasonId").val(id);
    $("#editReportReason").val(title);
    $("#editReportReasonModal").modal("show");
  });

  $(document).on("submit", "#editReportReasonForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $("#reportReasonId").val();
      let EditformData = new FormData($("#editReportReasonForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateReportReason/` + id,
        data: EditformData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Same as Previous",
              message: "Report Reason Exists",
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
              message: "Report Reason Update Succesfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#reportReasonTable").DataTable().ajax.reload(null, false);
            $("#editReportReasonModal").modal("hide");
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

  $("#reportReasonTable").on("click", ".delete", function (e) {
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
              url: `${domainUrl}deleteReportReasonType/`,
              data: {
                reportReason_id: id,
              },
              dataType: "json",
              success: function (response) {
                if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "Report reason Delete Succesfully",
                    color: "green",
                    position: toastPosition,
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#reportReasonTable").DataTable().ajax.reload(null, false);
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

  $(document).on("click", ".addFakeData", function (e) {
    e.preventDefault();
    var $button = $(this);
    $button.addClass("spinning disabled");
    if (user_type == 1) {
      $.ajax({
        type: "POST",
        url: `${domainUrl}addFakeData`,
        data: {},
        dataType: "json",
        success: function (response) {
          if (response.status == true) {
            iziToast.show({
              title: "Success",
              message: "Fake data added Successfully.",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
          } else {
            iziToast.show({
              title: "Oops",
              message: "First Add Interests.",
              color: "red",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/x.svg`,
            });
          }
          $button.removeClass("spinning disabled");
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
      $button.removeClass("spinning disabled");
    }
  });
});
