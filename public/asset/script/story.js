$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".storySideA").addClass("activeLi");
  
  $("#allStoriesTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [0, 1, 2, 3],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}allStoriesList`,
      error: (error) => {
        console.log(error);
      },
    },
  });

 $("#allStoriesTable").on("click", ".deleteStory", function (e) {
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
                 $("#allStoriesTable").DataTable().ajax.reload(null, false);
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
