function confirmAprroval() {
    let title1;
    let title2;
    let text;
    let confirmText;

    title1 = "Do you want to submit?";
    title2 = "KPI submitted successfuly!";
    text = "You won't be able to revert this!";
    confirmText = "Submit";

    Swal.fire({
        title: title1,
        text: text,
        showCancelButton: true,
        confirmButtonColor: "#4e73df",
        cancelButtonColor: "#d33",
        confirmButtonText: confirmText,
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("goalApprovalForm").submit();
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

function sendBack(id, nik, name) {
    let msg = $(`#messages${id}`);

    $("#request_id").val(id);
    $("#sendto").val(nik);

    if (!msg.val()) {
        Swal.fire({
            icon: "error",
            title: "Messages is Empty!",
            text: "Please fill the messages",
            confirmButtonColor: "#4e73df",
        }).then(() => {
            msg.focus();
        });

        return false;
    }

    title1 = "Do you want to sendback?";
    title2 = "KPI sendback successfuly!";
    text = `This form will sendback to ${name}`;
    confirmText = "Submit";

    Swal.fire({
        title: title1,
        text: text,
        showCancelButton: true,
        confirmButtonColor: "#4e73df",
        cancelButtonColor: "#d33",
        confirmButtonText: confirmText,
    }).then((result) => {
        if (result.isConfirmed) {
            // Menggunakan Ajax untuk mengirim data ke Laravel
            document.getElementById("goalSendbackForm").submit();
            Swal.fire({
                title: title2,
                icon: "success",
                showConfirmButton: false,
                // If confirmed, proceed with form submission
            });
        }
    });

    return false;
}
