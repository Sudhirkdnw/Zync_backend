$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".reelSideA").addClass("activeLi");
 
  $("#reelTable").dataTable({
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
      url: `${domainUrl}reelList`,
      error: (error) => {
        console.log(error);
      },
    },
  });
 
});
