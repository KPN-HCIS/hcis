// Periksa apakah ada pesan sukses
var successMessage = "{{ session('success') }}";

// Jika ada pesan sukses, tampilkan sebagai alert
if (successMessage) {
    Swal.fire({
        title: "Success!",
        text: successMessage,
        icon: "success",
        confirmButtonColor: "#9a2a27",
        confirmButtonText: "Ok",
    });
}
function redirectToExportExcel() {
    const route = "{{ route('exportca.excel') }}";

    const startDate = document.getElementById("start_date").value;
    const endDate = document.getElementById("end_date").value;

    // Create a form element
    const form = document.createElement("form");
    form.method = "GET";
    form.action = route;

    const startDateInput = document.createElement("input");
    startDateInput.type = "hidden";
    startDateInput.name = "start_date";
    startDateInput.value = startDate;

    const endDateInput = document.createElement("input");
    endDateInput.type = "hidden";
    endDateInput.name = "end_date";
    endDateInput.value = endDate;

    form.appendChild(startDateInput);
    form.appendChild(endDateInput);

    // Append the form to the body and submit it
    document.body.appendChild(form);
    form.submit();
}

// Event listener untuk menangkap date range yang dipilih
$("#singledaterange").on("apply.daterangepicker", function (ev, picker) {
    var startDate = picker.startDate.format("YYYY-MM-DD");
    var endDate = picker.endDate.format("YYYY-MM-DD");

    // Panggil fungsi untuk mendapatkan data yang difilter
    filterTableByDateRange(startDate, endDate);
});

// Fungsi untuk mengirimkan tanggal yang dipilih ke server dan memperbarui tabel
function filterTableByDateRange(startDate, endDate) {
    $.ajax({
        url: '{{ route("cashadvanced.admin") }}', // Route yang sudah Anda buat
        type: "GET",
        data: {
            start_date: startDate,
            end_date: endDate,
        },
        success: function (response) {
            // Perbarui tabel dengan data yang difilter
            // $('#scheduleTable tbody').html(response);
            // $('#tableFilter').html(response);
        },
        error: function (xhr) {
            // console.error(xhr);
        },
    });
}
//script modal

// Modal Mengubah Status
document.addEventListener("DOMContentLoaded", function () {
    var statusModal = document.getElementById("statusModal");
    statusModal.addEventListener("show.bs.modal", function (event) {
        // Dapatkan tombol yang men-trigger modal
        var button = event.relatedTarget;

        // Ambil data-id dan data-status dari tombol tersebut
        var transactionId = button.getAttribute("data-id");
        var transactionStatus = button.getAttribute("data-status");

        // Temukan form di dalam modal dan update action-nya
        var form = statusModal.querySelector("form");
        var action = form.getAttribute("action");
        form.setAttribute("action", action.replace(":id", transactionId));

        // Set nilai transaction_id di input hidden
        var transactionInput = form.querySelector("#transaction_id");
        transactionInput.value = transactionId;

        // Pilih status yang sesuai di dropdown
        var statusSelect = form.querySelector("#ca_status");
        statusSelect.value = transactionStatus;
    });
});

// Modal Export
document.addEventListener("DOMContentLoaded", function () {
    var exportModal = document.getElementById("exportModal");
    var declareSection = document.querySelector(".declare-section");
    exportModal.addEventListener("show.bs.modal", function (event) {
        var button = event.relatedTarget;

        var transactionId = button.getAttribute("data-id");
        var status = button.getAttribute("data-status");
        console.log(status);

        var downloadLink = document.getElementById("downloadLink");
        downloadLink.href =
            '{{ route("cashadvanced.download", ":id") }}'.replace(
                ":id",
                transactionId
            );

        var declareLink = document.getElementById("declareLink");
        declareLink.href =
            '{{ route("cashadvanced.downloadDeclare", ":id") }}'.replace(
                ":id",
                transactionId
            );

        var transactionInput = document.getElementById("transaction_id");
        transactionInput.value = transactionId;

        if (
            status === "Pending" ||
            status === "Approved" ||
            status === "Rejected"
        ) {
            declareSection.style.display = "flex"; // Tampilkan
        } else {
            declareSection.style.display = "none"; // Sembunyikan
        }
    });
});

