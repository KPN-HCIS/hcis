document.addEventListener("DOMContentLoaded", function () {
    handleTicketForms();
    handleHotelForms();
    handleTaksiForms();
});

function toggleSection(checkboxId, navId, tabId) {
    const checkbox = document.getElementById(checkboxId);
    const nav = document.getElementById(navId);
    const tab = document.getElementById(tabId); // The tab itself (button)

    checkbox.addEventListener("change", function () {
        if (this.checked) {
            nav.style.display = "block";
            tab.click(); // Programmatically activate the tab
        } else {
            nav.style.display = "none";
        }
    });
}

// Initialize toggling for each checkbox and tab
toggleSection(
    "cashAdvancedCheckbox",
    "nav-cash-advanced",
    "pills-cash-advanced-tab"
);
toggleSection("ticketCheckbox", "nav-ticket", "pills-ticket-tab");
toggleSection("hotelCheckbox", "nav-hotel", "pills-hotel-tab");
toggleSection("taksiCheckbox", "nav-taksi", "pills-taksi-tab");

function formatCurrency(input) {
    var cursorPos = input.selectionStart;
    var value = input.value.replace(/[^\d]/g, "");

    // Format the number with thousands separators
    var formattedValue = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    // Update the input value
    input.value = formattedValue;

    // Adjust cursor position
    cursorPos += formattedValue.length - value.length;

    // Set the cursor position
    input.setSelectionRange(cursorPos, cursorPos);
}
document.getElementById("btFrom").addEventListener("submit", function (event) {
    // Unformat the voucher fields before submission
    var nominalField = document.getElementById("nominal_vt");
    var keeperField = document.getElementById("keeper_vt");

    // Remove dots from the formatted value
    nominalField.value = nominalField.value.replace(/\./g, "");
    keeperField.value = keeperField.value.replace(/\./g, "");
});

//save to draft
document.addEventListener("DOMContentLoaded", function () {
    document
        .getElementById("save-draft")
        .addEventListener("click", function (event) {
            event.preventDefault();

            // Remove the existing status input
            const existingStatus = document.getElementById("status");
            if (existingStatus) {
                existingStatus.remove();
            }

            // Create a new hidden input for "Draft"
            const draftInput = document.createElement("input");
            draftInput.type = "hidden";
            draftInput.name = "status";
            draftInput.value = "Draft";
            draftInput.id = "status";

            // Append the draft input to the form
            this.closest("form").appendChild(draftInput);

            // Submit the form
            this.closest("form").submit();
        });
});

// VALIDATION DATE JS SECTIONS
//BT Validation Date
function validateStartEndDates() {
    const startDateInput = document.getElementById("mulai");
    const endDateInput = document.getElementById("kembali");

    if (startDateInput && endDateInput) {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (endDate < startDate) {
            alert("End Date cannot be earlier than the Start Date.");
            endDateInput.value = ""; // Reset the end date if it's invalid
        }
    }
}

//BT Toggle Other Locations
function BTtoggleOthers() {
    var locationFilter = document.getElementById("tujuan");
    var others_location = document.getElementById("others_location");

    if (locationFilter.value === "Others") {
        others_location.style.display = "block";
    } else {
        others_location.style.display = "none";
        others_location.value = "";
    }
}

//Ticket Validation Date
function validateDates(index) {
    const departureDateInput = document.getElementById(`tgl_brkt_tkt_${index}`);
    const returnDateInput = document.getElementById(`tgl_plg_tkt_${index}`);
    const departureTimeInput = document.getElementById(`jam_brkt_tkt_${index}`);
    const returnTimeInput = document.getElementById(`jam_plg_tkt_${index}`);

    if (
        departureDateInput &&
        returnDateInput &&
        departureTimeInput &&
        returnTimeInput
    ) {
        const departureDate = new Date(departureDateInput.value);
        const returnDate = new Date(returnDateInput.value);

        // Check if return date is earlier than departure date
        if (returnDate < departureDate) {
            alert("Return date cannot be earlier than the departure date.");
            returnDateInput.value = ""; // Reset the return date if it's invalid
            return; // Stop further validation
        }

        // If the dates are the same, check the times
        if (returnDate.getTime() === departureDate.getTime()) {
            const departureTime = departureTimeInput.value;
            const returnTime = returnTimeInput.value;

            if (departureTime && returnTime && returnTime < departureTime) {
                alert("Return time cannot be earlier than the departure time.");
                returnTimeInput.value = ""; // Reset the return time if it's invalid
            }
        }
    }
}

