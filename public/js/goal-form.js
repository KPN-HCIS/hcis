function otherUom(index) {
    const uomSelect = $("#uom" + index).val();
    // Event listener for select element
    const inputField = $("#custom_uom" + index);
    if (uomSelect === "Other") {
        // Display input field
        inputField.show(); // Show the input field
        inputField.prop("required", true); // Set input as required
    } else {
        inputField.hide(); // Hide the input field
        inputField.prop("required", false); // Remove required attribute
    }
}
$(document).ready(function () {
    // Get the value of the hidden input
    var managerId = $('input[name="manager_id"]').val();

    // Check if managerId is empty or not assigned
    if (managerId == "") {
        // Show SweetAlert alert
        Swal.fire({
            title: "No direct manager is assigned!",
            text: "Please contact admin to assign your manager",
            icon: "error",
            closeOnClickOutside: false, // Prevent closing by clicking outside alert
        }).then(function () {
            // Redirect back
            window.history.back();
        });
    }
});

var wrapper = $(".container-card"); // Fields wrapper

var x = 1; // initial text box count

// Function to fetch UoM data and populate select element
function populateUoMSelect(select) {
    fetch("/units-of-measurement")
        .then((response) => response.json())
        .then((data) => {
            Object.keys(data.UoM).forEach((category) => {
                const optgroup = $("<optgroup></optgroup>").attr(
                    "label",
                    category
                );
                data.UoM[category].forEach((unit) => {
                    var option = $("<option></option>")
                        .attr("value", unit)
                        .text(unit);
                    optgroup.append(option);
                });
                $(select).append(optgroup);
            });
        })
        .catch((error) => {
            console.error("Error fetching units of measurement:", error);
        });
}

function addField(val) {
    var count = $("#count").val();
    var max_fields = val === "input" ? 10 : 10 - count; // maximum input boxes allowed
    // on add input button click
    if (x < max_fields) {
        // max input box allowed
        x++; // text box increment
        $(wrapper).append(
            '<div class="card col-md-12 mb-4 shadow-sm">' +
                ' <div class="card-header border-0 p-0 bg-white d-flex align-items-center justify-content-between"><h1 class="rotate-n-45 text-primary"><i class="fas fa-angle-up p-0"></i></h1><a class="btn btn-danger btn-sm btn-circle remove_field"><i class="fas fa-times"></i></a></div>' +
                '<div class="card-body pt-0">' +
                '<div class="row mx-auto">' +
                '<div class="col-md-4">' +
                '<div class="form-group">' +
                '<label for="kpi">KPI</label>' +
                '<textarea name="kpi[]" id="kpi" class="form-control" required></textarea>' +
                "</div>" +
                "</div>" +
                '<div class="col-md-2">' +
                '<div class="form-group">' +
                '<label for="target">Target</label><input type="text" name="target[]" id="target" class="form-control" required>' +
                "</div>" +
                "</div>" +
                '<div class="col-md-2">' +
                '<div class="form-group">' +
                '<label for="uom">UoM</label>' +
                '<select class="form-control uom-select" name="uom[]" id="uom' +
                x +
                '"onchange="otherUom(' +
                x +
                ')" title="Unit of Measure" required>' +
                '<option value="">- Select -</option>' +
                '</select><input type="text" name="custom_uom[]" id="custom_uom' +
                x +
                '" class="form-control mt-2" placeholder="Enter UoM" style="display: none" placeholder="Enter UoM">' +
                "</div>" +
                "</div>" +
                '<div class="col-md-2">' +
                '<div class="form-group">' +
                '<label for="weightage">Weightage</label>' +
                '<div class="input-group">' +
                '<input type="number" min="5" max="100" class="form-control" name="weightage[]" value="{{ old("weightage") }}" required>' +
                '<div class="input-group-append">' +
                '<span class="input-group-text">%</span>' +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>" +
                '<div class="col-md-2">' +
                '<div class="form-group">' +
                '<label for="type">Type</label>' +
                '<select class="form-control" name="type[]" id="type" required>' +
                '<option value="">- Select -</option>' +
                '<option value="Higher is better">Higher is better</option>' +
                '<option value="Lower is better">Lower is better</option>' +
                '<option value="Exact value">Exact value</option>' +
                "</select>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>" +
                "</div>"
        ); // add input box
        // Populate UoM select for the newly added field
        var newSelect = $("#uom" + x); // Assuming your select has an ID like "uom1", "uom2", ...
        populateUoMSelect(newSelect);
    } else {
        Swal.fire({
            title: "Oops, you've exceeded the maximum KPI inputs",
            icon: "error",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK",
        });
    }
}

