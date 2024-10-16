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
