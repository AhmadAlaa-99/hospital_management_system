<script>
(function () {
    var TAX_RATE_SINGLE = 17;

    function fmt(n) {
        return parseFloat(n || 0).toFixed(2);
    }

    function calcSingleTotal(price) {
        var discount = 0;
        var subtotal = price - discount;
        var taxValue = subtotal * (TAX_RATE_SINGLE / 100);
        return subtotal + taxValue;
    }

    function calcGroupTotal($opt) {
        var total = parseFloat($opt.data('total') || 0);
        if (total > 0) {
            return total;
        }
        var price = parseFloat($opt.data('price') || 0);
        var discount = parseFloat($opt.data('discount') || 0);
        var taxRate = parseFloat($opt.data('tax-rate') || 0);
        var subtotal = price - discount;
        return subtotal + (subtotal * (taxRate / 100));
    }

    function updatePreview() {
        var serviceCount = ($('#doctor_invoice_service_ids').val() || []).length;
        var groupCount = ($('#doctor_invoice_group_ids').val() || []).length;
        var grandTotal = 0;

        $('#doctor_invoice_service_ids option:selected').each(function () {
            grandTotal += calcSingleTotal(parseFloat($(this).data('price') || 0));
        });
        $('#doctor_invoice_group_ids option:selected').each(function () {
            grandTotal += calcGroupTotal($(this));
        });

        var parts = [];
        if (serviceCount) {
            parts.push(serviceCount + ' خدمة مفردة');
        }
        if (groupCount) {
            parts.push(groupCount + ' مجموعة');
        }

        var text = parts.length
            ? 'سيتم إنشاء ' + (serviceCount + groupCount) + ' فاتورة (' + parts.join(' + ') + ') — إجمالي تقريبي: ' + fmt(grandTotal)
            : 'لم يُختر أي خدمة أو مجموعة بعد.';
        $('#doctor-invoice-preview-text').text(text);
    }

    window.openDoctorInvoiceModal = function (opts) {
        opts = opts || {};
        $('#doctor_invoice_return_to').val(opts.returnTo || 'invoices');
        $('#doctor_invoice_appointment_id').val(opts.appointmentId || '');
        if (opts.patientId) {
            $('#doctor_invoice_patient_id').val(opts.patientId).trigger('change');
        }
        if (opts.serviceIds && opts.serviceIds.length) {
            $('#doctor_invoice_service_ids').val(opts.serviceIds).trigger('change');
        }
        updatePreview();
        $('#doctorAddInvoiceModal').modal('show');
    };

    $(function () {
        if ($.fn.select2) {
            $('.doctor-invoice-select2').select2({
                width: '100%',
                dir: 'rtl',
                dropdownParent: $('#doctorAddInvoiceModal')
            });
            $('.doctor-invoice-multi-select').select2({
                width: '100%',
                dir: 'rtl',
                placeholder: '— اختر —',
                allowClear: true,
                dropdownParent: $('#doctorAddInvoiceModal')
            });
        }

        $('#doctor_invoice_service_ids, #doctor_invoice_group_ids').on('change', updatePreview);

        $(document).on('click', '.btn-open-doctor-invoice-modal', function (e) {
            e.preventDefault();
            var serviceId = $(this).data('service-id');
            openDoctorInvoiceModal({
                patientId: $(this).data('patient-id'),
                appointmentId: $(this).data('appointment-id'),
                returnTo: $(this).data('return-to') || 'invoices',
                serviceIds: serviceId ? [String(serviceId)] : []
            });
        });

        $(document).on('click', '.doctor-invoice-prefill', function () {
            openDoctorInvoiceModal({
                patientId: $(this).data('patient-id'),
                appointmentId: $(this).data('appointment-id'),
                returnTo: 'queue'
            });
        });

        $('#doctor-invoice-form').on('submit', function (e) {
            var services = ($('#doctor_invoice_service_ids').val() || []).length;
            var groups = ($('#doctor_invoice_group_ids').val() || []).length;
            if (!services && !groups) {
                e.preventDefault();
                alert('يرجى اختيار خدمة مفردة أو مجموعة خدمات واحدة على الأقل.');
            }
        });

        $('#doctorAddInvoiceModal').on('hidden.bs.modal', function () {
            $('#doctor-invoice-form')[0].reset();
            $('#doctor_invoice_appointment_id').val('');
            $('#doctor_invoice_return_to').val('invoices');
            if ($('.doctor-invoice-select2').data('select2')) {
                $('.doctor-invoice-select2').val('').trigger('change');
            }
            if ($('.doctor-invoice-multi-select').data('select2')) {
                $('.doctor-invoice-multi-select').val(null).trigger('change');
            }
            updatePreview();
        });

        updatePreview();

        @if(request()->filled('patient_id') || request()->filled('appointment_id'))
        openDoctorInvoiceModal({
            patientId: @json(request('patient_id')),
            appointmentId: @json(request('appointment_id')),
            serviceIds: @json(request('Service_id') ? [request('Service_id')] : []),
            returnTo: @json(request('return_to', 'invoices'))
        });
        @endif
    });
})();
</script>
