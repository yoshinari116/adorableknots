document.getElementById('addressForm').addEventListener('submit', function(event) {
    const phoneInput = document.querySelector('input[name="phone"]');
    const phoneValue = phoneInput.value.replace(/\D/g, '');  // Remove non-digit characters

    // Validate phone number: must start with '09' and be 11 digits long
    if (!phoneValue.startsWith('09') || phoneValue.length !== 11) {
        alert('Invalid format. Please enter a phone number starting with 09 and 11 digits total.');  // Show alert
        event.preventDefault();  // Prevent form submission
        phoneInput.setCustomValidity('Invalid format. Please enter a phone number starting with 09 and 11 digits total.');
    } else {
        phoneInput.setCustomValidity('');  // Clear the custom error message if valid
    }
});
