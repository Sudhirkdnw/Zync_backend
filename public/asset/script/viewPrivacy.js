$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".privacySideA").addClass("activeLi");

    // $('#summernote').summernote();
    
    let summernoteOptions = {
        height: 550,
    };
    $("#summernote").summernote(summernoteOptions);

    $(document).on("submit", "#contentForm", function (e) {
        e.preventDefault();
        if (user_type == 1) {
            let formData = new FormData($("#contentForm")[0]);

            $.ajax({
                type: "POST",
                url: `${domainUrl}addContentForm`,
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == false) {
                        console.log(response.errors);
                    } else if (response.status == true) {
                        iziToast.show({
                            title: "Success",
                            message: "Privacy Policy Updated Succesfully",
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



    // $("#privacy").on("submit", function (event) {
    //     event.preventDefault();
    //     // $(".loader").show();
    //     if (user_type == 1) {
    //         var formdata = new FormData($("#privacy")[0]);
    //         $.ajax({
    //             url:  domainUrl + "updatePrivacy",
    //             type: "POST",
    //             data: formdata,
    //             dataType: "json",
    //             contentType: false,
    //             cache: false,
    //             processData: false,
    //             success: function (response) {
    //                 iziToast.show({
    //                     title: "Updated",
    //                     message: "Privacy Updated Succesfully",
    //                     color: "green",
    //                     position: toastPosition,
    //                     transitionIn: "fadeInUp",
    //                     transitionOut: "fadeOutDown",
    //                     timeout: 3000,
    //                     animateInside: false,
    //                     iconUrl: `${domainUrl}asset/img/check-circle.svg`,
    //                 });
    //             },
    //             error: (error) => {
    //                 iziToast.show({
    //                     title: "Bas ",
    //                     message: "--",
    //                     color: "red",
    //                     position: toastPosition,
    //                     transitionIn: "fadeInUp",
    //                     transitionOut: "fadeOutDown",
    //                     timeout: 3000,
    //                     animateInside: false,
    //                     iconUrl: `${domainUrl}asset/img/check-circle.svg`,
    //                 });
    //                 // console.log(JSON.stringify(error));
    //             },
    //         });
    //     } else {
    //         $(".loader").hide();
    //         iziToast.error({
    //             title: "Error!",
    //             message: " you are Tester ",
    //             position: "topRight",
    //         });
    //     }
    // });
});
