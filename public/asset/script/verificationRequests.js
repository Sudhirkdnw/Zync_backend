$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".verificationRequestsSideA").addClass("activeLi");

    $("#profileVerificationTable").dataTable({
        processing: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [[0, "desc"]],
        columnDefs: [
            {
                targets: [0, 1,2,3,4,5,6],
                orderable: false,
            },
        ],
        ajax: {
            url: `${domainUrl}profileVerificationList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            },
        },
    });
    
    $("#profileVerificationTable").on("click", "img", function (e) {
        e.preventDefault();

        var image = $(this).data("image");
        var modalTitle = $(this).data("modal_title");
        
        $("#imagePreview").attr('src', `${image}`);
        $("#previewModalTitle").text(modalTitle);

        $("#imagePreviewModal").modal("show");
    });

    $("#profileVerificationTable").on("click", ".approved", function (e) {
        e.preventDefault();
        var id = $(this).attr("rel");
        if (user_type == 1) {
            swal({
                title: "Are you sure?",
                icon: "success",
                buttons: true,
                buttons: ["Cancel", "Approve"],
            }).then((approved) => {
                if (approved) {
                    if (approved == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}approvedProfileVerification/` + id,
                            dataType: "json",
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: "Approved",
                                        message: "Profile Verified Succesfully",
                                        color: "green",
                                        position: toastPosition,
                                        transitionIn: "fadeInUp",
                                        transitionOut: "fadeOutDown",
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#profileVerificationTable").DataTable().ajax.reload(null, false);
                                    console.log(response.message);
                                }
                                else {
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

    $("#profileVerificationTable").on("click", ".reject", function (e) {
        e.preventDefault();
        var id = $(this).attr("rel");
        if (user_type == 1) {
            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                buttons: ["Cancel", "Reject "],
            }).then((approved) => {
                if (approved) {
                    if (approved == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}rejectProfileVerification/` + id,
                            dataType: "json",
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                } else if (response.status == true) {
                                    iziToast.show({
                                        title: "Rejected",
                                        message: "Profile Rejected",
                                        color: "green",
                                        position: toastPosition,
                                        transitionIn: "fadeInUp",
                                        transitionOut: "fadeOutDown",
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#profileVerificationTable").DataTable().ajax.reload(null, false);
                                    console.log(response.message);
                                }
                                else {
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