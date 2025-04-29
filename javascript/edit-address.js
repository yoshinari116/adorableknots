function editAddress(address) {
    const form = document.getElementById('addressForm');
    const modalTitle = document.getElementById('manageAddressLabel');

    // Set form action to edit handler
    form.action = 'account/edit-address.php';
    modalTitle.textContent = 'Edit Address';

    // Format phone with spaces
    const phone = address.phone.replace(/(\d{4})(\d{3})(\d{4})/, '$1 $2 $3');
    document.getElementById('phone_number').value = phone;

    form.region.value = address.region;
    form.province.value = address.province;
    form.city.value = address.city;
    form.barangay.value = address.barangay;
    form.postal_code.value = address.postal_code;
    form.street_details.value = address.street_details;

    // Add or update hidden input for address_id
    let hiddenInput = document.getElementById('address_id_input');
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'address_id';
        hiddenInput.id = 'address_id_input';
        form.appendChild(hiddenInput);
    }
    hiddenInput.value = address.address_id;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('manageAddress'));
    modal.show();
}
