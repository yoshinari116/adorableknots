document.addEventListener("DOMContentLoaded", function () {
    var errorModal = document.getElementById('errorModal');
    var successModal = document.getElementById('successModal');

    if (errorModal) {
        var modal = new bootstrap.Modal(errorModal);
        modal.show();
    }

    if (successModal) {
        var modal = new bootstrap.Modal(successModal);
        modal.show();
    }
});
