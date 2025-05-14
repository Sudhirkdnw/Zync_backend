var domainUrl = $("#appUrl").val();

let start = 0;
const limit = 10;
let hasMoreComments = true;

$(window).on("resize", function () {
  if ($(window).width() >= 1199) {
    $("table").removeClass("table-responsive");
  }

  if ($(window).width() <= 1199) {
    $("table").addClass("table-responsive");
  }
});

$(document).on("click", ".viewDescPost", function (e) {
  e.preventDefault();

  function empty(element) {
    element.replaceChildren();
  }

  let parentClass = document.getElementById("post_contents");
  empty(parentClass);
  let imagePostInterests = document.getElementById("desc-post-interests");
  empty(imagePostInterests);

  const postId = $(this).data("postid");
  const desc = $(this).data("desc");
  const userAvatar = $(this).data("profile");
  const userName = $(this).data("username");
  const userid = $(this).data("userid");
  const interests = $(this).data("interests");

  if (interests != "") {
    const interestTag = `<div class='py-2 border-bottom'>${interests}</div>`;
    $("#desc-post-interests").append(interestTag);
  }

  let start = 0;
  const limit = 10;
  let hasMoreComments = true;
  let loadedComments = 0;

  function fetchComments(postId) {
    if (!hasMoreComments) return;

    $.ajax({
      url: `${domainUrl}fetchPostComment`,
      data: { post_id: postId, start, limit },
      dataType: "json",
      success: function (response) {
        if (response.status) {
          const postComments = response.data;
          hasMoreComments = response.hasMore;

          loadedComments = postComments.length;
          if (loadedComments == 0) {
            $("#no_comments1").show();
            $(".comment-content-div1").addClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton1").hide();
          }
          postComments.forEach((comment) => {
            const commentHtml = `
              <div class="comment-user-data">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <a href="${domainUrl}usersDetail/${
              comment.user.id
            }" class="d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center">
                      <div class="post-modal-user-profile mr-2">
                        <img src="${
                          comment.user.profile || "asset/image/default.png"
                        }" alt="" id="user-profile">
                      </div>
                      <div class="comment-user">
                        <div id="comment-user-name">${
                          comment.user.username
                        }</div>
                      </div>
                    </div>
                  </a>
                  <div class="commentDeleteFromAdmin" id="commentDeleteFromAdmin" rel="${
                    comment.id
                  }" data-user_id="${comment.user.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      <line x1="10" y1="11" x2="10" y2="17"></line>
                      <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                  </div>
                </div>
                <div class="d-flex align-items-start justify-content-between">
                  <div id="comment-text">${comment.desc}</div>
                    <span class="text-danger badge d-flex align-items-center justify-content-between">
                      <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="#fc544b" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                      <span class="ms-1 fw-normal">
                      ${comment.comment_like_count}
                      </span>
                    </span>
                  </div>
                </div>
              </div>
            `;
            $("#comment-content1").append(commentHtml);
            $(".comment-content-div1").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton1").show();
          });

          start += limit;
          loadedComments += postComments.length;

          if (loadedComments >= 10) {
            $(".comment-content-div1").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $(window).on("scroll", function () {
              const scrollPosition = $(window).scrollTop() + $(window).height();
              const commentBottom =
                $("#comment-content1").offset().top +
                $("#comment-content1").outerHeight();

              if (scrollPosition >= commentBottom) {
                $("#loadMoreButton1").show();
              }
            });
          }

          if (!hasMoreComments) {
            $("#loadMoreButton1").hide();
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("Error fetching comments:", error);
      },
    });
  }

  fetchComments(postId);

  $("#loadMoreButton")
    .off("click")
    .on("click", function () {
      fetchComments(postId);
    });

  if (userAvatar == null) {
    $("#postUserProfile1").attr("src", domainUrl + "asset/image/default.png");
  } else {
    $("#postUserProfile1").attr("src", userAvatar);
  }
  $("#postUserDataId1").attr("href", domainUrl + "usersDetail/" + userid);
  $("#postUserName1").text(userName);
  $("#postDesc1").text(desc);
  $("#viewPostDescModal").modal("show");
});

$(document).on("click", ".viewPost", function (e) {
  e.preventDefault();

  function empty(element) {
    element.replaceChildren();
  }

  let parentClass = document.getElementById("post_contents");
  empty(parentClass);
  let imagePostInterests = document.getElementById("image-post-interests");
  empty(imagePostInterests);

  const postId = $(this).data("postid");
  const desc = $(this).data("desc");
  const images = $(this).data("image");
  const userAvatar = $(this).data("profile");
  const userName = $(this).data("username");
  const userid = $(this).data("userid");
  const postContents = document.getElementById("post_contents");
  const interests = $(this).data("interests");

  if (interests != "") {
    const interestTag = `<div class='py-2 border-bottom'>${interests}</div>`;
    $("#image-post-interests").append(interestTag);
  }

  images.forEach((src) => {
    const imgContainer = document.createElement("div");
    imgContainer.className = "swiper-slide";

    const img = document.createElement("img");
    img.src = src;
    img.controls = true;

    imgContainer.appendChild(img);
    postContents.appendChild(imgContainer);
  });

  $("#post_contents").addClass("swiper-wrapper");

  const swiper = new Swiper(".mySwiper", {
    spaceBetween: 30,
    pagination: { el: ".swiper-pagination", type: "fraction" },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

  let start = 0;
  const limit = 10;
  let hasMoreComments = true;
  let loadedComments = 0;

  function fetchComments(postId) {
    if (!hasMoreComments) return;

    $.ajax({
      url: `${domainUrl}fetchPostComment`,
      data: { post_id: postId, start, limit },
      dataType: "json",
      success: function (response) {
        if (response.status) {
          const postComments = response.data;
          hasMoreComments = response.hasMore;

          loadedComments = postComments.length;
          if (loadedComments == 0) {
            $("#no_comments").show();
            $(".comment-content-div").addClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton").hide();
          }
          postComments.forEach((comment) => {
            const commentHtml = `
              <div class="comment-user-data">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <a href="${domainUrl}usersDetail/${
              comment.user.id
            }" class="d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center">
                      <div class="post-modal-user-profile mr-2">
                        <img src="${
                          comment.user.profile || "asset/image/default.png"
                        }" alt="" id="user-profile">
                      </div>
                      <div class="comment-user">
                        <div id="comment-user-name">${
                          comment.user.username
                        }</div>
                      </div>
                    </div>
                  </a>
                  <div class="commentDeleteFromAdmin" id="commentDeleteFromAdmin" rel="${
                    comment.id
                  }" data-user_id="${comment.user.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      <line x1="10" y1="11" x2="10" y2="17"></line>
                      <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                  </div>
                </div>
                <div class="d-flex align-items-start justify-content-between">
                <div id="comment-text">${comment.desc}</div>
                  <span class="text-danger badge d-flex align-items-center justify-content-between">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="#fc544b" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    <span class="ms-1 fw-normal">
                    ${comment.comment_like_count}
                    </span>
                  </span>
                </div>
              </div>
            `;
            $("#comment-content").append(commentHtml);
            $(".comment-content-div").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton").show();
          });

          start += limit;
          loadedComments += postComments.length;

          if (loadedComments >= 10) {
            $(".comment-content-div").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $(window).on("scroll", function () {
              const scrollPosition = $(window).scrollTop() + $(window).height();
              const commentBottom =
                $("#comment-content").offset().top +
                $("#comment-content").outerHeight();

              if (scrollPosition >= commentBottom) {
                $("#loadMoreButton").show();
              }
            });
          }

          if (!hasMoreComments) {
            $("#loadMoreButton").hide();
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("Error fetching comments:", error);
      },
    });
  }

  fetchComments(postId);

  $("#loadMoreButton")
    .off("click")
    .on("click", function () {
      fetchComments(postId);
    });

  if (userAvatar == null) {
    $("#postUserProfile").attr("src", domainUrl + "asset/image/default.png");
  } else {
    $("#postUserProfile").attr("src", userAvatar);
  }
  $("#postUserDataId").attr("href", domainUrl + "usersDetail/" + userid);
  $("#postUserName").text(userName);
  $("#postDesc").text(desc);
  $("#viewPostModal").modal("show");
});

$(document).on("click", ".viewVideoPost", function (e) {
  e.preventDefault();

  function empty(element) {
    element.replaceChildren();
  }

  let parentClass = document.getElementById("post_contents");
  empty(parentClass);
  let imagePostInterests = document.getElementById("image-post-interests");
  empty(imagePostInterests);

  const postId = $(this).data("postid");
  const desc = $(this).data("desc");
  const images = $(this).data("image");
  const userAvatar = $(this).data("profile");
  const userName = $(this).data("username");
  const userid = $(this).data("userid");
  const postContents = document.getElementById("post_contents");
  const interests = $(this).data("interests");

  if (interests != "") {
    const interestTag = `<div class='py-2 border-bottom'>${interests}</div>`;
    $("#image-post-interests").append(interestTag);
  }

  images.forEach((src) => {
    const videoContainer = document.createElement("div");
    videoContainer.className = "swiper-slide";

    const video = document.createElement("video");
    video.src = src;
    video.controls = true;

    videoContainer.appendChild(video);
    postContents.appendChild(videoContainer);
  });

  $("#post_contents").addClass("swiper-wrapper");

  const swiper = new Swiper(".mySwiper", {
    spaceBetween: 30,
    pagination: { el: ".swiper-pagination", type: "fraction" },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

  let start = 0;
  const limit = 10;
  let hasMoreComments = true;
  let loadedComments = 0;

  function fetchComments(postId) {
    if (!hasMoreComments) return;

    $.ajax({
      url: `${domainUrl}fetchPostComment`,
      data: { post_id: postId, start, limit },
      dataType: "json",
      success: function (response) {
        if (response.status) {
          const postComments = response.data;
          hasMoreComments = response.hasMore;

          loadedComments = postComments.length;
          if (loadedComments == 0) {
            $("#no_comments").show();
            $(".comment-content-div").addClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton").hide();
          }
          postComments.forEach((comment) => {
            const commentHtml = `
              <div class="comment-user-data">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <a href="${domainUrl}usersDetail/${
              comment.user.id
            }" class="d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center">
                      <div class="post-modal-user-profile mr-2">
                        <img src="${
                          comment.user.profile || "asset/image/default.png"
                        }" alt="" id="user-profile">
                      </div>
                      <div class="comment-user">
                        <div id="comment-user-name">${
                          comment.user.username
                        }</div>
                      </div>
                    </div>
                  </a>
                  <div class="commentDeleteFromAdmin" id="commentDeleteFromAdmin" rel="${
                    comment.id
                  }" data-user_id="${comment.user.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      <line x1="10" y1="11" x2="10" y2="17"></line>
                      <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                  </div>
                </div>
                <div class="d-flex align-items-start justify-content-between">
                <div id="comment-text">${comment.desc}</div>
                  <span class="text-danger badge d-flex align-items-center justify-content-between">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="#fc544b" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    <span class="ms-1 fw-normal">
                    ${comment.comment_like_count}
                    </span>
                  </span>
                </div>
              </div>
              </div>
            `;
            $("#comment-content").append(commentHtml);
            $(".comment-content-div").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton").show();
          });

          start += limit;
          loadedComments += postComments.length;

          if (loadedComments >= 10) {
            $(".comment-content-div").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $(window).on("scroll", function () {
              const scrollPosition = $(window).scrollTop() + $(window).height();
              const commentBottom =
                $("#comment-content").offset().top +
                $("#comment-content").outerHeight();

              if (scrollPosition >= commentBottom) {
                $("#loadMoreButton").show();
              }
            });
          }

          if (!hasMoreComments) {
            $("#loadMoreButton").hide();
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("Error fetching comments:", error);
      },
    });
  }

  fetchComments(postId);

  $("#loadMoreButton")
    .off("click")
    .on("click", function () {
      fetchComments(postId);
    });

  if (userAvatar == null) {
    $("#postUserProfile").attr("src", domainUrl + "asset/image/default.png");
  } else {
    $("#postUserProfile").attr("src", userAvatar);
  }
  $("#postUserDataId").attr("href", domainUrl + "usersDetail/" + userid);
  $("#postUserName").text(userName);
  $("#postDesc").text(desc);
  $("#viewPostModal").modal("show");
});

$(document).on("click", ".viewAudioPost", function (e) {
  e.preventDefault();

  function empty(element) {
    element.replaceChildren();
  }

  let parentClass = document.getElementById("post_contents_audio");
  empty(parentClass);
  let audioPostInterests = document.getElementById("audio-post-interests");
  empty(audioPostInterests);

  const postId = $(this).data("postid");
  const desc = $(this).data("desc");
  const audios = $(this).data("audio");
  const userAvatar = $(this).data("profile");
  const userName = $(this).data("username");
  const userid = $(this).data("userid");
  const interests = $(this).data("interests");

  if (interests != "") {
    const interestTag = `<div class='py-2 border-bottom'>${interests}</div>`;
    $("#audio-post-interests").append(interestTag);
  }

  var parent = document.getElementById("post_contents_audio");
  const fragment = document.createDocumentFragment();
  const audioDiv = fragment.appendChild(document.createElement("div"));

  $(audioDiv).addClass("w-100");

  const audio = audioDiv.appendChild(document.createElement("audio"));
  $(audio).addClass("w-100");
  audio.controls = true;
  audio.src = audios;

  parent.appendChild(fragment);

  let start = 0;
  const limit = 10;
  let hasMoreComments = true;
  let loadedComments = 0;

  function fetchComments(postId) {
    if (!hasMoreComments) return;

    $.ajax({
      url: `${domainUrl}fetchPostComment`,
      data: { post_id: postId, start, limit },
      dataType: "json",
      success: function (response) {
        if (response.status) {
          const postComments = response.data;
          hasMoreComments = response.hasMore;

          loadedComments = postComments.length;
          if (loadedComments == 0) {
            $("#no_comments2").show();
            $(".comment-content-div2").addClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton2").hide();
          }
          postComments.forEach((comment) => {
            const commentHtml = `
              <div class="comment-user-data">
                <div class="d-flex align-items-center justify-content-between mb-2">
                  <a href="${domainUrl}usersDetail/${
              comment.user.id
            }" class="d-flex align-items-center justify-content-between text-dark text-decoration-none">
                    <div class="d-flex align-items-center">
                      <div class="post-modal-user-profile mr-2">
                        <img src="${
                          comment.user.profile || "asset/image/default.png"
                        }" alt="" id="user-profile">
                      </div>
                      <div class="comment-user">
                        <div id="comment-user-name">${
                          comment.user.username
                        }</div>
                      </div>
                    </div>
                  </a>
                  <div class="commentDeleteFromAdmin" id="commentDeleteFromAdmin" rel="${
                    comment.id
                  }" data-user_id="${comment.user.id}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                      <polyline points="3 6 5 6 21 6"></polyline>
                      <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      <line x1="10" y1="11" x2="10" y2="17"></line>
                      <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                  </div>
                </div>
                <div class="d-flex align-items-start justify-content-between">
                <div id="comment-text">${comment.desc}</div>
                  <span class="text-danger badge d-flex align-items-center justify-content-between">
                    <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2" fill="#fc544b" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    <span class="ms-1 fw-normal">
                    ${comment.comment_like_count}
                    </span>
                  </span>
                </div>
              </div>
              </div>
            `;
            $("#comment-content2").append(commentHtml);
            $(".comment-content-div2").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton2").show();
          });

          start += limit;
          loadedComments += postComments.length;

          if (loadedComments >= 10) {
            $(".comment-content-div2").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $(window).on("scroll", function () {
              const scrollPosition = $(window).scrollTop() + $(window).height();
              const commentBottom =
                $("#comment-content2").offset().top +
                $("#comment-content2").outerHeight();

              if (scrollPosition >= commentBottom) {
                $("#loadMoreButton2").show();
              }
            });
          }

          if (!hasMoreComments) {
            $("#loadMoreButton2").hide();
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("Error fetching comments:", error);
      },
    });
  }

  fetchComments(postId);

  $("#loadMoreButton2")
    .off("click")
    .on("click", function () {
      fetchComments(postId);
    });

  if (userAvatar == null) {
    $("#postUserProfile2").attr("src", domainUrl + "asset/image/default.png");
  } else {
    $("#postUserProfile2").attr("src", userAvatar);
  }
  $("#postUserDataId2").attr("href", domainUrl + "usersDetail/" + userid);
  $("#postUserName2").text(userName);
  $("#postAudioDesc").text(desc);

  $("#viewAudioPostModal").modal("show");
});

$(document).on("click", ".commentDeleteFromAdmin", function (e) {
  e.preventDefault();
  if (user_type == 1) {
    var id = $(this).attr("rel");
    var user_id = $(this).data("user_id");
    var $commentElement = $(this).closest(".comment-user-data");
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
            url: `${domainUrl}deleteCommentFromAdmin`,
            dataType: "json",
            data: {
              comment_id: id,
              user_id: user_id,
            },
            success: function (response) {
              if (response.status) {
                iziToast.show({
                  title: "Deleted",
                  message: "Comment deleted successfully",
                  color: "green",
                  position: toastPosition,
                  transitionIn: "fadeInUp",
                  transitionOut: "fadeOutDown",
                  timeout: 3000,
                  animateInside: true,
                  iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                });
                $commentElement.addClass("d-none");
                $("#userPostTable").DataTable().ajax.reload(null, false);
                $("#allPostsTable").DataTable().ajax.reload(null, false);
                $("#postReportTable").DataTable().ajax.reload(null, false);
              } else {
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

$(document).on("click", "#reelCommentDeleteFromAdmin", function (e) {
  e.preventDefault();
  if (user_type == 1) {
    var id = $(this).attr("rel");
    var user_id = $(this).data("user_id");
    var $commentElement = $(this).closest(".comment-user-data");
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
            url: `${domainUrl}deleteReelCommentFromAdmin`,
            dataType: "json",
            data: {
              comment_id: id,
              user_id: user_id,
            },
            success: function (response) {
              if (response.status) {
                iziToast.show({
                  title: "Deleted",
                  message: "Comment deleted successfully",
                  color: "green",
                  position: toastPosition,
                  transitionIn: "fadeInUp",
                  transitionOut: "fadeOutDown",
                  timeout: 3000,
                  animateInside: true,
                  iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                });
                $commentElement.addClass("d-none");
                $("#reelTable").DataTable().ajax.reload(null, false);
              } else {
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

$(document).on("click", ".deleteReelByAdmin", function (e) {
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
            url: `${domainUrl}deleteReelByAdmin`,
            dataType: "json",
            data: {
              reel_id: id,
            },
            success: function (response) {
              if (response.status == false) {
                console.log(response.message);
              } else if (response.status == true) {
                iziToast.show({
                  title: "Deleted",
                  message: "Reel Delete Successfully",
                  color: "green",
                  position: "bottomCenter",
                  transitionIn: "fadeInUp",
                  transitionOut: "fadeOutDown",
                  timeout: 3000,
                  animateInside: false,
                  iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                });
                $("#reelTable").DataTable().ajax.reload(null, false);
                $("#userReelTable").DataTable().ajax.reload(null, false);
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

$(document).on("click", ".viewStory", function (e) {
  e.preventDefault();
  var story = $(this).data("image");

  $("#story_content").attr("src", story);
  $("#viewStoryModal").modal("show");
});

$(document).on("click", ".viewStoryVideo", function (e) {
  e.preventDefault();
  var story = $(this).data("image");

  $("#story_content_video").attr("src", story);
  $("#viewStoryVideoModal").modal("show");
});

$(document).on("change", ".private", function (event) {
  event.preventDefault();

  swal({
    title: "Are you sure?",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      if (user_type == 1) {
        $id = $(this).attr("rel");
        if ($(this).prop("checked") == true) {
          $value = 1;
          iziToast.show({
            title: "Updated",
            message: "Room is Private Now",
            color: "green",
            position: toastPosition,
            transitionIn: "fadeInUp",
            transitionOut: "fadeOutDown",
            timeout: 3000,
            animateInside: true,
            iconUrl: `${domainUrl}asset/img/check-circle.svg`,
          });
        } else {
          $value = 0;
          iziToast.show({
            title: "Removed",
            message: "Room is Public Now",
            color: "green",
            position: toastPosition,
            transitionIn: "fadeInUp",
            transitionOut: "fadeOutDown",
            timeout: 3000,
            animateInside: true,
            iconUrl: `${domainUrl}asset/img/check-circle.svg`,
          });
        }

        $.post(
          `${domainUrl}updatePrivateStatus`,
          {
            id: $id,
            is_private: $value,
          },

          function (returnedData) {
            console.log(returnedData);

            $("#roomsListTable").DataTable().ajax.reload(null, false);
            $("#userRoomsOwnTable").DataTable().ajax.reload(null, false);
          }
        ).fail(function (error) {
          console.log(error);
        });
      } else {
        iziToast.error({
          title: "Error!",
          message: " you are Tester ",
          position: toastPosition,
        });
      }
    } else {
      $("#roomsListTable").DataTable().ajax.reload(null, false);
      $("#userRoomsOwnTable").DataTable().ajax.reload(null, false);
    }
  });
});

$(document).on("change", ".is_join_request_enable", function (event) {
  event.preventDefault();

  swal({
    title: "Are you sure?",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
    if (willDelete) {
      if (user_type == 1) {
        $id = $(this).attr("rel");

        if ($(this).prop("checked") == true) {
          $value = 1;
          iziToast.show({
            title: "Updated",
            message: "join request enable",
            color: "green",
            position: toastPosition,
            transitionIn: "fadeInUp",
            transitionOut: "fadeOutDown",
            timeout: 3000,
            animateInside: true,
            iconUrl: `${domainUrl}asset/img/check-circle.svg`,
          });
        } else {
          $value = 0;
          iziToast.show({
            title: "Disable",
            message: "join request disable",
            color: "green",
            position: toastPosition,
            transitionIn: "fadeInUp",
            transitionOut: "fadeOutDown",
            timeout: 3000,
            animateInside: true,
            iconUrl: `${domainUrl}asset/img/check-circle.svg`,
          });
        }

        $.post(
          `${domainUrl}updateJoinRequestStatus`,
          {
            id: $id,
            is_join_request_enable: $value,
          },

          function (returnedData) {
            console.log(returnedData);

            $("#roomsListTable").DataTable().ajax.reload(null, false);
            $("#userRoomsOwnTable").DataTable().ajax.reload(null, false);
          }
        ).fail(function (error) {
          console.log(error);
        });
      } else {
        iziToast.error({
          title: "Error!",
          message: " you are Tester ",
          position: toastPosition,
        });
      }
    } else {
      $("#roomsListTable").DataTable().ajax.reload(null, false);
      $("#userRoomsOwnTable").DataTable().ajax.reload(null, false);
    }
  });
});

$(document).on("change", ".moderator", function (event) {
  event.preventDefault();

  if (user_type == 1) {
    $id = $(this).attr("rel");
    if ($(this).prop("checked") == true) {
      $value = 1;
      iziToast.show({
        title: "Updated",
        message: "User is Moderator Now.",
        color: "green",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: true,
        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
      });
    } else {
      $value = 0;
      iziToast.show({
        title: "Removed",
        message: "User remove from Moderator.",
        color: "green",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: true,
        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
      });
    }

    $.post(
      `${domainUrl}updateModeratorStatus`,
      {
        id: $id,
        is_moderator: $value,
      },

      function (returnedData) {
        console.log(returnedData);

        $("#userTable").DataTable().ajax.reload(null, false);
      }
    ).fail(function (error) {
      console.log(error);
    });
  } else {
    iziToast.error({
      title: "Error!",
      message: " you are Tester ",
      position: toastPosition,
    });
  }
});

$(document).on("change", ".postRestricted", function (event) {
  event.preventDefault();

  if (user_type == 1) {
    $id = $(this).attr("rel");
    if ($(this).prop("checked") == true) {
      $value = 1;
      iziToast.show({
        title: "Updated",
        message: "Post is Restricted From Home Page.",
        color: "green",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: true,
        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
      });
    } else {
      $value = 0;
      iziToast.show({
        title: "Removed",
        message: "Post remove from Restrict.",
        color: "green",
        position: toastPosition,
        transitionIn: "fadeInUp",
        transitionOut: "fadeOutDown",
        timeout: 3000,
        animateInside: true,
        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
      });
    }

    $.post(
      `${domainUrl}updatePostRestrictionStatus`,
      {
        id: $id,
        is_restricted: $value,
      },

      function (returnedData) {
        console.log(returnedData);

        $("#allPostsTable").DataTable().ajax.reload(null, false);
      }
    ).fail(function (error) {
      console.log(error);
    });
  } else {
    iziToast.error({
      title: "Error!",
      message: " you are Tester ",
      position: toastPosition,
    });
  }
});

$(document).on("click", ".viewReelModal", function (e) {
  e.preventDefault();

  function empty(element) {
    element.replaceChildren();
  }

  let parentClass = document.getElementById("reel-product-data");
  let parentClass2 = document.getElementById("comment-content");
  let parentClass3 = document.getElementById("comment-content3");

  empty(parentClass);
  empty(parentClass2);
  empty(parentClass3);

  var reelId = $(this).attr("rel");
  var content = $(this).data("content");
  var user_id = $(this).data("user_id");
  var user_profile = $(this).data("user_profile");
  var username = $(this).data("username");
  var description = $(this).data("description");
  var interests = $(this).data("interests");

  $("#reelUserId").attr("href", domainUrl + "usersDetail/" + user_id);
  $("#reelUserProfile").attr("src", user_profile);
  $("#username").text(username);
  $("#reel_description").text(description);
  $("#reel_contents").attr("src", content);
  $("#reel-interests").text(interests);

  let start = 0;
  const limit = 10;
  let hasMoreComments = true;
  let loadedComments = 0;

  function fetchComments(reelId) {
    if (!hasMoreComments) return;

    $.ajax({
      url: `${domainUrl}fetchCommentsInReelModal`,
      data: { reel_id: reelId, start, limit },
      dataType: "json",
      success: function (response) {
        if (response.status) {
          const reelComments = response.data;
          hasMoreComments = response.hasMore;
          loadedComments = reelComments.length;
          if (loadedComments == 0) {
            $("#no_comments3").show();
            $(".comment-content-div").addClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton3").hide();
          } else {
            $("#no_comments3").hide();
          }
          reelComments.forEach((comment) => {
            const commentHtml = `
        <div class="comment-user-data">
          <div class="d-flex align-items-center justify-content-between mb-1">
            <div class="d-flex align-items-center justify-content-between text-dark text-decoration-none">
              <div class="d-flex align-items-center">
                <div class="post-modal-user-profile mr-2">
                  <img src="${
                    comment.user.profile ||
                    domainUrl + "assets/img/placeholder.png"
                  }" id="user-profile" class="rounded-circle me-1 object-fit-cover bg-info-lighten" height="35" width="35">
                </div>
                <div class="comment-user">
                  <div id="comment-user-name" class="text-dark fw-normal">${
                    comment.user.username
                  }</div>
                </div>
              </div>
            </div>
            <div class="commentDeleteFromAdmin" id="reelCommentDeleteFromAdmin" rel="${
              comment.id
            }" data-user_id="${comment.user.id}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2">
                <polyline points="3 6 5 6 21 6"></polyline>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                <line x1="10" y1="11" x2="10" y2="17"></line>
                <line x1="14" y1="11" x2="14" y2="17"></line>
              </svg>
            </div>
          </div>
          <div id="comment-text">${comment.description}</div>
        </div>
      `;
            $("#comment-content3").append(commentHtml);
            $(".comment-content-div").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $("#loadMoreButton").show();
          });

          start += limit;
          loadedComments += reelComments.length;

          if (loadedComments >= 10) {
            $(".comment-content-div").removeClass(
              "d-flex align-items-center justify-content-center"
            );
            $(window).on("scroll", function () {
              const scrollPosition = $(window).scrollTop() + $(window).height();
              const commentBottom =
                $("#comment-content3").offset().top +
                $("#comment-content3").outerHeight();
              if (scrollPosition >= commentBottom) {
                $("#loadMoreButton3").show();
              }
            });
          }
          if (!hasMoreComments) {
            $("#loadMoreButton3").hide();
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("Error fetching comments:", error);
      },
    });
  }

  fetchComments(reelId);

  $("#loadMoreButton3")
    .off("click")
    .on("click", function () {
      fetchComments(reelId);
    });

  $("#viewReelModal").modal("show");
});

$("#viewReelModal").on("hidden.bs.modal", function () {
  $("#reel_contents").attr("src", "");

  const commentContainer = document.getElementById("comment-content3");
  commentContainer.replaceChildren();
});

var app = {
  admobIsOn: "Admob is on",
  admobIsOff: "Admob is off",
};
