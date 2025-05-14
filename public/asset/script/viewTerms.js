$(document).ready(function () {
    $(".sideBarli").removeClass("activeLi");
    $(".termsSideA").addClass("activeLi");

    // $('#summernote').summernote();

    $(document).on("submit", "#contentForm", function (e) {
        e.preventDefault();
        if (user_type == 1) {
            let formData = new FormData($("#contentForm")[0]);
            $.ajax({
                type: "POST",
                url: `${domainUrl}addTermsForm`,
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    if (response.status == false) {
                        console.log(response.errors);
                    } else if (response.status == true) {
                        iziToast.show({
                            title: "Success",
                            message: "Terms Of Uses Updated Succesfully",
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


    let summernoteOptions = {
        height: 550,
    };
    $("#summernote").summernote(summernoteOptions);

    // $("#terms").on("submit", function (event) {
    //     event.preventDefault();
    //     $(".loader").show();
    //     if (user_type == "1") {
    //         var formdata = new FormData($("#terms")[0]);
    //         $.ajax({
    //             url: domainUrl + "updateTerms",
    //             type: "POST",
    //             data: formdata,
    //             dataType: "json",
    //             contentType: false,
    //             cache: false,
    //             processData: false,
    //             success: function (response) {
    //                 // $(".loader").hide();
    //                 location.reload();
    //             },
    //             error: (error) => {
    //                 $(".loader").hide();
    //                 console.log(JSON.stringify(error));
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