$(wrapper).on("click", ".remove_field", function (e) {
    // user click on remove text
    e.preventDefault();
    $(this).closest(".card").remove();
    x--;
});

// Event listener for select element
// $(wrapper).on("change", ".uom-select", function () {
//     const selectedValue = $(this).val();
//     if (selectedValue === "Other") {
//         // Display input field
//         const inputField = $(
//             '<input type="text" name="custom_uom[]" class="form-control mt-2 custom-measurement" placeholder="Enter UoM" required>'
//         );

//         // Remove any previously displayed input field
//         $(this).closest(".row").find(".custom-measurement").remove();

//         // Append input field to the parent element of select
//         $(this).closest(".form-group").append(inputField);
//     } else {
//         // If a value other than "Others" is selected, remove the input field if it exists
//         $(this).closest(".row").find(".custom-measurement").remove();
//     }
// });

var firstSelect = $("#uom"); // Assuming your first select has an ID "uom1"
populateUoMSelect(firstSelect);

function checkEmptyFields(submitType) {
    if (submitType === "submit_form") {
        var requiredInputs = document.querySelectorAll(
            "input[required], select[required]"
        );
        for (var i = 0; i < requiredInputs.length; i++) {
            if (requiredInputs[i].value.trim() === "") {
                Swal.fire({
                    title: "Please fill out all empty fields!",
                    confirmButtonColor: "#3085d6",
                    icon: "error",
                    // If confirmed, proceed with form submission
                });
                return false; // Prevent form submission
            }
        }
        return true; // All required fields are filled
    }
    return true; // All required fields are filled
}

function validate() {
    var weight = document.querySelectorAll('input[name="weightage[]"]');
    var sum = 0;
    for (var i = 0; i < weight.length; i++) {
        sum += parseInt(weight[i].value) || 0; // Parse input value to integer, default to 0 if NaN
    }

    if (sum > 100) {
        Swal.fire({
            title: "The total weightage cannot exceed 100%",
            confirmButtonColor: "#3085d6",
            icon: "error",
            // If confirmed, proceed with form submission
        });
        return false; // Prevent form submission
    }

    return true; // Allow form submission
}

function setSubmitType(submitType) {
    document.getElementById("submitType").value = submitType; // Set the value of the hidden input field
    // Now you can call the confirmSubmission() function to show the confirmation dialog
    // Check for empty required fields
    if (!checkEmptyFields(submitType)) {
        return false; // Stop submission if required fields are empty
    }
    if (!validate()) {
        return false; // Stop submission if required fields are empty
    }
    return confirmSubmission(submitType);
}

function confirmSubmission(submitType) {
    let title1;
    let title2;
    let text;
    let confirmText;

    if (submitType === "save_draft") {
        title1 = "Do you want to save this form?";
        title2 = "Form saved successfuly!";
        text = "Your data will be saved as draft";
        confirmText = "Save";
    } else {
        title1 = "Do you want to submit?";
        title2 = "KPI submitted successfuly!";
        text = "You won't be able to revert this!";
        confirmText = "Submit";
    }

    Swal.fire({
        title: title1,
        text: text,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: confirmText,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("goalForm").submit();
            Swal.fire({
                title: title2,
                icon: "success",
                showConfirmButton: false,
                // If confirmed, proceed with form submission
            });
        }
    });

    return false; // Prevent default form submission
}
