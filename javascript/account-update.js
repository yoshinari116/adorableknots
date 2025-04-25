document.querySelectorAll('.edit-btn').forEach(editBtn => {
    editBtn.addEventListener('click', () => {
        const field = editBtn.dataset.field;
        const state = editBtn.dataset.state;
        const saveBtn = document.querySelector(`.save-btn[data-field="${field}"]`);
        const icon = editBtn.querySelector('img');

        if (field === 'fullname') {
            const fullInput = document.querySelector('input[name="fullname"]');
            const editWrapper = document.getElementById('fullname-edit-wrapper');
            const firstInput = document.getElementById('firstname');
            const lastInput = document.getElementById('lastname');

            if (state === 'edit') {
                const [first, ...lastParts] = fullInput.value.split(' ');
                firstInput.value = first;
                lastInput.value = lastParts.join(' ');

                editWrapper.style.display = 'block';
                saveBtn.style.display = 'inline-block';
                icon.src = 'assets/icons/cancel.png';
                icon.alt = 'Cancel';
                editBtn.dataset.state = 'cancel';
            } else {
                editWrapper.style.display = 'none';
                saveBtn.style.display = 'none';
                icon.src = 'assets/icons/edit.png';
                icon.alt = 'Edit';
                editBtn.dataset.state = 'edit';
            }
        } else {
            const input = document.querySelector(`input[name="${field}"]`);
            if (state === 'edit') {
                input.removeAttribute('readonly');
                input.focus();
                saveBtn.style.display = 'inline-block';
                icon.src = 'assets/icons/cancel.png';
                icon.alt = 'Cancel';
                editBtn.dataset.state = 'cancel';
            } else {
                input.setAttribute('readonly', true);
                saveBtn.style.display = 'none';
                icon.src = 'assets/icons/edit.png';
                icon.alt = 'Edit';
                editBtn.dataset.state = 'edit';
            }
        }
    });
});

document.querySelectorAll('.save-btn').forEach(saveBtn => {
    saveBtn.addEventListener('click', () => {
        const field = saveBtn.dataset.field;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'account/update-account.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
            if (xhr.status === 200) {
                if (field === 'fullname') {
                    const fullInput = document.querySelector('input[name="fullname"]');
                    const first = document.getElementById('firstname').value;
                    const last = document.getElementById('lastname').value;
                    fullInput.value = `${first} ${last}`;
                    document.getElementById('fullname-edit-wrapper').style.display = 'none';
                } else {
                    document.querySelector(`input[name="${field}"]`).setAttribute('readonly', true);
                }

                saveBtn.style.display = 'none';
                const editBtn = document.querySelector(`.edit-btn[data-field="${field}"]`);
                const icon = editBtn.querySelector('img');
                icon.src = 'assets/icons/edit.png';
                icon.alt = 'Edit';
                editBtn.dataset.state = 'edit';
            } else {
                alert('Error saving changes.');
            }
        };

        let data = '';
        if (field === 'fullname') {
            const first = encodeURIComponent(document.getElementById('firstname').value);
            const last = encodeURIComponent(document.getElementById('lastname').value);
            data = `field=fullname&firstname=${first}&lastname=${last}`;
        } else {
            const value = encodeURIComponent(document.querySelector(`input[name="${field}"]`).value);
            data = `field=${field}&value=${value}`;
        }

        xhr.send(data);
    });
});

