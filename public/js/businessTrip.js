$(document).ready(function() {
    // Show additional fields when "luar kota" is selected
    $('#businessTripSelect').change(function() {
        if ($(this).val() === 'luar kota') {
            $('#additional-fields').show();
        } else {
            $('#additional-fields').hide();
            $('#additional-fields input[type=checkbox]').prop('checked', false);
            $('#additional-fields .tab-content .tab-pane').hide();
            $('#additional-fields .nav-pills .nav-link').removeClass('active');
        }
    });

    // Show relevant content based on selected checkboxes
    $('#additional-fields input[type=checkbox]').change(function() {
        let selectedTabs = [];

        $('#additional-fields input[type=checkbox]:checked').each(function() {
            let tabId = $(this).attr('id').replace('Checkbox', '');
            $(`#pills-${tabId}-tab`).addClass('active');
            $(`#pills-${tabId}`).show();
        });

        // Hide unselected tabs
        $('#additional-fields .nav-pills .nav-link').each(function() {
            let tabId = $(this).attr('id').replace('pills-', '').replace('-tab', '');
            if (!selectedTabs.includes(tabId)) {
                $(this).removeClass('active');
                $(`#pills-${tabId}`).hide();
            }
        });
    });
});

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

document.addEventListener("DOMContentLoaded", function () {
    var jnsDinasSelect = document.getElementById("jns_dinas");
    var additionalFields = document.getElementById("additional-fields");

    jnsDinasSelect.addEventListener("change", function () {
        if (this.value === "luar kota") {
            additionalFields.style.display = "block";
        } else {
            additionalFields.style.display = "none";
            // Reset all fields to "Tidak"
            document.getElementById("ca").value = "Tidak";
            document.getElementById("tiket").value = "Tidak";
            document.getElementById("hotel").value = "Tidak";
            document.getElementById("taksi").value = "Tidak";
        }
    });
});
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

