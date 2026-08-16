<script>
(function () {
    var patientInput = document.querySelector('.js-patient-share-input');
    var companyInput = document.querySelector('.js-company-share-input');
    if (!patientInput || !companyInput) {
        return;
    }

    function clamp(value) {
        var n = parseFloat(value);
        if (isNaN(n)) {
            return 0;
        }
        return Math.min(100, Math.max(0, n));
    }

    function formatValue(value) {
        var n = Math.round(value * 100) / 100;
        return Number.isInteger(n) ? String(n) : n.toFixed(2).replace(/\.?0+$/, '');
    }

    function syncFromPatient() {
        var patient = clamp(patientInput.value);
        patientInput.value = formatValue(patient);
        companyInput.value = formatValue(100 - patient);
        updateShareSummary();
    }

    function syncFromCompany() {
        var company = clamp(companyInput.value);
        companyInput.value = formatValue(company);
        patientInput.value = formatValue(100 - company);
        updateShareSummary();
    }

    function updateShareSummary() {
        var patient = parseFloat(patientInput.value) || 0;
        var company = parseFloat(companyInput.value) || 0;
        var total = patient + company;
        var patientEl = document.querySelector('.js-patient-share');
        var companyEl = document.querySelector('.js-company-share');
        var totalEl = document.querySelector('.js-total-share');
        var errorEl = document.querySelector('.js-share-error');
        var submitBtn = document.querySelector('.hms-form-actions button[type="submit"]');
        var exact = Math.abs(total - 100) < 0.01;

        if (patientEl) {
            patientEl.textContent = formatValue(patient);
        }
        if (companyEl) {
            companyEl.textContent = formatValue(company);
        }
        if (totalEl) {
            totalEl.textContent = formatValue(total);
        }

        if (errorEl) {
            errorEl.style.display = exact ? 'none' : 'block';
        }
        if (submitBtn) {
            submitBtn.disabled = !exact;
        }
    }

    patientInput.addEventListener('input', syncFromPatient);
    companyInput.addEventListener('input', syncFromCompany);

    if (!patientInput.value && !companyInput.value) {
        patientInput.value = '30';
        companyInput.value = '70';
    } else if (patientInput.value && !companyInput.value) {
        syncFromPatient();
    } else if (!patientInput.value && companyInput.value) {
        syncFromCompany();
    } else if (Math.abs((parseFloat(patientInput.value) || 0) + (parseFloat(companyInput.value) || 0) - 100) >= 0.01) {
        syncFromPatient();
    }

    updateShareSummary();
})();
</script>
