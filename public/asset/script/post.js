$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".postSideA").addClass("activeLi");
 
  $("#allPostsTable").dataTable({
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
      url: `${domainUrl}allPostsList`,
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#allPostsTable").on("click", ".deletePost", function (e) {
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
                    message: "Post Delete Successfully",
                    color: "green",
                    position: "bottomCenter",
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: false,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#allPostsTable").DataTable().ajax.reload(null, false);
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
 
});
