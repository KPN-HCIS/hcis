$(document).ready(function () {
    $("#taskTable").DataTable();
    $("#tableInitiate").DataTable();
});

// tippy("#approval.badge-warning", {
//     content: "L1 Manager: Douglas McGee",
//     placement: "right",
// });
$(document).ready(function () {
    let tooltipInitialized = false; // Flag to track tooltip initialization

    // Function to initialize tippy tooltip
    function initializeTooltip(name, layer) {
        tippy("#approval.badge-warning", {
            content: `Manager L${layer}: ${name}`,
            placement: "right",
        });
    }

    // Function to fetch tooltip content and initialize tooltip
    function fetchAndInitializeTooltip() {
        if (!tooltipInitialized) {
            fetch("/get-tooltip-content")
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Failed to fetch tooltip content");
                    }
                    return response.json();
                })
                .then((data) => {
                    const name = data.name;
                    const layer = data.layer;
                    // Initialize tooltip with retrieved content
                    initializeTooltip(name, layer);
                    tooltipInitialized = true; // Update flag
                })
                .catch((error) => {
                    console.error("Error fetching tooltip content:", error);
                });
        }
    }

    // Attach tooltip initialization when #approval.badge-warning is hovered
    $(document).on("mouseenter", "#approval.badge-warning", function () {
        fetchAndInitializeTooltip(); // Call the function to fetch and initialize tooltip
        $(this).off("mouseenter"); // Remove the event listener after initialization
    });
});
$(document).ready(function () {
    let tooltipInitialized = false; // Flag to track tooltip initialization

    // Function to initialize tippy tooltip
    function initializeTooltip(name, layer) {
        tippy("#myApproval.badge-warning", {
            content: `Manager L${layer}: ${name}`,
            placement: "right",
        });
    }

    // Function to fetch tooltip content and initialize tooltip
    function fetchAndInitializeTooltip() {
        if (!tooltipInitialized) {
            fetch("/get-tooltip-content")
                .then((response) => {
                    if (!response.ok) {
                        throw new Error("Failed to fetch tooltip content");
                    }
                    return response.json();
                })
                .then((data) => {
                    const name = data.name;
                    const layer = data.layer;
                    // Initialize tooltip with retrieved content
                    initializeTooltip(name, layer);
                    tooltipInitialized = true; // Update flag
                })
                .catch((error) => {
                    console.error("Error fetching tooltip content:", error);
                });
        }
    }

    // Attach tooltip initialization when #approval.badge-warning is hovered
    $(document).on("mouseenter", "#myApproval.badge-warning", function () {
        fetchAndInitializeTooltip(); // Call the function to fetch and initialize tooltip
        $(this).off("mouseenter"); // Remove the event listener after initialization
    });
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
    $("#myModal").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var id = button.data("id");

        // Ajax request to fetch modal content from the server
        $.ajax({
            url: "/goals/" + id,
            method: "GET",
            success: function (response) {
                $(".modal-body").html(response);
            },
        });
    });
});

$(document).ready(function () {
    const reportForm = $("#filter_form");
    const exportButton = $("#export");
    const reportContentDiv = $("#report_content");
    const customsearch = $("#customsearch");

    // Submit form event handler
    reportForm.on("submit", function (event) {
        event.preventDefault(); // Prevent default form submission behavior

        const formData = reportForm.serialize(); // Serialize form data

        // Send AJAX request to fetch and display report content
        $.ajax({
            url: "/get-report-content", // Endpoint URL to fetch report content
            method: "POST",
            data: formData, // Send serialized form data
            success: function (data) {
                reportContentDiv.html(data); // Update report content
                exportButton.removeClass("disabled"); // Enable export button
                $("#modalFilter").modal("hide");

                const reportGoalsTable = $("#reportGoalsTable").DataTable({
                    dom: "lrtip",
                    pageLength: 50,
                });
                customsearch.keyup(function () {
                    reportGoalsTable.search($(this).val()).draw();
                });
            },
            error: function (xhr, status, error) {
                console.error("Error fetching report content:", error);
                // Optionally display an error message to the user
                reportContentDiv.html(
                    "Error fetching report content. Please try again."
                );
            },
        });
    });

    // Optional: Add event listener for exportButton if needed
    exportButton.on("click", function () {
        const reportContent = reportContentDiv.html();
        // Code here to handle exporting the report content
        // console.log("Exporting report content:", reportContent);
    });
});

$(document).ready(function () {
    $("#group_company").change(function () {
        const selectedGroupCompany = $(this).val();

        // Make AJAX request to fetch updated options
        $.ajax({
            url: "/changes-group-company",
            method: "GET",
            data: { groupCompany: selectedGroupCompany },
            dataType: "json",
            success: function (data) {
                // Update options for Location select
                $("#location").html(
                    '<option value="">- select location -</option>'
                );
                $.each(data.locations, function (index, location) {
                    $("#location").append(
                        `<option value="${location.work_area}">${location.area} (${location.company_name})</option>`
                    );
                });
            },
            error: function (xhr, status, error) {
                console.error("Error fetching data:", error);
            },
        });
    });
});

function exportExcel() {
    const exportForm = $("#exportForm");
    const reportType = $("#report_type").val();
    const groupCompany = $("#group_company").val();
    const company = $("#company").val();
    const location = $("#location").val();

    $("#export_report_type").val(reportType);
    $("#export_group_company").val(groupCompany);
    $("#export_company").val(company);
    $("#export_location").val(location);

    // Submit the form
    exportForm.submit();
}

$(document).ready(function () {
    $(".select2").select2({
        theme: "bootstrap4",
    });
});
