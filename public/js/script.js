$(document).ready(function () {
    $("#taskTable").DataTable();
    $("#tableInitiate").DataTable();
    $("#reportGoalsTable").DataTable();
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
    const reportTypeSelect = $("#report_type");
    const generateButton = $("#generate");
    const exportButton = $("#export");
    const reportContentDiv = $("#report_content");

    generateButton.on("click", function () {
        const selectedValue = reportTypeSelect.val();
        if (selectedValue !== "") {
            // Send AJAX request to fetch and display report content
            $.ajax({
                url: `/get-report-content/${selectedValue}`,
                method: "GET",
                success: function (data) {
                    reportContentDiv.html(data); // Update report content
                    exportButton.removeClass("disabled"); // Show the Generate button
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching report content:", error);
                },
            });
        }
    });
});
