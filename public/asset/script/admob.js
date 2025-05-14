$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".admobSideA").addClass("activeLi");

  $("#admobAndroidForm").on("submit", function (event) {
    event.preventDefault();
    if (user_type == 1) {
      var formdata = new FormData($("#admobAndroidForm")[0]);
      $.ajax({
        url: `${domainUrl}updateSettings`,
        type: "POST",
        data: formdata,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            iziToast.show({
              title: "Error",
              message: "Admob not found",
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
              message: "Admob updated Successfully",
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
  $("#admobiOSForm").on("submit", function (event) {
    event.preventDefault();
    if (user_type == 1) {
      var formdata = new FormData($("#admobiOSForm")[0]);
      $.ajax({
        url: `${domainUrl}updateSettings`,
        type: "POST",
        data: formdata,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            iziToast.show({
              title: "Error",
              message: "Admob not found",
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
              message: "Admob updated Successfully",
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

  $(document).on("change", "#is_admob_on", function (event) {
    event.preventDefault();
    if ($(this).prop("checked") == true) {
      $("#admob_text").text(app.admobIsOn);
    } else {
      $("#admob_text").text(app.admobIsOff);
    }

      if (user_type == 1) {
        $id = $(this).attr("rel");
        if ($(this).prop("checked") == true) {
          $value = 1;
          $("#admob_text").text(app.admobIsOff);
        } else {
          $value = 0;
          $("#admob_text").text(app.admobIsOff);
        }

        $.post(
          `${domainUrl}updateSettings`,
          {
            is_admob_on: $value,
          },

          function (returnedData) {
             iziToast.show({
               title: "Success",
               message: "Admob updated Successfully",
               color: "green",
               position: toastPosition,
               transitionIn: "fadeInUp",
               transitionOut: "fadeOutDown",
               timeout: 3000,
               animateInside: false,
               iconUrl: `${domainUrl}asset/img/check-circle.svg`,
             });
             $("#admob_switch").load(location.href + " #admob_switch>*");
            // location.reload();
          }
        ).fail(function (error) {
          console.log(error);
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
});
