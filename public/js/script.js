$(document).ready(function () {
    $("#taskTable").DataTable();
    $("#tableInitiate").DataTable();
});

tippy("#approval.badge-warning", {
    content: "L1 Manager: Douglas McGee",
    placement: "right",
});

$(document).ready(function () {
    // Function to handle "Select All" button click
    $("#select-all").click(function () {
        $(".day-button").addClass("active");
    });

    // Function to handle individual day button clicks
    $(".day-button").click(function () {
        $(this).toggleClass("active");
        if ($(".day-button.active").length === 7) {
            $("#select-all").addClass("active");
        } else {
            $("#select-all").removeClass("active");
        }
    });
});

$(document).ready(function () {
    // Hide the .reminders element initially
    // $('.reminders').hide();

    // Toggle the hidden attribute of .reminders based on the checkbox state
    $("#checkbox_reminder").change(function () {
        if ($(this).is(":checked")) {
            $(".reminders").removeAttr("hidden");
            $("#messages").attr("required", true);
        } else {
            $(".reminders").attr("hidden", true);
            $("#messages").removeAttr("required");
        }
    });
});

function logout() {
    let timerInterval;
    Swal.fire({
        title: "Logout Confirmation",
        text: "Are you sure you want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#4e73df",
        cancelButtonColor: "#e74a3b",
        confirmButtonText: "Yes, logout",
        cancelButtonText: "Cancel",
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: "You have been logged out.",
                icon: "success",
                timer: 1500,
                showConfirmButton: false,
                willClose: () => {
                    clearInterval(timerInterval);
                },
            }).then(() => {
                // Redirect to the route success page after the user clicks "OK"
                window.location.href = "/logout";
            });
        }
    });
}

$(document).ready(function () {
    var max_fields = 10; //maximum input boxes allowed
    var wrapper = $(".container-card"); //Fields wrapper
    var add_button = $(".add_field_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function (e) {
        //on add input button click
        console.log(e);
        e.preventDefault();
        if (x < max_fields) {
            //max input box allowed
            x++; //text box increment
            $(wrapper).append(
                '<div class="card border-left-primary col-md-12 mb-3 shadow-sm">' +
                    ' <div class="card-header border-0 px-0 bg-white d-sm-flex align-items-center justify-content-end"><a class="btn btn-danger btn-sm btn-circle remove_field"><i class="fas fa-times"></i></a></div>' +
                    '<div class="card-body pt-0">' +
                    '<div class="row mx-auto">' +
                    '<div class="col-md-4">' +
                    '<div class="form-group">' +
                    '<label for="kpi' +
                    x +
                    '">KPI</label>' +
                    '<textarea name="kpi[]' +
                    x +
                    '" id="kpi' +
                    x +
                    '" class="form-control"></textarea>' +
                    "</div>" +
                    "</div>" +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label for="target' +
                    x +
                    '">Target</label>' +
                    '<input type="text" name="target[]' +
                    x +
                    '" id="target' +
                    x +
                    '" class="form-control">' +
                    "</div>" +
                    "</div>" +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label for="uom' +
                    x +
                    '">UoM</label>' +
                    '<select class="form-control" name="uom[]' +
                    x +
                    '" id="uom' +
                    x +
                    '" title="Unit of Measure" required>' +
                    '<option value="">Select</option>' +
                    '<option value="Piece">Piece</option>' +
                    '<option value="Kilogram">Kilogram</option>' +
                    '<option value="Hectare">Hectare</option>' +
                    '<option value="Other">Others</option>' +
                    "</select>" +
                    "</div>" +
                    "</div>" +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label for="weightage' +
                    x +
                    '">Weightage</label>' +
                    '<div class="input-group">' +
                    '<input type="number" min="5" max="100" class="form-control" name="weightage[]' +
                    x +
                    '" value="{{ old("weightage") }}" required>' +
                    '<div class="input-group-append">' +
                    '<span class="input-group-text">%</span>' +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    '<div class="col-md-2">' +
                    '<div class="form-group">' +
                    '<label for="type' +
                    x +
                    '">Type</label>' +
                    '<select class="form-control" name="type[]' +
                    x +
                    '" id="type' +
                    x +
                    '" required>' +
                    '<option value="">Select</option>' +
                    '<option value="Higher is Better">Higher is Better</option>' +
                    '<option value="Lower is Better">Lower is Better</option>' +
                    '<option value="Exact Value">Exact Value</option>' +
                    "</select>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "</div>"
            ); //add input box
        }
    });

    $(wrapper).on("click", ".remove_field", function (e) {
        //user click on remove text
        e.preventDefault();
        $(this).closest(".card").remove();
        x--;
    });
});
