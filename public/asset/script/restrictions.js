$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".restrictionsSideA").addClass("activeLi");

  $("#usernameRestrictionsTable").dataTable({
    processing: true,
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
      url: `${domainUrl}usernameRestrictionsList`,
      data: function (data) {},
      error: (error) => {
        console.log(error);
      },
    },
  });

  $(document).on("submit", "#addUsernameRestrictForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#addUsernameRestrictForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}addUsernameRestrict`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            iziToast.show({
              title: "Dublicate Entry",
              message: "Username Dublicate Entry",
              color: "red",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/copy.svg`,
            });
          } else if (response.status == true) {
            iziToast.show({
              title: "Success",
              message: "Username Added Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#usernameRestrictionsTable")
              .DataTable()
              .ajax.reload(null, false);
            $("#usernameRestrictionModal").modal("hide");
          }
        },
      });
    } else {
      iziToast.error({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: true,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#usernameRestrictionsTable").on("click", ".edit", function (e) {
    e.preventDefault();

    var id = $(this).attr("rel");
    var title = $(this).data("title");
    $("#usernameId").val(id);
    $("#editTitle").val(title);

    $("#editUsernameRestrictionModal").modal("show");
  });

  $(document).on("submit", "#editUsernameRestrictionForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $("#usernameId").val();
      let EditformData = new FormData($("#editUsernameRestrictionForm")[0]);
      EditformData.append("username_id", id);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateUsernameRestrict`,
        data: EditformData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            iziToast.show({
              title: "Dublicate Entry",
              message: "Username Dublicate Entry",
              color: "red",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/copy.svg`,
            });
          } else if (response.status == true) {
            iziToast.show({
              title: "Success",
              message: "Username Update Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#usernameRestrictionsTable")
              .DataTable()
              .ajax.reload(null, false);
            $("#editUsernameRestrictionModal").modal("hide");
          }
        },
      });
    } else {
      iziToast.error({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: true,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#usernameRestrictionsTable").on("click", ".delete", function (e) {
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
              url: `${domainUrl}deleteUsernameRestrictions`,
              dataType: "json",
              data: {
                username_id: id,
              },
              success: function (response) {
                if (response.status == false) {
                  console.log(response.message);
                } else if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "Username Delete Succesfully",
                    color: "green",
                    position: toastPosition,
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#usernameRestrictionsTable")
                    .DataTable()
                    .ajax.reload(null, false);
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
});
