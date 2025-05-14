$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".musicSideA").addClass("activeLi");

  $("#categoryTable").dataTable({
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
      url: `${domainUrl}categoryList`,
      error: (error) => {
        console.log(error);
      },
    },
  });

  $(document).on("submit", "#addCategoryForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#addCategoryForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}addCategory`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            iziToast.show({
              title: "Dublicate Entry",
              message: "Category Dublicate Entry",
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
              message: "Category Added Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#categoryTable").DataTable().ajax.reload(null, false);
            $("#addCategoryModal").modal("hide");
            $("#addCategoryModal").load(
              location.href + " #addCategoryModal>*",
              ""
            );
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
        // iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#categoryTable").on("click", ".edit", function (e) {
    e.preventDefault();

    var id = $(this).attr("rel");
    var title = $(this).data("title");
    $("#categoryId").val(id);
    $("#editCategory").val(title);

    $("#editCategoryModal").modal("show");
  });

  $(document).on("submit", "#editCategoryForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $("#categoryId").val();
      let EditFormData = new FormData($("#editCategoryForm")[0]);
      EditFormData.append("category_id", id);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateCategory`,
        data: EditFormData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Oops",
              message: "Category Not Found.",
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
              title: "Updated",
              message: "Category Update Successfully.",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#categoryTable").DataTable().ajax.reload(null, false);
            $("#addCategoryForm").load(location.href + " #addCategoryForm>*", "");
            $("#editCategoryModal").modal("hide");
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
        // iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#categoryTable").on("click", ".delete", function (e) {
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
              url: `${domainUrl}deleteCategory`,
              dataType: "json",
              data: {
                category_id: id,
              },
              success: function (response) {
                if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "Category Delete Successfully",
                    color: "green",
                    position: toastPosition,
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: true,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#categoryTable").DataTable().ajax.reload(null, false);
                  $("#addFAQsModal").load(
                    location.href + " #addFAQsModal>*",
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
      iziToast.error({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: true,
        // iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#musicTable").dataTable({
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
      url: `${domainUrl}musicList`,
      error: (error) => {
        console.log(error);
      },
    },
  });

  $(document).on("submit", "#addMusicForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#addMusicForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}addMusic`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == true) {
            iziToast.show({
              title: "Success",
              message: "Music Added Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#musicTable").DataTable().ajax.reload(null, false);
            $("#addMusicModal").modal("hide");
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
        // iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#musicTable").on("click", ".edit", function (e) {
    e.preventDefault();

    var id = $(this).attr("rel");
    var title = $(this).data("title");
    var category_id = $(this).data("category_id");
    var duration = $(this).data("duration");
    var artist = $(this).data("artist");
    
    $("#musicId").val(id);
    $("#edit_music_title").val(title);
    $("#edit_category_id").val(category_id);
    $("#edit_music_duration").val(duration);
    $("#edit_music_artist").val(artist);

    $("#editMusicModal").modal("show");
  });

  $(document).on("submit", "#editMusicForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let EditFormData = new FormData($("#editMusicForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateMusic`,
        data: EditFormData,
        contentType: false,
        processData: false,
        success: function (response) {
         if (response.status == true) {
            iziToast.show({
              title: "Updated",
              message: "Music Update Successfully.",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#musicTable").DataTable().ajax.reload(null, false);
            $("#editMusicModal").modal("hide");
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
        // iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $("#musicTable").on("click", ".delete", function (e) {
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
              url: `${domainUrl}deleteMusic`,
              dataType: "json",
              data: {
                music_id: id,
              },
              success: function (response) {
                if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "Music Delete Successfully.",
                    color: "green",
                    position: toastPosition,
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: true,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#musicTable").DataTable().ajax.reload(null, false);
                }
              },
            });
          }
        }
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
        // iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

});