document.addEventListener("DOMContentLoaded", function () {
    const ticketSelect = document.getElementById("tiket");
    const ticketDiv = document.getElementById("tiket_div");

    // Hide/show ticket form based on select option
    ticketSelect.addEventListener("change", function () {
        if (this.value === "Ya") {
            ticketDiv.style.display = "block";
        } else {
            ticketDiv.style.display = "none";
            // Reset all input fields within the ticketDiv when 'Tidak' is selected
            resetTicketFields(ticketDiv);
        }
    });

    // Function to reset ticket fields
    function resetTicketFields(container) {
        const inputs = container.querySelectorAll(
            'input[type="text"], input[type="number"], textarea'
        );
        inputs.forEach((input) => {
            input.value = "";
        });
        // Also reset select fields if needed
        const selects = container.querySelectorAll("select");
        selects.forEach((select) => {
            select.selectedIndex = 0; // or set to a specific default value
        });
    }

    // Handling form visibility and reset for multiple ticket forms
    for (let i = 1; i <= 4; i++) {
        const yesRadio = document.getElementById(`more_tkt_yes_${i}`);
        const noRadio = document.getElementById(`more_tkt_no_${i}`);
        const nextForm = document.getElementById(`ticket-form-${i + 1}`);

        yesRadio.addEventListener("change", function () {
            if (this.checked) {
                nextForm.style.display = "block";
            }
        });

        noRadio.addEventListener("change", function () {
            if (this.checked) {
                nextForm.style.display = "none";
                // Hide all subsequent forms
                for (let j = i + 1; j <= 5; j++) {
                    const form = document.getElementById(`ticket-form-${j}`);
                    if (form) {
                        form.style.display = "none";
                        // Reset the form when it is hidden
                        resetTicketFields(form);
                    }
                }
                // Reset radio buttons for subsequent forms
                for (let j = i + 1; j <= 4; j++) {
                    const noRadioButton = document.getElementById(
                        `more_tkt_no_${j}`
                    );
                    if (noRadioButton) {
                        noRadioButton.checked = true;
                    }
                }
            }
        });
    }

    // Handle Round Trip options
    const ticketTypes = document.querySelectorAll('select[name="type_tkt[]"]');
    ticketTypes.forEach((select, index) => {
        select.addEventListener("change", function () {
            const roundTripOptions = this.closest(".card-body").querySelector(
                ".round-trip-options"
            );
            if (this.value === "Round Trip") {
                roundTripOptions.style.display = "block";
            } else {
                roundTripOptions.style.display = "none";
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    // Ticket form handling
    const ticketCheckbox = document.getElementById("ticketCheckbox");
    const ticketDiv = document.getElementById("tiket_div");

    ticketCheckbox.addEventListener("change", function () {
        if (this.checked) {
            // Checkbox is checked
            ticketDiv.style.display = "block";
        } else {
            // Checkbox is unchecked
            ticketDiv.style.display = "none";
            // Reset all input fields within the ticketDiv when unchecked
            resetTicketFields(ticketDiv);
        }
    });

    function resetTicketFields(container) {
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

    for (let i = 1; i <= 4; i++) {
        const yesRadio = document.getElementById(`more_tkt_yes_${i}`);
        const noRadio = document.getElementById(`more_tkt_no_${i}`);
        const nextForm = document.getElementById(`ticket-form-${i + 1}`);

        yesRadio.addEventListener("change", function () {
            if (this.checked) {
                nextForm.style.display = "block";
            }
        });

        noRadio.addEventListener("change", function () {
            if (this.checked) {
                nextForm.style.display = "none";
                // Hide all subsequent forms
                for (let j = i + 1; j <= 5; j++) {
                    const form = document.getElementById(`ticket-form-${j}`);
                    if (form) {
                        form.style.display = "none";
                        // Reset the form when it is hidden
                        resetTicketFields(form);
                    }
                }
                // Reset radio buttons for subsequent forms
                for (let j = i + 1; j <= 4; j++) {
                    const noRadioButton = document.getElementById(
                        `more_tkt_no_${j}`
                    );
                    if (noRadioButton) {
                        noRadioButton.checked = true;
                    }
                }
            }
        });
    }

    // Handle Round Trip options
    const ticketTypes = document.querySelectorAll('select[name="type_tkt[]"]');
    ticketTypes.forEach((select, index) => {
        select.addEventListener("change", function () {
            const roundTripOptions = this.closest(".card-body").querySelector(
                ".round-trip-options"
            );
            if (this.value === "Round Trip") {
                roundTripOptions.style.display = "block";
            } else {
                roundTripOptions.style.display = "none";
            }
        });
    });

    // Hotel form handling
    for (let i = 1; i <= 4; i++) {
        const yesRadio = document.getElementById(`more_htl_yes_${i}`);
        const noRadio = document.getElementById(`more_htl_no_${i}`);
        const nextForm = document.getElementById(`hotel-form-${i + 1}`);

        yesRadio.addEventListener("change", function () {
            if (this.checked) {
                nextForm.style.display = "block";
            }
        });

        noRadio.addEventListener("change", function () {
            if (this.checked) {
                nextForm.style.display = "none";
                // Hide all subsequent forms
                for (let j = i + 1; j <= 5; j++) {
                    const form = document.getElementById(`hotel-form-${j}`);
                    if (form) {
                        form.style.display = "none";
                        // Reset the form when it is hidden
                        resetHotelFields(form);
                    }
                }
                // Reset radio buttons for subsequent forms
                for (let j = i + 1; j <= 4; j++) {
                    const noRadioButton = document.getElementById(
                        `more_htl_no_${j}`
                    );
                    if (noRadioButton) {
                        noRadioButton.checked = true;
                    }
                }
            }
        });
    }

    // Function to reset hotel fields
    function resetHotelFields(container) {
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

    // Calculate total days for each hotel form
    function calculateTotalDays(index) {
        const checkIn = document.querySelector(
            `#hotel-form-${index} input[name="tgl_masuk_htl[]"]`
        );
        const checkOut = document.querySelector(
            `#hotel-form-${index} input[name="tgl_keluar_htl[]"]`
        );
        const totalDays = document.querySelector(
            `#hotel-form-${index} input[name="total_hari[]"]`
        );

        if (checkIn && checkOut && totalDays) {
            const start = new Date(checkIn.value);
            const end = new Date(checkOut.value);

            if (checkIn.value && checkOut.value) {
                // Calculate difference in milliseconds and convert to days, excluding the same day
                const difference = Math.ceil(
                    (end - start) / (1000 * 60 * 60 * 24)
                );
                if (difference < 0) {
                    alert(
                        "Check out date cannot be earlier than check in date."
                    );
                    checkOut.value = ""; // Clear the check-out date if invalid
                    totalDays.value = ""; // Clear the total days if check-out date is reset
                } else {
                    totalDays.value = difference >= 0 ? difference : 0;
                }
            } else {
                totalDays.value = ""; // Clear total days if dates are not set
            }
        } else {
            console.error("Elements not found. Check selectors.");
        }
    }

    const hotelCheckbox = document.getElementById("hotelCheckbox");
    const hotelDiv = document.getElementById("hotel_div");
    const hotelFormsContainer = document.getElementById(
        "hotel_forms_container"
    );

    // Handle checkbox change event
    hotelCheckbox.addEventListener("change", function () {
        if (this.checked) {
            hotelDiv.style.display = "block";
            // Show the first hotel form when checkbox is checked
            document.getElementById("hotel-form-1").style.display = "block";
        } else {
            hotelDiv.style.display = "none";
            // Hide all hotel forms when checkbox is unchecked
            for (let i = 1; i <= 5; i++) {
                const form = document.getElementById(`hotel-form-${i}`);
                if (form) {
                    form.style.display = "none";
                    // Reset the form when it is hidden
                    resetHotelFields(form);
                }
            }
        }
    });
    const taksiCheckbox = document.getElementById("taksiCheckbox");
    const taksiDiv = document.getElementById("taksi_div");

    // Handle checkbox change event
    taksiCheckbox.addEventListener("change", function () {
        if (this.checked) {
            taksiDiv.style.display = "block";
        } else {
            taksiDiv.style.display = "none";
        }
    });

    // Add event listeners for date inputs
    for (let i = 1; i <= 5; i++) {
        const checkIn = document.querySelector(
            `#hotel-form-${i} input[name="tgl_masuk_htl[]"]`
        );
        const checkOut = document.querySelector(
            `#hotel-form-${i} input[name="tgl_keluar_htl[]"]`
        );

        if (checkIn && checkOut) {
            checkIn.addEventListener("change", () => calculateTotalDays(i));
            checkOut.addEventListener("change", () => calculateTotalDays(i));
        }
    }

    // Handle date validation for the return date
    document.getElementById("kembali").addEventListener("change", function () {
        var mulaiDate = document.getElementById("mulai").value;
        var kembaliDate = this.value;

        if (kembaliDate < mulaiDate) {
            alert("Return date cannot be earlier than Start date.");
            this.value = ""; // Reset the kembali field
        }
    });
});

document
    .getElementById("tgl_keluar_htl")
    .addEventListener("change", function () {
        var masukHtl = document.getElementById("tgl_masuk_htl").value;
        var keluarDate = this.value;

        if (masukHtl && keluarDate) {
            var checkInDate = new Date(masukHtl);
            var checkOutDate = new Date(keluarDate);

            if (checkOutDate < checkInDate) {
                alert("Check out date cannot be earlier than check in date.");
                this.value = ""; // Reset the check out date field
            }
        }
    });

document.getElementById("type_tkt").addEventListener("change", function () {
    var roundTripOptions = document.getElementById("roundTripOptions");
    if (this.value === "Round Trip") {
        roundTripOptions.style.display = "block";
    } else {
        roundTripOptions.style.display = "none";
    }
});

function BTtoggleOthers() {
    // ca_type ca_nbt ca_e
    var locationFilter = document.getElementById("tujuan");
    var others_location = document.getElementById("others_location");

    if (locationFilter.value === "Others") {
        others_location.style.display = "block";
    } else {
        others_location.style.display = "none";
        others_location.value = "";
    }
}

function validateDates(index) {
    // Get the departure and return date inputs for the given form index
    const departureDate = document.querySelector(`#tgl_brkt_tkt_${index}`);
    const returnDate = document.querySelector(`#tgl_plg_tkt_${index}`);

    // Get the departure and return time inputs for the given form index
    const departureTime = document.querySelector(`#jam_brkt_tkt_${index}`);
    const returnTime = document.querySelector(`#jam_plg_tkt_${index}`);

    if (departureDate && returnDate) {
        const depDate = new Date(departureDate.value);
        const retDate = new Date(returnDate.value);

        // Check if both dates are valid
        if (depDate && retDate) {
            // Validate if return date is earlier than departure date
            if (retDate < depDate) {
                alert("Return date cannot be earlier than departure date.");
                returnDate.value = ""; // Reset the return date field
            } else if (
                retDate.getTime() === depDate.getTime() &&
                departureTime &&
                returnTime
            ) {
                // If dates are the same, validate time
                const depTime = departureTime.value;
                const retTime = returnTime.value;

                // Check if both times are set and validate
                if (depTime && retTime) {
                    const depDateTime = new Date(`1970-01-01T${depTime}:00`);
                    const retDateTime = new Date(`1970-01-01T${retTime}:00`);

                    if (retDateTime < depDateTime) {
                        alert(
                            "Return time cannot be earlier than departure time on the same day."
                        );
                        returnTime.value = ""; // Reset the return time field
                    }
                }
            }
        }
    }
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
