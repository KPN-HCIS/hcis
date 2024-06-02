function adminReportType(val) {
    $("#report_type").val(val);
    const reportForm = $("#admin_report_filter");
    const exportButton = $("#export");
    const reportContentDiv = $("#report_content");
    const customsearch = $("#customsearch");
    const formData = reportForm.serialize();
    $.ajax({
        url: "/admin/get-report-content", // Endpoint URL to fetch report content
        method: "POST",
        data: formData, // Send serialized form data
        success: function (data) {
            //alert(data);
            reportContentDiv.html(data); // Update report content
            exportButton.removeClass("disabled"); // Enable export button
            $("#modalFilter").modal("hide");

            const reportGoalsTable = $("#adminReportTable").DataTable({
                dom: "lrtip",
                pageLength: 50,
            });
            customsearch.keyup(function () {
                reportGoalsTable.search($(this).val()).draw();
            });
        },
        error: function (xhr, status, error) {
            console.error("Error fetching report content:", error);
            // Optionally display an error message to the user
            reportContentDiv.html("");
        },
    });
    return; // Prevent default form submission
}
function reportType(val) {
    $("#report_type").val(val);
    const reportForm = $("#filter_form");
    const exportButton = $("#export");
    const reportContentDiv = $("#report_content");
    const customsearch = $("#customsearch");
    const formData = reportForm.serialize();
    $.ajax({
        url: "/get-report-content", // Endpoint URL to fetch report content
        method: "POST",
        data: formData, // Send serialized form data
        success: function (data) {
            reportContentDiv.html(data); // Update report content
            exportButton.removeClass("disabled"); // Enable export button
            $("#modalFilter").modal("hide");

            const reportGoalsTable = $("#reportGoalsTable").DataTable({
                dom: "lrtip",
                pageLength: 50,
            });
            customsearch.keyup(function () {
                reportGoalsTable.search($(this).val()).draw();
            });
        },
        error: function (xhr, status, error) {
            console.error("Error fetching report content:", error);
            // Optionally display an error message to the user
            reportContentDiv.html("");
        },
    });
    return; // Prevent default form submission
}
