$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".roomSideA").addClass("activeLi");

  var id = $("#room_id").val(); 

  $("#roomsListTable").dataTable({
    process: true,
    serverSide: true,
    serverMethod: "post",
    aaSorting: [[0, "desc"]],
    columnDefs: [
      {
        targets: [0, 1, 2, 3, 4],
        orderable: false,
      },
    ],
    ajax: {
      url: `${domainUrl}roomsListWeb`,
      data: function (data) {},
      error: (error) => {
        console.log(error);
      },
    },
  });


  $("#allRoomUsersListTableWeb").dataTable({
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
      url: `${domainUrl}allRoomUsersListTableWeb`,
      data: {
        room_id: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });

  $("#roomMembersListTableWeb").dataTable({
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
      url: `${domainUrl}roomMembersListTableWeb`,
      data: {
        room_id: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });
  $("#roomCoAdminTableWeb").dataTable({
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
      url: `${domainUrl}roomCoAdminTableWeb`,
      data: {
        room_id: id,
      },
      error: (error) => {
        console.log(error);
      },
    },
  });

});