function addSweetAlert(approveButton) {
    approveButton.addEventListener("click", function (event) {
        event.preventDefault(); // Mencegah submit form secara langsung
        const transactionCA = approveButton.getAttribute("data-no-ca");
        const form = document.getElementById("approveForm");

        Swal.fire({
            title: `Do you want to approve transaction "${transactionCA}"?`,
            text: "You won't be able to revert this!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#0c63e4",
            cancelButtonColor: "#9a2a27",
            confirmButtonText: "Yes, approve it!",
        }).then((result) => {
            if (result.isConfirmed) {
                // Buat input baru untuk action_ca_approve
                const input = document.createElement("input");
                input.type = "hidden"; // Set input sebagai hidden
                input.name = "action_ca_approve"; // Set nama input
                input.value = "Approve"; // Set nilai input

                // Tambahkan input ke form
                form.appendChild(input);

                form.submit(); // Kirim form
            }
        });
    });
}

function addSweetAlertDec(approveButtonDec) {
    approveButtonDec.addEventListener("click", function (event) {
        event.preventDefault(); // Mencegah submit form secara langsung
        const transactionCA = approveButtonDec.getAttribute("data-no-ca");
        const form = document.getElementById("approveFormDec");

        Swal.fire({
            title: `Do you want to approve transaction "${transactionCA}"?`,
            text: "You won't be able to revert this!",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#0c63e4",
            cancelButtonColor: "#9a2a27",
            confirmButtonText: "Yes, approve it!",
        }).then((result) => {
            if (result.isConfirmed) {
                // Buat input baru untuk action_ca_approve
                const input = document.createElement("input");
                input.type = "hidden"; // Set input sebagai hidden
                input.name = "action_ca_approve"; // Set nama input
                input.value = "Approve"; // Set nilai input

                // Tambahkan input ke form
                form.appendChild(input);

                form.submit(); // Kirim form
            }
        });
    });
}

// Reject Request Modal
document.addEventListener("DOMContentLoaded", function () {
    var modalRejectDec = document.getElementById("modalReject");
    modalReject.addEventListener("show.bs.modal", function (event) {
        var button = event.relatedTarget;

        var transactionId = button.getAttribute("data-no-id");
        var transactionNo = button.getAttribute("data-no-ca");
        var transactionIdCA = button.getAttribute("data-no-idCA");
        console.log(transactionIdCA);

        // Mendefinisikan form terlebih dahulu
        var form = modalReject.querySelector("form");

        form.querySelector("#data_no_id").value = transactionIdCA;

        document.getElementById("reject_no_ca_2").textContent = transactionNo;

        var form = modalReject.querySelector("form");
        var action = form.getAttribute("action");
        form.setAttribute("action", action.replace(":id", transactionId));
    });
});

// Reject Declaration Modal
document.addEventListener("DOMContentLoaded", function () {
    var modalRejectDec = document.getElementById("modalRejectDec");
    modalRejectDec.addEventListener("show.bs.modal", function (event) {
        var button = event.relatedTarget;

        var transactionId = button.getAttribute("data-no-id");
        var transactionNo = button.getAttribute("data-no-ca");
        var transactionIdCA = button.getAttribute("data-no-idCA");
        console.log(transactionIdCA);

        // Mendefinisikan form terlebih dahulu
        var form = modalRejectDec.querySelector("form");

        form.querySelector("#data_no_id").value = transactionIdCA;

        document.getElementById("rejectDec_no_ca_2").textContent =
            transactionNo;

        var form = modalRejectDec.querySelector("form");
        var action = form.getAttribute("action");
        form.setAttribute("action", action.replace(":id", transactionId));
    });
});