//Hotel Validation Date
function calculateTotalDays(index) {
    const checkInInput = document.querySelector(
        `#hotel-form-${index} input[name="tgl_masuk_htl[]"]`
    );
    const checkOutInput = document.querySelector(
        `#hotel-form-${index} input[name="tgl_keluar_htl[]"]`
    );
    const totalDaysInput = document.querySelector(
        `#hotel-form-${index} input[name="total_hari[]"]`
    );

    // Get Start Date and End Date from the main form
    const mulaiInput = document.getElementById("mulai");
    const kembaliInput = document.getElementById("kembali");

    if (!checkInInput || !checkOutInput || !mulaiInput || !kembaliInput) {
        return; // Ensure elements are present before proceeding
    }

    // Parse the dates
    const checkInDate = new Date(checkInInput.value);
    const checkOutDate = new Date(checkOutInput.value);
    const mulaiDate = new Date(mulaiInput.value);
    const kembaliDate = new Date(kembaliInput.value);

    // Validate Check In Date
    if (checkInDate < mulaiDate) {
        alert("Check In date cannot be earlier than Start date.");
        checkInInput.value = ""; // Reset the Check In field
        totalDaysInput.value = ""; // Clear total days
        return;
    }
    if (checkInDate > kembaliDate) {
        alert("Check In date cannot be more than End date.");
        checkInInput.value = ""; // Reset the Check In field
        totalDaysInput.value = ""; // Clear total days
        return;
    }

    // Ensure Check Out Date is not earlier than Check In Date
    if (checkOutDate < checkInDate) {
        alert("Check Out date cannot be earlier than Check In date.");
        checkOutInput.value = ""; // Reset the Check Out field
        totalDaysInput.value = ""; // Clear total days
        return;
    }

    // Calculate the total days if all validations pass
    if (checkInDate && checkOutDate) {
        const diffTime = Math.abs(checkOutDate - checkInDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        totalDaysInput.value = diffDays;
    } else {
        totalDaysInput.value = "";
    }
}

// Attach event listeners to the hotel forms
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".hotel-form").forEach((form, index) => {
        const i = index + 1; // Adjust for 1-based index

        form.querySelector('input[name="tgl_masuk_htl[]"]').addEventListener(
            "change",
            () => calculateTotalDays(i)
        );
        form.querySelector('input[name="tgl_keluar_htl[]"]').addEventListener(
            "change",
            () => calculateTotalDays(i)
        );
    });
});

//RESET CHECKBOX FIELDS
document.addEventListener("DOMContentLoaded", function () {
    var jnsDinasSelect = document.getElementById("jns_dinas");
    var additionalFields = document.getElementById("additional-fields");

    var checkboxes = [
        "cashAdvancedCheckbox",
        "ticketCheckbox",
        "hotelCheckbox",
        "taksiCheckbox",
    ];

    // Corresponding section divs to hide/reset
    var sections = [
        "nav-cash-advanced",
        "nav-ticket",
        "nav-hotel",
        "nav-taksi",
    ];

    jnsDinasSelect.addEventListener("change", function () {
        if (this.value === "luar kota") {
            additionalFields.style.display = "block";
        } else {
            additionalFields.style.display = "none";

            // Uncheck all the checkboxes and hide/reset related fields
            checkboxes.forEach(function (checkboxId) {
                var checkbox = document.getElementById(checkboxId);
                if (checkbox.checked) {
                    checkbox.checked = false; // Uncheck the checkbox
                    // Trigger the change event to ensure corresponding sections are hidden
                    checkbox.dispatchEvent(new Event("change"));
                }
            });
        }
    });
});

// Function to toggle the visibility of sections based on checkboxes
function toggleSection(checkboxId, navId, tabId) {
    const checkbox = document.getElementById(checkboxId);
    const nav = document.getElementById(navId);
    const tab = document.getElementById(tabId); // The tab button (anchor) for navigation

    checkbox.addEventListener("change", function () {
        if (this.checked) {
            nav.style.display = "block";
            tab.click(); // Programmatically activate the tab
        } else {
            nav.style.display = "none";
        }
    });
}

// Initialize toggling for each checkbox and tab
toggleSection(
    "cashAdvancedCheckbox",
    "nav-cash-advanced",
    "pills-cash-advanced-tab"
);
toggleSection("ticketCheckbox", "nav-ticket", "pills-ticket-tab");
toggleSection("hotelCheckbox", "nav-hotel", "pills-hotel-tab");
toggleSection("taksiCheckbox", "nav-taksi", "pills-taksi-tab");

