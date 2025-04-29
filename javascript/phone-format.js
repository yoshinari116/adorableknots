document.addEventListener("DOMContentLoaded", function () {
    const phoneInput = document.getElementById("phone_number");

    if (phoneInput) {
        phoneInput.addEventListener("input", function () {
            let numbers = this.value.replace(/\D/g, '');
            if (numbers.length > 11) numbers = numbers.slice(0, 11);

            let formatted = '';
            if (numbers.length > 0) formatted += numbers.substring(0, 4);
            if (numbers.length >= 5) formatted += ' ' + numbers.substring(4, 7);
            if (numbers.length >= 8) formatted += ' ' + numbers.substring(7, 11);

            this.value = formatted;
        });

        const form = document.getElementById("addressForm");
        if (form) {
            form.addEventListener("submit", function () {
                phoneInput.value = phoneInput.value.replace(/\s/g, '');
            });
        }
    }
});