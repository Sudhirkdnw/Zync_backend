$(document).ready(function(){
    $(".sideBarli").removeClass("activeLi");
    $(".reportSideA").addClass("activeLi");

    // Room Report

    $("#reportTable").dataTable({
        process: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [
            [0, "desc"]
        ],
        columnDefs: [{
            targets: [0, 1, 2, 3, 4],
            orderable: false,
        }],
        ajax: {
            url: `${domainUrl}reportList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            }
        },
    });
   
    $("#reportTable").on('click', '.rejectReport', function (e) {
        e.preventDefault();

        if (user_type == 1) {
            var id = $(this).attr('rel');
            console.log(id);

            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ['Cancel', 'Yes'],
            })
            .then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deleteReport/`,
                            data : {
                                report_id : id
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                }  else if (response.status == true) {
                                    iziToast.show({
                                        title: 'Deleted',
                                        message: 'Report Delete Succesfully',
                                        color: 'green',
                                        position: 'bottomCenter',
                                        transitionIn: 'fadeInUp',
                                        transitionOut: 'fadeOutDown',
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#reportTable").DataTable().ajax.reload(null, false);
                                    console.log(response.message);
                                }
                            }
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
    
    $("#reportTable").on('click', '.deleteReportWithRoom', function (e) {
        e.preventDefault();
        if (user_type == 1) {
            var id = $(this).attr('rel');
            console.log(id);
            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ['Cancel', 'Yes'],
            })
            .then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deleteReportWithRoom`,
                            data: {
                                report_RoomId: id
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                }  else if (response.status == true) {
                                    iziToast.show({
                                        title: 'Deleted',
                                        message: 'Room Delete Succesfully',
                                        color: 'green',
                                        position: 'bottomCenter',
                                        transitionIn: 'fadeInUp',
                                        transitionOut: 'fadeOutDown',
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#reportTable").DataTable().ajax.reload(null, false);
                                    console.log(response.message);
                                }
                            }
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
   
    // Post Report
       
    $("#postReportTable").dataTable({
        process: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [
            [0, "desc"]
        ],
        columnDefs: [{
            targets: [0, 1, 2, 3],
            orderable: false,
        }],
        ajax: {
            url: `${domainUrl}reportPostList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            }
        },
    });

    $("#postReportTable").on('click', '.rejectReport', function (e) {
        e.preventDefault();

        if (user_type == 1) {
            var id = $(this).attr('rel');

            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ['Cancel', 'Yes'],
            })
            .then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deletePostReport/`,
                            data : {
                                report_id : id
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                }  else if (response.status == true) {
                                    iziToast.show({
                                        title: 'Deleted',
                                        message: 'Report Delete Succesfully',
                                        color: 'green',
                                        position: 'bottomCenter',
                                        transitionIn: 'fadeInUp',
                                        transitionOut: 'fadeOutDown',
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#postReportTable").DataTable().ajax.reload(null, false);
                                    console.log(response.message);
                                }
                            }
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

    $("#postReportTable").on('click', '.deletePost', function (e) {
        e.preventDefault();
        if (user_type == 1) {
            var id = $(this).attr('rel');
            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ['Cancel', 'Yes'],
            })
            .then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deletePost`,
                            dataType: "json",
                            data:  {
                                report_id : id
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                }  else if (response.status == true) {
                                    console.log(response.data);
                                    iziToast.show({
                                        title: 'Deleted',
                                        message: 'Post Delete Succesfully',
                                        color: 'green',
                                        position: 'bottomCenter',
                                        transitionIn: 'fadeInUp',
                                        transitionOut: 'fadeOutDown',
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#postReportTable").DataTable().ajax.reload(null, false);
                                    console.log(response.message);
                                }
                            }
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

    // User Report

    $("#userReportTable").dataTable({
        process: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [
            [0, "desc"]
        ],
        columnDefs: [{
            targets: [0, 1, 2, 3],
            orderable: false,
        }],
        ajax: {
            url: `${domainUrl}userReportList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            }
        },
    });

    $("#userReportTable").on('click', '.rejectReport', function (e) {
        e.preventDefault();

        if (user_type == 1) {
            var id = $(this).attr('rel');
            console.log(id);
            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ['Cancel', 'Yes'],
            })
            .then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deleteUserReport`,
                            data: {
                                report_id : id
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                }  else if (response.status == true) {
                                    console.log(response.data);
                                    iziToast.show({
                                        title: 'Deleted',
                                        message: 'Report Delete Succesfully',
                                        color: 'green',
                                        position: 'bottomCenter',
                                        transitionIn: 'fadeInUp',
                                        transitionOut: 'fadeOutDown',
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#userReportTable").DataTable().ajax.reload(null, false);
                                    console.log(response.message);
                                }
                            }
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

    $("#userReportTable").on('click', '.blockUserBtn', function (e) {
        e.preventDefault();
        if (user_type == 1) {
            var id = $(this).attr('rel');
            console.log(id);
            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ['Cancel', 'Yes'],
            })
            .then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}blockUserFromReport/`,
                            data : {
                                report_id : id,
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                }  else if (response.status == true) {
                                    iziToast.show({
                                        title: 'Block',
                                        message: 'User is in blocklist',
                                        color: 'green',
                                        position: 'bottomCenter',
                                        transitionIn: 'fadeInUp',
                                        transitionOut: 'fadeOutDown',
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#userReportTable").DataTable().ajax.reload(null, false);
                                    console.log(response.message);
                                }
                            }
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

    // Reel Report

    $("#reelReportTable").dataTable({
        process: true,
        serverSide: true,
        serverMethod: "post",
        aaSorting: [
            [0, "desc"]
        ],
        columnDefs: [{
            targets: [0, 1, 2, 3],
            orderable: false,
        }],
        ajax: {
            url: `${domainUrl}reelReportList`,
            data: function (data) {},
            error: (error) => {
                console.log(error);
            }
        },
    });

    $("#reelReportTable").on('click', '.rejectReport', function (e) {
        e.preventDefault();

        if (user_type == 1) {
            var id = $(this).attr('rel');
            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ['Cancel', 'Yes'],
            })
            .then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deleteReelReport`,
                            data: {
                                report_id : id
                            },
                            dataType: "json",
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                }  else if (response.status == true) {
                                    console.log(response.data);
                                    iziToast.show({
                                        title: 'Deleted',
                                        message: 'Report Delete Succesfully',
                                        color: 'green',
                                        position: 'bottomCenter',
                                        transitionIn: 'fadeInUp',
                                        transitionOut: 'fadeOutDown',
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#reelReportTable").DataTable().ajax.reload(null, false);
                                    console.log(response.message);
                                }
                            }
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

    $("#reelReportTable").on('click', '.deleteReel', function (e) {
        e.preventDefault();
        if (user_type == 1) {
            var id = $(this).attr('rel');
            swal({
                title: "Are you sure?",
                icon: "error",
                buttons: true,
                dangerMode: true,
                buttons: ['Cancel', 'Yes'],
            })
            .then((deleteValue) => {
                if (deleteValue) {
                    if (deleteValue == true) {
                        $.ajax({
                            type: "POST",
                            url: `${domainUrl}deleteReelFromReport`,
                            dataType: "json",
                            data:  {
                                reel_id : id
                            },
                            success: function (response) {
                                if (response.status == false) {
                                    console.log(response.message);
                                }  else if (response.status == true) {
                                    iziToast.show({
                                        title: 'Deleted',
                                        message: 'Reel Delete Successfully.',
                                        color: 'green',
                                        position: 'bottomCenter',
                                        transitionIn: 'fadeInUp',
                                        transitionOut: 'fadeOutDown',
                                        timeout: 3000,
                                        animateInside: false,
                                        iconUrl: `${domainUrl}asset/img/check-circle.svg`,
                                    });
                                    $("#reelReportTable").DataTable().ajax.reload(null, false);
                                }
                            }
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
