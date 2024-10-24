//medical table
$("#example").DataTable({
    responsive: {
        details: {
            type: "column",
            target: "tr",
        },
    },
    columnDefs: [
        {
            className: "control",
            orderable: false,
            targets: 0,
        },
        {
            className: "none", // This will hide Disease and the 4 dynamic columns
            targets: [8, 9, 10, 11, 12], // Disease (8) and the 4 dynamic medical columns (9-12)
        },
        {
            responsivePriority: 1,
            targets: 0,
        },

        {
            responsivePriority: 4,
            targets: 3,
        },
    ],
    order: [1, "asc"],
    pageLength: 5,
    lengthMenu: [5, 10, 25, 50],
});

//Medical Form JS
$(document).ready(function () {
    // Map medical type IDs or keys to names
    var typeToNameMap = {};
    medicalPlanData.forEach(function (plan) {
        typeToNameMap[plan.medical_type] = plan.medical_type_name; // Adjust according to your medical type naming convention
    });

    // Function to populate the form with data from medicalPlanData
    function populateBalanceForm() {
        var dynamicForms = $("#balanceForm");
        dynamicForms.empty(); // Clear any previous forms

        // Loop through the medicalPlanData to create form fields
        medicalPlanData.forEach(function (plan) {
            var balance = plan.balance || 0; // Default to 0 if not found

            // Create a form group for each medical plan
            var formGroup = `
            <div class="col-md-3 mb-3">
                <label for="${plan.medical_type}" class="form-label">${
                typeToNameMap[plan.medical_type]
            }</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control currency-input" id="${
                        plan.medical_type
                    }"
                        name="medical_costs[${
                            plan.medical_type
                        }]" value="${balance}" readonly>
                </div>
            </div>
            `;
            dynamicForms.append(formGroup); // Append the form group to the dynamic forms
        });

        // Re-initialize currency formatting for new inputs
        initCurrencyFormatting();
    }

    function initCurrencyFormatting() {
        $(".currency-input").each(function () {
            var value = $(this).val().replace(/\D/g, ""); // Remove non-digit characters
            $(this).val(formatCurrency(value)); // Format the currency
        });
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat("id-ID").format(value); // Format the number as currency
    }

    // Initial population of the balance form when the document is ready
    populateBalanceForm();
});

$(document).ready(function () {
    var typeToNameMap = {};
    medicalTypeData.forEach(function (type) {
        typeToNameMap[type.medical_type] = type.name;
    });

    $("#medical_type").on("change", function () {
        var selectedTypes = $(this).val();
        var dynamicForms = $("#dynamicForms");
        dynamicForms.empty();

        if (selectedTypes && selectedTypes.length > 0) {
            selectedTypes.forEach(function (type) {
                var formGroup = `
                <div class="col-md-3 mb-3">
                    <label for="${type}" class="form-label">${type}</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control currency-input" id="${type}" name="medical_costs[${type}]" placeholder="0" required>
                    </div>
                </div>
            `;
                dynamicForms.append(formGroup);
            });

            // Re-initialize currency formatting for new inputs
            initCurrencyFormatting();
        }
    });

    function initCurrencyFormatting() {
        $(".currency-input").on("input", function () {
            var value = $(this).val().replace(/\D/g, "");
            $(this).val(formatCurrency(value));
        });
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat("id-ID").format(value);
    }

    // Initialize currency formatting
    initCurrencyFormatting();
});

function formatCurrency(input) {
    // Your currency formatting logic here
    let value = input.value.replace(/\D/g, ""); // Remove non-digit characters
    if (value) {
        value = (parseInt(value, 10) || 0).toLocaleString("id-ID"); // Format number
        input.value = value;
    }
}

//date medical
const today = new Date();
// Set the date for two weeks ago
const twoWeeksAgo = new Date();
twoWeeksAgo.setDate(today.getDate() - 60);

// Format the dates to YYYY-MM-DD
const formattedToday = today.toISOString().split("T")[0];
const formattedTwoWeeksAgo = twoWeeksAgo.toISOString().split("T")[0];

// Set the min attribute for the input to two weeks ago
const dateInput = document.getElementById("date");
dateInput.setAttribute("min", formattedTwoWeeksAgo);
dateInput.setAttribute("max", formattedToday); // Optional: To limit selection to today
