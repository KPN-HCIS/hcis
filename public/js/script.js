$(document).ready(function () {
    $("#taskTable").DataTable();
    $("#tableInitiate").DataTable();
});

// tippy("#approval.badge-warning", {
//     content: "L1 Manager: Douglas McGee",
//     placement: "right",
// });
$(document).ready(function () {
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

            // Initialize Tippy with the retrieved content
            tippy("#approval.badge-warning", {
                content: `Manager L${layer}: ${name}`,
                placement: "right",
            });
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

// $(document).ready(function () {
//     fetch("/units-of-measurement")
//         .then((response) => response.json())
//         .then((data) => {
//             const uomSelect = document.getElementById("uom");
//             Object.keys(data.UoM).forEach((category) => {
//                 const optgroup = document.createElement("optgroup");
//                 optgroup.label = category;
//                 data.UoM[category].forEach((unit) => {
//                     const option = document.createElement("option");
//                     option.value = unit;
//                     option.textContent = unit;
//                     optgroup.appendChild(option);
//                 });
//                 uomSelect.appendChild(optgroup);
//             });

//             // Event listener for select element
//             uomSelect.addEventListener("change", function () {
//                 const selectedValue = this.value;
//                 if (selectedValue === "Other") {
//                     // Display input field
//                     const inputField = document.createElement("input");
//                     inputField.type = "text";
//                     inputField.placeholder = "Enter UoM";
//                     inputField.id = "customUom";
//                     inputField.name = "customUom";
//                     inputField.className = "form-control mt-2";
//                     inputField.required = true;

//                     // Remove any previously displayed input field
//                     const existingInputField =
//                         document.getElementById("customUom");
//                     if (existingInputField) {
//                         existingInputField.remove();
//                     }

//                     // Append input field to the parent element of select
//                     this.parentNode.appendChild(inputField);
//                 } else {
//                     // If a value other than "Other" is selected, remove the input field if it exists
//                     const existingInputField =
//                         document.getElementById("customUom");
//                     if (existingInputField) {
//                         existingInputField.remove();
//                     }
//                 }
//             });
//         })
//         .catch((error) => {
//             console.error("Error fetching units of measurement:", error);
//         });
// });
