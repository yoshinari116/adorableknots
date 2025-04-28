document.addEventListener('DOMContentLoaded', () => {
    const phoneInput = document.querySelector('input[name="phone"]');
    const addressForm = document.getElementById('addressForm');

    // Auto-remove spaces as user types
    phoneInput.addEventListener('input', () => {
        phoneInput.value = phoneInput.value.replace(/\s+/g, '');
    });

    // Validate on form submit
    addressForm.addEventListener('submit', function(event) {
        const phone = phoneInput.value.trim();
        const phonePattern = /^09\d{9}$/;

        // Remove existing error if any
        let existingError = document.getElementById('phone-error');
        if (existingError) existingError.remove();

        if (!phonePattern.test(phone)) {
            event.preventDefault(); // Stop form from submitting

            const error = document.createElement('div');
            error.id = 'phone-error';
            error.style.color = 'red';
            error.style.marginTop = '5px';
            error.textContent = 'Phone number must start with 09 and be 11 digits long';
            phoneInput.parentNode.appendChild(error);
        }
    });
});
