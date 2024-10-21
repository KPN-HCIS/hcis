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
    var typeToNameMap = {};
    medicalTypeData.forEach(function (type) {
        typeToNameMap[type.medical_type] = type.name;
    });

    // Function to generate dynamic forms based on selected types
    function generateDynamicForms(selectedTypes) {
        var dynamicForms = $("#dynamicForms");
        dynamicForms.empty();

        if (selectedTypes && selectedTypes.length > 0) {
            selectedTypes.forEach(function (type) {
                var balanceValue = balanceMapping[type] || ""; // Get the balance from mapping or set to empty
                var formattedValue = formatCurrency(balanceValue); // Format the initial value
                var formGroup = `
                <div class="col-md-3 mb-3">
                    <label for="${type}" class="form-label">${type}</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control currency-input" id="${type}" name="medical_costs[${type}]" placeholder="0" value="${formattedValue}" required>
                    </div>
                </div>
            `;
                dynamicForms.append(formGroup);
            });

            // Re-initialize currency formatting for new inputs
            initCurrencyFormatting();
        }
    }

    // Event listener for the medical type dropdown
    $("#medical_type").on("change", function () {
        var selectedTypes = $(this).val();
        generateDynamicForms(selectedTypes);
    });

    function initCurrencyFormatting() {
        $(".currency-input")
            .off("input")
            .on("input", function () {
                var value = $(this).val().replace(/\D/g, "");
                $(this).val(formatCurrency(value));
            });
    }

    function formatCurrency(value) {
        // Remove non-digit characters and parse as integer
        var numericValue =
            parseInt(value.toString().replace(/\D/g, ""), 10) || 0;
        // Format the number
        return new Intl.NumberFormat("id-ID").format(numericValue);
    }

    // Initialize currency formatting
    initCurrencyFormatting();

    // Step to initialize the dynamic forms on page load with selected values
    var initialSelectedTypes = $("#medical_type").val();
    generateDynamicForms(initialSelectedTypes); // Call this function to set initial forms
});

// This function is kept outside for global access if needed
function formatCurrency(input) {
    if (typeof input === "object" && input.value !== undefined) {
        // If input is an element
        let value = input.value.replace(/\D/g, "");
        if (value) {
            value = (parseInt(value, 10) || 0).toLocaleString("id-ID");
            input.value = value;
        }
    } else {
        // If input is a value
        let value = input.toString().replace(/\D/g, "");
        return (parseInt(value, 10) || 0).toLocaleString("id-ID");
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
