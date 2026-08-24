<script>
(function () {
    function addRxRow(invoiceId) {
        var container = document.getElementById('rx-rows-' + invoiceId);
        if (!container) {
            return;
        }

        var idx = container.querySelectorAll('.rx-row').length;
        var row = document.createElement('div');
        row.className = 'row rx-row mb-2';
        row.innerHTML =
            '<div class="col-md-3"><input type="text" name="medicines[' + idx + '][medicine_name]" class="form-control" placeholder="اسم الدواء"></div>' +
            '<div class="col-md-2"><input type="text" name="medicines[' + idx + '][dosage]" class="form-control" placeholder="الجرعة"></div>' +
            '<div class="col-md-2"><input type="text" name="medicines[' + idx + '][frequency]" class="form-control" placeholder="التكرار"></div>' +
            '<div class="col-md-2"><input type="number" name="medicines[' + idx + '][duration_days]" class="form-control" placeholder="أيام" min="1"></div>' +
            '<div class="col-md-3"><input type="text" name="medicines[' + idx + '][instructions]" class="form-control" placeholder="تعليمات"></div>';
        container.appendChild(row);
    }

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-add-rx-row');
        if (!btn) {
            return;
        }
        e.preventDefault();
        addRxRow(btn.getAttribute('data-invoice-id'));
    });
})();
</script>
