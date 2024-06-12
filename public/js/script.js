document.addEventListener("DOMContentLoaded", function () {
    const teamGoalsTable = new DataTable("#teamGoalsTable");

    $(".filter-btn").on("click", function () {
        const filterValue = $(this).data("id");

        if (filterValue === "all") {
            teamGoalsTable.search("").draw(); // Clear the search for 'All Task'
        } else {
            teamGoalsTable.search(filterValue).draw();
        }
    });

    $("#assignTable").DataTable({
        initComplete: function (settings, json) {
            hideLoader();
        },
    });
    $("#employeeTable").DataTable({
        initComplete: function (settings, json) {
            hideLoader();
        },
    });
    $("#historyTable").DataTable({
        initComplete: function (settings, json) {
            hideLoader();
        },
        dom: "frtip",
    });
    $("#tableInitiate").DataTable({
        initComplete: function (settings, json) {
            hideLoader();
        },
    });

    const layerTable = $("#layerTable").DataTable({
        dom: "lrtip",
        pageLength: 50,
        initComplete: function (settings, json) {
            hideLoader();
        },
    });

    const scheduleTable = $("#scheduleTable").DataTable({
        dom: "lrtip",
        pageLength: 50,
    });

    const goalTable = $("#goalTable").DataTable({
        dom: "lrtip",
        pageLength: 50,
    });

    $("#customsearch").on("keyup", function () {
        goalTable.search($(this).val()).draw();
        layerTable.search($(this).val()).draw();
        scheduleTable.search($(this).val()).draw();
    });
});

