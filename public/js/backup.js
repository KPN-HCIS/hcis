document.addEventListener("DOMContentLoaded", function () {
    let formTicketCount = 1;
    const ticketCheckbox = document.getElementById("ticketCheckbox");
    const maxTicketForms = 5;
    const ticketFormsContainer = document.getElementById(
        "ticket_forms_container"
    );

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

    $("#add-ticket-btn").on("click", function () {
        if (formTicketCount < maxTicketForms) {
            formTicketCount++;
            const newTicketForm = `
             <div class="card bg-light shadow-none" id="ticket-form-${formTicketCount}" style="display: block;">
                    <div class="card-body">
                        <div class="h5 text-uppercase">
                            <b>TICKET ${formTicketCount}</b>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Employee Name</label>
                                <select class="form-select form-select-sm selection2" id="noktp_tkt_${formTicketCount}" name="noktp_tkt[]">
                                   <option value="">Please Select</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">From</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" name="dari_tkt[]" type="text" placeholder="ex. Yogyakarta (YIA)">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">To</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" name="ke_tkt[]" type="text" placeholder="ex. Jakarta (CGK)">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Date</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" id="tgl_brkt_tkt_${formTicketCount}" name="tgl_brkt_tkt[]" type="date">
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Time</label>
                                <div class="input-group">
                                    <input class="form-control form-control-sm" id="jam_brkt_tkt_${formTicketCount}" name="jam_brkt_tkt[]" type="time">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Transportation Type</label>
                                <div class="input-group">
                                    <select class="form-select form-select-sm" name="jenis_tkt[]" id="jenis_tkt_${formTicketCount}">
                                        <option value="">Select Transportation Type</option>
                                        <option value="Train">Train</option>
                                        <option value="Bus">Bus</option>
                                        <option value="Airplane">Airplane</option>
                                        <option value="Car">Car</option>
                                        <option value="Ferry">Ferry</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Ticket Type</label>
                                <select class="form-select form-select-sm" name="type_tkt[]">
                                    <option value="One Way" selected>One Way</option>
                                    <option value="Round Trip">Round Trip</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Information</label>
                                <textarea class="form-control" id="ket_tkt_${formTicketCount}" name="ket_tkt[]" rows="3" placeholder="This field is for adding ticket details, e.g., Citilink, Garuda Indonesia, etc."></textarea>
                            </div>
                        </div>
                        <div class="round-trip-options" style="display: none;">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Return Date</label>
                                    <div class="input-group">
                                        <input class="form-control form-control-sm" name="tgl_plg_tkt[]" type="date" id="tgl_plg_tkt_${formTicketCount}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Return Time</label>
                                    <div class="input-group">
                                        <input class="form-control form-control-sm" id="jam_plg_tkt_${formTicketCount}" name="jam_plg_tkt[]" type="time">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-ticket-btn" data-form-id="${formTicketCount}">Remove Ticket</button>
                        </div>
                    </div>
                </div>`;

            $("#ticket_forms_container").append(newTicketForm);
            updateRemoveButtons();

            $(".selection2").select2({
                minimumInputLength: 1,
                theme: "bootstrap-5",
                ajax: {
                    url: "/search/name", // Route for your Laravel search endpoint
                    dataType: "json",
                    delay: 250, // Wait 250ms before triggering request (debounce)
                    data: function (params) {
                        return {
                            searchTerm: params.term, // Search term entered by the user
                            // employeeId: $("#employee_id").val(),
                        };
                    },
                    processResults: function (data) {
                        // Map the data to Select2 format
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    id: item.ktp, // ID field for Select2
                                    text: item.fullname + "-" + item.ktp, // Text to display in Select2
                                };
                            }),
                        };
                    },
                    cache: true,
                },
            });
        } else {
            alert("You have reached the maximum number of tickets (5).");
        }
    });
    function updateRemoveButtons() {
        // $('.remove-calibrator').prop('disabled', false); // Enable all remove buttons
        if (formTicketCount === 1) {
            $(".remove-ticket-btn").hide(); // Hide remove button when only one form
        } else {
            $(".remove-ticket-btn").show(); // Show remove buttons if more than one form
        }
        // $(`#ticket-form-${formTicketCount} .remove-ticket-btn`).prop(
        //     "disabled",
        //     false
        // ); // Ensure the latest one is enabled
    }

    $(document).on("click", ".remove-ticket-btn", function () {
        const formId = $(this).data("form-id");
        $(`#ticket-form-${formId}`).remove();
        formTicketCount--;
        updateRemoveButtons(); // Update buttons visibility
    });
    updateRemoveButtons();
});
