// Pendaftaran PMB - JavaScript Enhancement
document.addEventListener('DOMContentLoaded', function() {
    console.log('Pendaftaran PMB loaded');
    
    // ==================== FORM VALIDATION ====================
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showToast('Harap periksa kembali form Anda', 'error');
            } else {
                // Show loading overlay
                showLoading();
                
                // Auto remove loading after 3 seconds (fallback)
                setTimeout(hideLoading, 3000);
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                clearFieldError(this);
            });
        });
    });
    
    // ==================== DELETE CONFIRMATION ====================
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!this.disabled) {
                e.preventDefault();
                const href = this.getAttribute('href');
                const nama = this.closest('tr').querySelector('td:nth-child(2)').textContent;
                
                showConfirmationModal(
                    'Konfirmasi Hapus',
                    `Anda yakin ingin menghapus pendaftaran: <strong>${nama}</strong>?`,
                    href
                );
            }
        });
    });
    
    // ==================== AUTO-FILL CURRENT YEAR ====================
    const tahunLulusInput = document.getElementById('tahun_lulus');
    if (tahunLulusInput && !tahunLulusInput.value) {
        tahunLulusInput.value = new Date().getFullYear();
    }
    
    // ==================== CHARACTER COUNTER ====================
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const counter = document.createElement('div');
        counter.className = 'char-counter';
        counter.style.cssText = 'text-align: right; font-size: 0.8rem; color: #6c757d; margin-top: 5px;';
        textarea.parentNode.appendChild(counter);
        
        function updateCounter() {
            const count = textarea.value.length;
            const max = textarea.getAttribute('maxlength') || 1000;
            counter.textContent = `${count}/${max} karakter`;
            
            if (count > max * 0.9) {
                counter.style.color = '#dc3545';
            } else if (count > max * 0.7) {
                counter.style.color = '#ffc107';
            } else {
                counter.style.color = '#6c757d';
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial count
    });
    
    // ==================== AUTO-FORMAT NILAI ====================
    const nilaiInput = document.getElementById('nilai_akhir');
    if (nilaiInput) {
        nilaiInput.addEventListener('blur', function() {
            let value = parseFloat(this.value);
            if (!isNaN(value)) {
                if (value > 100) value = 100;
                if (value < 0) value = 0;
                this.value = value.toFixed(2);
                
                // Show grade prediction
                showGradePrediction(value);
            }
        });
    }
    
    // ==================== ENHANCE SELECT BOXES ====================
    const selectBoxes = document.querySelectorAll('select');
    selectBoxes.forEach(select => {
        // Add custom arrow
        select.style.backgroundImage = 'none';
        
        select.addEventListener('change', function() {
            if (this.value) {
                this.classList.add('selected');
            } else {
                this.classList.remove('selected');
            }
        });
        
        // Trigger change event for initial state
        if (select.value) {
            select.classList.add('selected');
        }
    });
    
    // ==================== PRINT FUNCTIONALITY ====================
    const printButton = document.createElement('button');
    printButton.innerHTML = '<i class="fas fa-print"></i> Cetak';
    printButton.className = 'btn-action';
    printButton.style.cssText = 'background: #6c757d; color: white; margin-left: auto;';
    printButton.addEventListener('click', function() {
        window.print();
    });
    
    const crudHeader = document.querySelector('.crud-header');
    if (crudHeader && document.querySelector('.table-container')) {
        crudHeader.appendChild(printButton);
    }
    
    // ==================== AUTO-HIDE ALERTS ====================
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }, 5000);
        
        // Close button
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '&times;';
        closeBtn.style.cssText = 'background: none; border: none; font-size: 1.5rem; color: inherit; cursor: pointer; margin-left: auto;';
        closeBtn.addEventListener('click', () => {
            alert.style.display = 'none';
        });
        alert.appendChild(closeBtn);
    });
    
    // ==================== RESPONSIVE TABLE ====================
    function makeTableResponsive() {
        const tables = document.querySelectorAll('.table');
        tables.forEach(table => {
            if (table.offsetWidth > table.parentElement.offsetWidth) {
                table.parentElement.style.overflowX = 'auto';
            }
        });
    }
    
    makeTableResponsive();
    window.addEventListener('resize', makeTableResponsive);
    
    // ==================== SIDEBAR TOGGLE (Mobile) ====================
    const sidebarToggle = document.createElement('button');
    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
    sidebarToggle.className = 'sidebar-toggle';
    sidebarToggle.style.cssText = `
        position: fixed;
        top: 20px;
        left: 20px;
        background: #2c3e50;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 15px;
        z-index: 1000;
        cursor: pointer;
        display: none;
    `;
    
    document.body.appendChild(sidebarToggle);
    
    sidebarToggle.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });
    
    // Check screen width
    function checkScreenWidth() {
        if (window.innerWidth <= 768) {
            sidebarToggle.style.display = 'block';
            document.querySelector('.sidebar').classList.remove('active');
        } else {
            sidebarToggle.style.display = 'none';
            document.querySelector('.sidebar').classList.add('active');
        }
    }
    
    checkScreenWidth();
    window.addEventListener('resize', checkScreenWidth);
});

