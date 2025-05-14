$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".faqsSideA").addClass("activeLi");

  // FAQs
  $("#faqsTypeTable").dataTable({
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
      url: `${domainUrl}faqsTypeList`,
      data: function (data) {},
      error: (error) => {
        console.log(error);
      },
    },
  });

  $(document).on("submit", "#addFAQsTypeForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#addFAQsTypeForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}addFAQsType`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Dublicate Entry",
              message: "FAQs Type Dublicate Entry",
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
              message: "FAQs Type Added Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#faqsTypeTable").DataTable().ajax.reload(null, false);
            $("#addFAQsTypeModal").modal("hide");
            $("#addFAQsTypeModal").load(
              location.href + " #addFAQsTypeModal>*",
              ""
            );
            $("#addFAQsForm").load(location.href + " #addFAQsForm>*", "");
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

  $("#faqsTypeTable").on("click", ".edit", function (e) {
    e.preventDefault();

    var id = $(this).attr("rel");
    var title = $(this).data("title");
    $("#faqsTypeId").val(id);
    $("#editFaqsType").val(title);

    $("#editFaqsTypeModal").modal("show");
  });

  $(document).on("submit", "#editFAQsTypeForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $("#faqsTypeId").val();
      let EditformData = new FormData($("#editFAQsTypeForm")[0]);
      EditformData.append("faqsType_id", id);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateFAQsType`,
        data: EditformData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Same as Previous",
              message: "FAQs Type Dublicate Entry",
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
              message: "FAQs Type Update Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#faqsTypeTable").DataTable().ajax.reload(null, false);
            $("#addFAQsForm").load(location.href + " #addFAQsForm>*", "");
            $("#editFaqsTypeModal").modal("hide");
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

  $("#faqsTypeTable").on("click", ".delete", function (e) {
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
              url: `${domainUrl}deleteFAQsType`,
              dataType: "json",
              data: {
                faqsType_id: id,
              },
              success: function (response) {
                if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "FAQs Type Delete Successfully",
                    color: "green",
                    position: toastPosition,
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: true,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#faqsTypeTable").DataTable().ajax.reload(null, false);
                  $("#faqsTable").DataTable().ajax.reload(null, false);
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

  $("#faqsTable").dataTable({
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
      url: `${domainUrl}faqsList`,
      data: function (data) {},
      error: (error) => {
        console.log(error);
      },
    },
  });

  $(document).on("submit", "#addFAQsForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#addFAQsForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}addFAQs`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Dublicate Entry",
              message: "FAQs Dublicate Entry",
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
              message: "FAQs Added Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#faqsTable").DataTable().ajax.reload(null, false);
            $("#addFAQsModal").modal("hide");
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

  $("#faqsTable").on("click", ".edit", function (e) {
    e.preventDefault();

    var id = $(this).attr("rel");
    var faqtype = $(this).data("faqtype");
    var question = $(this).data("question");
    var answer = $(this).data("answer");
    $("#faqsID").val(id);
    $("#edit_faqs_type_id").val(faqtype);
    $("#editQuestion").val(question);
    $("#editAnswer").html(answer);

    $("#editFAQsModal").modal("show");
  });

  $(document).on("submit", "#editFAQsForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      var id = $("#faqsID").val();
      let EditformData = new FormData($("#editFAQsForm")[0]);
      EditformData.append("FAQs_id", id);
      $.ajax({
        type: "POST",
        url: `${domainUrl}updateFAQs`,
        data: EditformData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Same as Previous",
              message: "FAQs Dublicate Entry",
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
              message: "FAQs Update Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: "fadeInUp",
              transitionOut: "fadeOutDown",
              timeout: 3000,
              animateInside: true,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#faqsTable").DataTable().ajax.reload(null, false);
            $("#editFAQsModal").modal("hide");
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

  $("#faqsTable").on("click", ".delete", function (e) {
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
              url: `${domainUrl}deleteFAQs`,
              dataType: "json",
              data: {
                faqs_id: id,
              },
              success: function (response) {
                if (response.status == true) {
                  iziToast.show({
                    title: "Deleted",
                    message: "FAQs Delete Successfully",
                    color: "green",
                    position: toastPosition,
                    transitionIn: "fadeInUp",
                    transitionOut: "fadeOutDown",
                    timeout: 3000,
                    animateInside: true,
                    iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                  });
                  $("#faqsTable").DataTable().ajax.reload(null, false);
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
});