document.addEventListener("DOMContentLoaded", function () {
    // Elements
    const caSelect = document.getElementById("ca");
    const caNbtDiv = document.getElementById("ca_div");

    const hotelSelect = document.getElementById("hotel");
    const hotelDiv = document.getElementById("hotel_div");

    const taksiSelect = document.getElementById("taksi");
    const taksiDiv = document.getElementById("taksi_div");

    const tiketSelect = document.getElementById("tiket");
    const tiketDiv = document.getElementById("tiket_div");

    // Function to reset fields in the target div
    function resetFields(container) {
        const inputs = container.querySelectorAll(
            'input[type="text"], input[type="number"], textarea'
        );
        inputs.forEach((input) => {
            input.value = "";
        });
        const selects = container.querySelectorAll("select");
        selects.forEach((select) => {
            select.selectedIndex = 0;
        });
    }

    // Function to toggle display and reset fields
    function toggleDisplay(selectElement, targetDiv) {
        if (selectElement.value === "Ya") {
            targetDiv.style.display = "block";
        } else {
            targetDiv.style.display = "none";
            resetFields(targetDiv); // Reset fields when hiding the target div
        }
    }

    // Event listeners for select elements
    caSelect.addEventListener("change", function () {
        toggleDisplay(caSelect, caNbtDiv);
    });

    hotelSelect.addEventListener("change", function () {
        toggleDisplay(hotelSelect, hotelDiv);
    });

    taksiSelect.addEventListener("change", function () {
        toggleDisplay(taksiSelect, taksiDiv);
    });

    tiketSelect.addEventListener("change", function () {
        toggleDisplay(tiketSelect, tiketDiv);
    });
});