// ==================== HELPER FUNCTIONS ====================

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    // Special validation for nilai
    const nilaiField = form.querySelector('#nilai_akhir');
    if (nilaiField && nilaiField.value) {
        const nilai = parseFloat(nilaiField.value);
        if (nilai < 0 || nilai > 100) {
            showFieldError(nilaiField, 'Nilai harus antara 0-100');
            isValid = false;
        }
    }
    
    // Special validation for tahun lulus
    const tahunField = form.querySelector('#tahun_lulus');
    if (tahunField && tahunField.value) {
        const tahun = parseInt(tahunField.value);
        const currentYear = new Date().getFullYear();
        if (tahun < 2000 || tahun > currentYear) {
            showFieldError(tahunField, `Tahun harus antara 2000-${currentYear}`);
            isValid = false;
        }
    }
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.previousElementSibling?.textContent || field.name;
    
    // Clear previous error
    clearFieldError(field);
    
    // Check required
    if (field.required && !value) {
        showFieldError(field, `${fieldName} harus diisi`);
        return false;
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            showFieldError(field, 'Format email tidak valid');
            return false;
        }
    }
    
    // Number validation
    if (field.type === 'number' && value) {
        const min = field.min ? parseFloat(field.min) : null;
        const max = field.max ? parseFloat(field.max) : null;
        
        if (min !== null && parseFloat(value) < min) {
            showFieldError(field, `Minimal ${min}`);
            return false;
        }
        
        if (max !== null && parseFloat(value) > max) {
            showFieldError(field, `Maksimal ${max}`);
            return false;
        }
    }
    
    // Add success class if valid
    if (value) {
        field.classList.add('success');
    }
    
    return true;
}

function showFieldError(field, message) {
    field.classList.add('error');
    
    let errorElement = field.nextElementSibling;
    if (!errorElement || !errorElement.classList.contains('error-message')) {
        errorElement = document.createElement('span');
        errorElement.className = 'error-message';
        field.parentNode.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
}

function clearFieldError(field) {
    field.classList.remove('error');
    
    const errorElement = field.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.remove();
    }
}

function showGradePrediction(nilai) {
    const gradeElement = document.getElementById('grade-prediction') || 
                         document.createElement('div');
    
    gradeElement.id = 'grade-prediction';
    gradeElement.style.cssText = `
        margin-top: 10px;
        padding: 10px;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    `;
    
    let grade = 'E';
    let color = '#dc3545';
    
    if (nilai >= 85) { grade = 'A'; color = '#28a745'; }
    else if (nilai >= 80) { grade = 'A-'; color = '#28a745'; }
    else if (nilai >= 75) { grade = 'B+'; color = '#17a2b8'; }
    else if (nilai >= 70) { grade = 'B'; color = '#17a2b8'; }
    else if (nilai >= 65) { grade = 'B-'; color = '#ffc107'; }
    else if (nilai >= 60) { grade = 'C+'; color = '#ffc107'; }
    else if (nilai >= 55) { grade = 'C'; color = '#ffc107'; }
    else if (nilai >= 50) { grade = 'C-'; color = '#fd7e14'; }
    else if (nilai >= 40) { grade = 'D'; color = '#dc3545'; }
    
    gradeElement.innerHTML = `Prediksi Nilai Huruf: <span style="color:${color};font-size:1.2em;">${grade}</span>`;
    gradeElement.style.background = color + '20'; // 20 = 12% opacity in hex
    
    const nilaiField = document.getElementById('nilai_akhir');
    if (nilaiField && !nilaiField.nextElementSibling?.id === 'grade-prediction') {
        nilaiField.parentNode.appendChild(gradeElement);
    }
}

function showConfirmationModal(title, message, confirmUrl) {
    const modal = document.createElement('div');
    modal.className = 'confirmation-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <h3 style="margin-bottom: 15px; color: #dc3545;">${title}</h3>
            <p>${message}</p>
            <div class="modal-actions">
                <button class="btn-modal-cancel">Batal</button>
                <button class="btn-modal-confirm">Ya, Hapus</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    modal.style.display = 'flex';
    
    modal.querySelector('.btn-modal-cancel').addEventListener('click', () => {
        modal.remove();
    });
    
    modal.querySelector('.btn-modal-confirm').addEventListener('click', () => {
        window.location.href = confirmUrl;
    });
    
    // Close on background click
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function showLoading() {
    let overlay = document.querySelector('.loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(overlay);
    }
    overlay.style.display = 'flex';
}

function hideLoading() {
    const overlay = document.querySelector('.loading-overlay');
    if (overlay) {
        overlay.style.display = 'none';
    }
}

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        color: white;
        z-index: 1000;
        animation: slideInRight 0.3s ease;
    `;
    
    if (type === 'success') toast.style.background = '#28a745';
    else if (type === 'error') toast.style.background = '#dc3545';
    else toast.style.background = '#17a2b8';
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Add CSS animations for toast
if (!document.querySelector('#toast-styles')) {
    const style = document.createElement('style');
    style.id = 'toast-styles';
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
}