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
function toggleInput(checkboxId, inputGroupId) {
    const checkbox = document.getElementById(checkboxId);
    const inputGroup = document.getElementById(inputGroupId);
    if (checkbox.checked) {
        inputGroup.style.display = "flex";
    } else {
        inputGroup.style.display = "none";
    }
}

function formatCurrency(input) {
    let value = input.value.replace(/\D/g, "");
    input.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

//date medical
const today = new Date();
// Set the date for two weeks ago
const twoWeeksAgo = new Date();
twoWeeksAgo.setDate(today.getDate() - 14);

// Format the dates to YYYY-MM-DD
const formattedToday = today.toISOString().split('T')[0];
const formattedTwoWeeksAgo = twoWeeksAgo.toISOString().split('T')[0];

// Set the min attribute for the input to two weeks ago
const dateInput = document.getElementById('date');
dateInput.setAttribute('min', formattedTwoWeeksAgo);
dateInput.setAttribute('max', formattedToday); // Optional: To limit selection to today
