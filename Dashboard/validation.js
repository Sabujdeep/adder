// Qualification row counter
let qualificationCount = document.querySelectorAll('.qualification-row').length;

// Validate Phone Number (must be exactly 10 digits)
function validatePhone() {
    const phone = document.getElementById('phone');
    const phoneError = document.getElementById('phoneError');
    const phoneValue = phone.value.replace(/\D/g, ''); // Remove non-digits
    
    phone.value = phoneValue; // Update input with only digits
    
    if (phoneValue.length > 0 && phoneValue.length !== 10) {
        phone.classList.add('error');
        phoneError.textContent = 'Phone number must be exactly 10 digits';
        return false;
    } else {
        phone.classList.remove('error');
        phoneError.textContent = '';
        return true;
    }
}

// Validate Aadhaar Number (must be exactly 12 digits)
function validateAadhaar() {
    const aadhaar = document.getElementById('adhaar_number');
    const aadhaarError = document.getElementById('aadhaarError');
    const aadhaarValue = aadhaar.value.replace(/\D/g, ''); // Remove non-digits
    
    aadhaar.value = aadhaarValue; // Update input with only digits
    
    if (aadhaarValue.length > 0 && aadhaarValue.length !== 12) {
        aadhaar.classList.add('error');
        aadhaarError.textContent = 'Aadhaar number must be exactly 12 digits';
        return false;
    } else {
        aadhaar.classList.remove('error');
        aadhaarError.textContent = '';
        return true;
    }
}

// Validate Email (must be Gmail only)
function validateEmail() {
    const email = document.getElementById('email');
    const emailError = document.getElementById('emailError');
    const emailValue = email.value.trim();
    
    if (emailValue.length > 0 && !emailValue.endsWith('@gmail.com')) {
        email.classList.add('error');
        emailError.textContent = 'Only Gmail addresses are allowed (e.g., user@gmail.com)';
        return false;
    } else {
        email.classList.remove('error');
        emailError.textContent = '';
        return true;
    }
}

// Form Validation on Submit
function validateForm() {
    let isValid = true;
    
    // Validate Phone
    const phone = document.getElementById('phone').value.replace(/\D/g, '');
    if (phone.length > 0 && phone.length !== 10) {
        validatePhone();
        isValid = false;
        alert('Phone number must be exactly 10 digits');
    }
    
    // Validate Aadhaar
    const aadhaar = document.getElementById('adhaar_number').value.replace(/\D/g, '');
    if (aadhaar.length > 0 && aadhaar.length !== 12) {
        validateAadhaar();
        isValid = false;
        alert('Aadhaar number must be exactly 12 digits');
    }
    
    // Validate Email
    const email = document.getElementById('email').value.trim();
    if (email.length > 0 && !email.endsWith('@gmail.com')) {
        validateEmail();
        isValid = false;
        alert('Only Gmail addresses are allowed (e.g., user@gmail.com)');
    }
    
    return isValid;
}

// Add new qualification row
function addRow() {
    const container = document.getElementById('qualificationContainer');
    qualificationCount++;
    const div = document.createElement('div');
    div.className = 'qualification-row';

    div.innerHTML = `
        <div style="align-self: center; color: #9ca3af; font-weight: 600; font-size: 13px;">${qualificationCount}</div>
        <div class="form-group">
            <label>Qualification</label>
            <input type="text" name="qualification_name[]" placeholder="e.g., B.Tech, MBA">
        </div>
        <div class="form-group">
            <label>Board / University</label>
            <input type="text" name="board_university[]" placeholder="e.g., IIT Delhi">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description[]" placeholder="Brief description" rows="1"></textarea>
        </div>
        <div class="form-group">
            <label>Year Join</label>
            <input type="number" name="year_join[]" placeholder="2020">
        </div>
        <div class="form-group">
            <label>Year Finish</label>
            <input type="number" name="year_finish[]" placeholder="2024">
        </div>
        <input type="hidden" name="old_qualification_file[]" value="">
        <div class="q-actions">
            <button type="button" class="add-btn" onclick="addRow()">➕</button>
            <button type="button" class="remove-btn" onclick="removeRow(this)">✕</button>
        </div>
    `;

    container.appendChild(div);
}

// Remove qualification row
function removeRow(button) {
    const container = document.getElementById('qualificationContainer');
    const rows = container.getElementsByClassName('qualification-row');

    if (rows.length > 1) {
        button.closest('.qualification-row').remove();
        updateRowNumbers();
    } else {
        alert("At least one qualification is required.");
    }
}

// Update row numbers after deletion
function updateRowNumbers() {
    const rows = document.querySelectorAll('.qualification-row');
    rows.forEach((row, idx) => {
        const numDiv = row.querySelector('div:first-child');
        numDiv.textContent = idx + 1;
    });
    qualificationCount = rows.length;
}