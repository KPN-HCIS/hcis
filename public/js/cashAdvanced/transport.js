var formCountTransport = 0;

window.addEventListener("DOMContentLoaded", function () {
    formCountTransport = document.querySelectorAll(
        "#form-container-transport > div"
    ).length;
});

$(".btn-warning").click(function (event) {
    event.preventDefault();
    var index = $(this).closest(".card-body").index() + 1;
    removeFormTransport(index, event);
});

function removeFormTransport(index, event) {
    event.preventDefault();
    if (formCountTransport > 0) {
        const formContainer = document.getElementById(
            `form-container-bt-transport-${index}`
        );
        if (formContainer) {
            const nominalInput = formContainer.querySelector(
                `#nominal_bt_transport_${index}`
            );
            if (nominalInput) {
                let nominalValue = cleanNumber(nominalInput.value);
                let total = cleanNumber(
                    document.querySelector('input[name="total_bt_transport"]')
                        .value
                );
                total -= nominalValue;
                document.querySelector(
                    'input[name="total_bt_transport"]'
                ).value = formatNumber(total);
                calculateTotalNominalBTTotal();
            }
            $(`#form-container-bt-transport-${index}`).remove();
            formCountTransport--;
        }
    }
}

function clearFormTransport(index, event) {
    event.preventDefault();
    if (formCountTransport > 0) {
        let nominalValue = cleanNumber(
            document.querySelector(`#nominal_bt_transport_${index}`).value
        );

        let total = cleanNumber(
            document.querySelector('input[name="total_bt_transport"]').value
        );
        total -= nominalValue;
        document.querySelector('input[name="total_bt_transport"]').value =
            formatNumber(total);

        let formContainer = document.getElementById(
            `form-container-bt-transport-${index}`
        );

        formContainer
            .querySelectorAll('input[type="text"], input[type="date"]')
            .forEach((input) => {
                input.value = "";
            });

        formContainer
            .querySelectorAll('input[type="number"]')
            .forEach((input) => {
                input.value = 0;
            });

        const companyCodeSelect = formContainer.querySelector(
            `#company_bt_transport_${index}`
        );
        if (companyCodeSelect) {
            companyCodeSelect.selectedIndex = 0; // Reset the select element to the default option
            var event = new Event("change");
            companyCodeSelect.dispatchEvent(event); // Trigger the change event to update the select2 component
        }

        formContainer.querySelectorAll("select").forEach((select) => {
            select.selectedIndex = 0;
        });

        formContainer.querySelectorAll("textarea").forEach((textarea) => {
            textarea.value = "";
        });

        document.querySelector(`#nominal_bt_transport_${index}`).value = 0;
        calculateTotalNominalBTTotal();
    }
}

function calculateTotalNominalBTTransport() {
    let total = 0;
    document
        .querySelectorAll('input[name="nominal_bt_transport[]"]')
        .forEach((input) => {
            total += cleanNumber(input.value); // Gunakan cleanNumber untuk parsing
        });
    document.querySelector('input[name="total_bt_transport"]').value =
        formatNumber(total); // Tampilkan dengan format
}

function onNominalChange() {
    calculateTotalNominalBTTransport();
}