//Ticket JS
function handleTicketForms() {
    const ticketCheckbox = document.getElementById("ticketCheckbox");
    const ticketFormsContainer = document.getElementById("tiket_div");
    const maxTickets = 5;

    if (ticketCheckbox) {
        ticketCheckbox.addEventListener("change", function () {
            if (this.checked) {
                ticketFormsContainer.style.display = "block";
            } else {
                ticketFormsContainer.style.display = "none";
                resetAllTicketForms();
            }
        });
    }

    function resetTicketFields(container) {
        const inputs = container.querySelectorAll(
            'input[type="text"], input[type="number"], input[type="date"], input[type="time"], textarea'
        );
        inputs.forEach((input) => (input.value = ""));

        const selects = container.querySelectorAll("select");
        selects.forEach((select) => {
            if ($(select).data("select2")) {
                $(select).val(null).trigger("change");
            } else {
                select.value = select.querySelector("option[selected]")
                    ? select.querySelector("option[selected]").value
                    : select.querySelector("option").value;
            }
        });

        // Reset the round-trip options
        const roundTripOptions = container.querySelector(".round-trip-options");
        if (roundTripOptions) {
            roundTripOptions.style.display = "none"; // Hide round-trip input by default
        }
    }

    function resetAllTicketForms() {
        const forms = ticketFormsContainer.querySelectorAll(
            '[id^="ticket-form-"]'
        );
        forms.forEach((form, index) => {
            resetTicketFields(form);
            if (index === 0) {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        });
        updateFormNumbers();
        updateButtonVisibility();
    }

    function updateFormNumbers() {
        const visibleForms = Array.from(
            ticketFormsContainer.querySelectorAll(
                '[id^="ticket-form-"]:not([style*="display: none"])'
            )
        );
        visibleForms.sort((a, b) => {
            return a.dataset.formIndex - b.dataset.formIndex;
        });
        visibleForms.forEach((form, index) => {
            const titleElement = form.querySelector(".h5.text-uppercase b");
            if (titleElement) {
                titleElement.textContent = `TICKET ${index + 1}`;
            }
            form.dataset.visibleIndex = index.toString();
        });
    }

    function updateButtonVisibility() {
        const visibleForms = ticketFormsContainer.querySelectorAll(
            '[id^="ticket-form-"]:not([style*="display: none"])'
        );
        visibleForms.forEach((form, index) => {
            const addButton = form.querySelector(".add-ticket-btn");
            const removeButton = form.querySelector(".remove-ticket-btn");

            if (addButton) {
                addButton.style.display =
                    index === visibleForms.length - 1 &&
                    visibleForms.length < maxTickets
                        ? "inline-block"
                        : "none";
            }

            if (removeButton) {
                removeButton.style.display =
                    visibleForms.length > 1 ? "inline-block" : "none";
            }
        });
    }

    function removeForm(formToRemove) {
        formToRemove.style.display = "none";
        resetTicketFields(formToRemove);
        updateFormNumbers();
        updateButtonVisibility();
    }

    function addForm() {
        const forms = Array.from(
            ticketFormsContainer.querySelectorAll('[id^="ticket-form-"]')
        );
        const visibleForms = forms.filter(
            (form) => form.style.display !== "none"
        );

        if (visibleForms.length < maxTickets) {
            const highestIndex = Math.max(
                ...forms.map((form) => parseInt(form.dataset.formIndex) || 0)
            );

            const hiddenForm = forms.find(
                (form) => form.style.display === "none"
            );

            if (hiddenForm) {
                hiddenForm.style.display = "block";
                hiddenForm.dataset.formIndex = (highestIndex + 1).toString();
                ticketFormsContainer.appendChild(hiddenForm);

                // Hide round-trip input by default for the newly added form
                const roundTripOptions = hiddenForm.querySelector(
                    ".round-trip-options"
                );
                if (roundTripOptions) {
                    roundTripOptions.style.display = "none";
                }
            }

            updateFormNumbers();
            updateButtonVisibility();
        }
    }

    // Initialize form indices
    ticketFormsContainer
        .querySelectorAll('[id^="ticket-form-"]')
        .forEach((form, index) => {
            form.dataset.formIndex = index.toString();
        });

    ticketFormsContainer.addEventListener("click", function (e) {
        if (e.target.classList.contains("add-ticket-btn")) {
            addForm();
        } else if (e.target.classList.contains("remove-ticket-btn")) {
            const formToRemove = e.target.closest('[id^="ticket-form-"]');
            if (formToRemove) {
                removeForm(formToRemove);
            }
        }
    });

    ticketFormsContainer.addEventListener("change", function (e) {
        if (e.target.name === "type_tkt[]") {
            const roundTripOptions = e.target
                .closest(".card-body")
                .querySelector(".round-trip-options");
            if (roundTripOptions) {
                roundTripOptions.style.display =
                    e.target.value === "Round Trip" ? "block" : "none";
            }
        }
    });

    // Initial setup
    resetAllTicketForms();
}

//Hotel JS
function handleHotelForms() {
    const hotelCheckbox = document.getElementById("hotelCheckbox");
    const hotelFormsContainer = document.getElementById("hotel_div");
    const maxHotels = 5;
    let currentHotelCount = 1;

    if (hotelCheckbox) {
        hotelCheckbox.addEventListener("change", function () {
            if (this.checked) {
                hotelFormsContainer.style.display = "block";
            } else {
                hotelFormsContainer.style.display = "none";
                resetAllHotelForms();
            }
        });
    }

    function resetHotelFields(container) {
        const inputs = container.querySelectorAll(
            'input[type="text"], input[type="number"], input[type="date"], input[type="time"], textarea'
        );
        inputs.forEach((input) => (input.value = ""));
        const selects = container.querySelectorAll("select");
        selects.forEach((select) => (select.selectedIndex = 0));
    }

    function resetAllHotelForms() {
        for (let i = 2; i <= maxHotels; i++) {
            const form = document.getElementById(`hotel-form-${i}`);
            if (form) {
                form.style.display = "none";
                resetHotelFields(form);
            }
        }
        currentHotelCount = 1;
        updateButtonVisibility();
    }

    function updateButtonVisibility() {
        for (let i = 1; i <= maxHotels; i++) {
            const form = document.getElementById(`hotel-form-${i}`);
            if (form) {
                const addButton = form.querySelector(".add-hotel-btn");
                const removeButton = form.querySelector(".remove-hotel-btn");

                if (addButton) {
                    addButton.style.display =
                        i === currentHotelCount && currentHotelCount < maxHotels
                            ? "inline-block"
                            : "none";
                }

                if (removeButton) {
                    removeButton.style.display =
                        i > 1 && i <= currentHotelCount
                            ? "inline-block"
                            : "none";
                }
            }
        }
    }

    hotelFormsContainer.addEventListener("click", function (e) {
        if (e.target.classList.contains("add-hotel-btn")) {
            if (currentHotelCount < maxHotels) {
                currentHotelCount++;
                const nextForm = document.getElementById(
                    `hotel-form-${currentHotelCount}`
                );
                if (nextForm) {
                    nextForm.style.display = "block";
                }
                updateButtonVisibility();
            }
        } else if (e.target.classList.contains("remove-hotel-btn")) {
            if (currentHotelCount > 1) {
                const currentForm = document.getElementById(
                    `hotel-form-${currentHotelCount}`
                );
                if (currentForm) {
                    currentForm.style.display = "none";
                    resetHotelFields(currentForm);
                }
                currentHotelCount--;
                updateButtonVisibility();
            }
        }
    });

    // Initial setup
    hotelFormsContainer.style.display = "none";
    resetAllHotelForms();
    updateButtonVisibility();
}

//Taksi JS
function handleTaksiForms() {
    const taksiCheckbox = document.getElementById("taksiCheckbox");
    const taksiDiv = document.getElementById("taksi_div");

    taksiCheckbox.addEventListener("change", function () {
        if (this.checked) {
            taksiDiv.style.display = "block";
        } else {
            taksiDiv.style.display = "none";
        }
    });
}

document.getElementById("nik").addEventListener("change", function () {
    var nik = this.value;

    fetch("/get-employee-data?nik=" + nik)
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                document.getElementById("jk_tkt").value = data.jk_tkt;
                document.getElementById("tlp_tkt").value = data.tlp_tkt;
            } else {
                alert("Employee data not found!");
            }
        })
        .catch((error) => console.error("Error:", error));
});
