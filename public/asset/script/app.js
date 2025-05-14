$.ajaxSetup({
  headers: {
    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
  },
});

var user_type = $("#user_type").val();
var toastPosition = "bottomCenter";

$(document).on("hidden.bs.modal", function () {
  $("form")[0].reset();
  $(this).data("bs.modal", null);
  $(".swiper-slide video").attr("src", "");
  $("video").attr("src", "");
  // $("audio").attr("src", "");
  $("#comment-content").html("");
  $("#comment-content1").html("");
  $("#comment-content2").html("");
  $("#no_comments").hide();
  $("#no_comments1").hide();
  $("#no_comments2").hide();
});

$(document).on("hidden.bs.modal", function () {
  $("form").trigger("reset");
  $(this).data("bs.modal", null);

  $(".saveButton").removeClass("spinning disabled");

  $(".saveButton1").removeClass("spinning disabled");
});

$("form").on("submit", function () {
  $(".saveButton").addClass("spinning disabled");
  $(".saveButton1").addClass("spinning disabled");
});

$("#admobAndroidForm").on("submit", function () {
  $(".saveButton2").addClass("spinning disabled");

  setTimeout(function () {
    $(".saveButton2").removeClass("spinning disabled");
  }, 1000);
});

$("#admobiOSForm").on("submit", function () {
  $(".saveButton3").addClass("spinning disabled");

  setTimeout(function () {
    $(".saveButton3").removeClass("spinning disabled");
  }, 1000);
});