document.addEventListener("DOMContentLoaded", function () {
    let popoverInitialized = false; // Flag to track popover initialization
    let previousPopoverId = null; // Variable to store the ID of the previously triggered popover
    let popoverTimeout = null; // Variable to store the timeout for hiding the popover

    // Function to initialize Bootstrap popover with delay
    function initializePopover(name, layer, element) {
        const contents = layer ? `Manager L${layer} : ${name}` : name;
        $(element)
            .popover({
                content: contents,
                // trigger: "manual", // Show popover manually
                trigger: "focus",
                placement: "top", // Auto placement (adjusts as needed)
            })
            .popover("show"); // Show the popover immediately

        // Set a timeout to hide the popover after 1.5 seconds
        popoverTimeout = setTimeout(function () {
            $(element).popover("hide"); // Hide the popover
            popoverInitialized = false; // Reset popoverInitialized flag
            $(element).blur();
        }, 1500); // 1500 milliseconds = 1.5 seconds
    }

    // Function to fetch popover content and initialize popover
    function fetchAndInitializePopover(id, element) {
        // Check if the current id is the same as the previousPopoverId
        if (id === previousPopoverId && popoverInitialized) {
            // If same id and popover is already initialized, return early
            return;
        }

        let url = `/get-tooltip-content?id=${id}`;
        fetch(url)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Failed to fetch popover content");
                }
                return response.json();
            })
            .then((data) => {
                const name = data.name;
                const layer = data.layer;
                // Initialize popover with retrieved content
                initializePopover(name, layer, element);
                popoverInitialized = true; // Update flag
                previousPopoverId = id; // Update the previousPopoverId with the current ID
            })
            .catch((error) => {
                console.error("Error fetching popover content:", error);
            });
    }

    // Attach popover initialization when #approval.badge-warning is clicked (for mobile)
    $(document).on("click mouseenter", "a[id^='approval']", function () {
        $('[data-toggle="popover"]').popover("hide");

        var id = $(this).data("bsId");

        // Call fetchAndInitializePopover function to fetch and initialize popover
        fetchAndInitializePopover(id, this);
    });

    // Function to cancel popover timeout when popover is manually closed
    $(document).on("hidden.bs.popover", function () {
        clearTimeout(popoverTimeout); // Clear the timeout
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Function to handle "Select All" button click
    $("#select-all").on("click", function () {
        $(".day-button").toggleClass("active");
        $("#select-all").toggleClass("active");
    });

    // Function to handle individual day button clicks
    $(".day-button").on("click", function () {
        $(this).toggleClass("active");
        if ($(".day-button.active").length === 7) {
            $("#select-all").addClass("active");
        } else {
            $("#select-all").removeClass("active");
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Hide the .reminders element initially
    // $('.reminders').hide();

    // Toggle the hidden attribute of .reminders based on the checkbox state
    $("#checkbox_reminder").on("change", function () {
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
    Swal.fire({
        title: "Confirm Logout",
        text: "Are you sure you want to log out?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3e60d5",
        cancelButtonColor: "#f15776",
        confirmButtonText: "Yes, logout",
        cancelButtonText: "Cancel",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "/logout";
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
            });
            Toast.fire({
                icon: "success",
                title: "You are now logged out.",
            });
        }
    });
}

document.addEventListener("DOMContentLoaded", function () {
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

document.addEventListener("DOMContentLoaded", function () {
    const reportForm = $("#report_filter");
    const exportButton = $("#export");
    const reportContentDiv = $("#report_content");
    const customsearch = $("#customsearch");

    // Submit form event handler
    reportForm.on("submit", function (event) {
        event.preventDefault(); // Prevent default form submission behavior

        const formData = reportForm.serialize(); // Serialize form data

        showLoader();

        // Send AJAX request to fetch and display report content
        $.ajax({
            url: "/get-report-content", // Endpoint URL to fetch report content
            method: "POST", // Use POST method
            data: formData, // Send serialized form data
            success: function (data) {
                reportContentDiv.html(data); // Update report content with the returned HTML
                exportButton.removeClass("disabled"); // Enable export button

                const reportGoalsTable = $("#reportGoalsTable").DataTable({
                    dom: "lrtip",
                    pageLength: 50,
                });
                customsearch.on("keyup", function () {
                    reportGoalsTable.search($(this).val()).draw();
                });

                $(".filter-btn").on("click", function () {
                    const filterValue = $(this).data("id");

                    if (filterValue === "all") {
                        reportGoalsTable.search("").draw(); // Clear the search for 'All Task'
                    } else {
                        reportGoalsTable.search(filterValue).draw();
                    }
                });
                hideLoader();

                $("#offcanvas-cancel").click();
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

document.addEventListener("DOMContentLoaded", function () {
    const reportForm = $("#admin_report_filter");
    const exportButton = $("#export");
    const reportContentDiv = $("#report_content");
    const customsearch = $("#customsearch");

    // Submit form event handler
    reportForm.on("submit", function (event) {
        event.preventDefault(); // Prevent default form submission behavior
        const formData = reportForm.serialize(); // Serialize form data
        showLoader();

        // Send AJAX request to fetch and display report content
        $.ajax({
            url: "/admin/get-report-content", // Endpoint URL to fetch report content
            method: "POST",
            data: formData, // Send serialized form data
            success: function (data) {
                reportContentDiv.html(data); // Update report content
                exportButton.removeClass("disabled"); // Enable export button

                const reportGoalsTable = $("#adminReportTable").DataTable({
                    dom: "lrtip",
                    pageLength: 50,
                });
                customsearch.on("keyup", function () {
                    reportGoalsTable.search($(this).val()).draw();
                });

                $(".filter-btn").on("click", function () {
                    const filterValue = $(this).data("id");

                    if (filterValue === "all") {
                        reportGoalsTable.search("").draw(); // Clear the search for 'All Task'
                    } else {
                        reportGoalsTable.search(filterValue).draw();
                    }
                });
                hideLoader();

                $("#offcanvas-cancel").click();
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

document.addEventListener("DOMContentLoaded", function () {
    const form = $("#onbehalf_filter");
    const contentOnBehalf = $("#contentOnBehalf");
    const customsearch = $("#customsearch");

    // Submit form event handler
    form.on("submit", function (event) {
        event.preventDefault(); // Prevent default form submission behavior
        const formData = form.serialize();
        showLoader();

        // Send AJAX request to fetch and display report content
        $.ajax({
            url: "/admin/onbehalf/content", // Endpoint URL to fetch report content
            method: "POST",
            data: formData, // Send serialized form data
            success: function (data) {
                contentOnBehalf.html(data); // Update report content

                const onBehalfTable = $("#onBehalfTable").DataTable({
                    dom: "lrtip",
                    pageLength: 25,
                });
                customsearch.on("keyup", function () {
                    onBehalfTable.search($(this).val()).draw();
                });

                $(".filter-btn").on("click", function () {
                    const filterValue = $(this).data("id");

                    if (filterValue === "all") {
                        onBehalfTable.search("").draw(); // Clear the search for 'All Task'
                    } else {
                        onBehalfTable.search(filterValue).draw();
                    }
                });
                hideLoader();

                $("#offcanvas-cancel").click();
            },
            error: function (xhr, status, error) {
                console.error("Error fetching report content:", error);
                // Optionally display an error message to the user
                contentOnBehalf.html(
                    "Error fetching report content. Please try again."
                );
            },
        });
    });
});

function changeCategory(val) {
    $("#filter_category").val(val);

    const form = $("#onbehalf_filter");
    const contentOnBehalf = $("#contentOnBehalf");
    const customsearch = $("#customsearch");
    const formData = form.serialize();

    showLoader();

    $.ajax({
        url: "/admin/onbehalf/content", // Endpoint URL to fetch report content
        method: "POST",
        data: formData, // Send serialized form data
        success: function (data) {
            //alert(data);
            contentOnBehalf.html(data); // Update report content

            const onBehalfTable = $("#onBehalfTable").DataTable({
                dom: "lrtip",
                pageLength: 25,
            });
            customsearch.keyup(function () {
                onBehalfTable.search($(this).val()).draw();
            });

            $(".filter-btn").on("click", function () {
                const filterValue = $(this).data("id");

                if (filterValue === "all") {
                    onBehalfTable.search("").draw(); // Clear the search for 'All Task'
                } else {
                    onBehalfTable.search(filterValue).draw();
                }
            });
            hideLoader();
        },
        error: function (xhr, status, error) {
            console.error("Error fetching report content:", error);
            // Optionally display an error message to the user
            contentOnBehalf.html("");
        },
    });
    return; // Prevent default form submission
}

document.addEventListener("DOMContentLoaded", function () {
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
                if (xhr.status === 401) {
                    Swal.fire({
                        icon: "error",
                        title: "Your Session is Ended",
                        text: "Login first.",
                    }).then(() => {
                        // Redirect to the home page after the SweetAlert is dismissed
                        window.location.href = "/"; // Adjust the home page URL as needed
                    });
                } else {
                    console.error("Error fetching data:", error);
                }
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

function getAssignmentData(id) {
    const subContent = $("#subContent");
    // Send AJAX request to fetch and display report content
    $.ajax({
        url: "/admin/roles/get-assignment", // Endpoint URL to fetch report content
        method: "GET",
        data: { roleId: id }, // Send serialized form data
        success: function (data) {
            subContent.html(data); // Update report content
            $(".select2").select2({
                theme: "bootstrap-5",
            });
            $("#modalFilter").modal("hide");
        },
        error: function (xhr, status, error) {
            console.error("Error fetching data:", error);
            // Optionally display an error message to the user
            subContent.html("Error fetching data. Please try again.");
        },
    });
}

function getPermissionData(id) {
    const subContent = $("#subContent");
    // Send AJAX request to fetch and display report content
    $.ajax({
        url: "/admin/roles/get-permission", // Endpoint URL to fetch report content
        method: "GET",
        data: { roleId: id }, // Send serialized form data
        success: function (data) {
            subContent.html(data); // Update report content
            $(".select2").select2({
                theme: "bootstrap-5",
            });
            $("#modalFilter").modal("hide");
        },
        error: function (xhr, status, error) {
            console.error("Error fetching data:", error);
            // Optionally display an error message to the user
            subContent.html("Error fetching data. Please try again.");
        },
    });
}

function yearGoal() {
    $("#formYearGoal").submit();
}

$(".select2").select2({
    theme: "bootstrap-5",
});

function showLoader() {
    $("#preloader").show();
}

function hideLoader() {
    $("#preloader").hide();
}

window.onload = function () {
    hideLoader();
};

function deleteRole() {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3e60d5",
        cancelButtonColor: "#f15776",
        confirmButtonText: "Yes, delete it!",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("delete-role-form").submit();
        }
    });
}
