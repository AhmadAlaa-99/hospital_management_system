<script>
(function () {
    var TAX_RATE_SINGLE = 17;

    function fmt(n) {
        return parseFloat(n || 0).toFixed(2);
    }

    function calcSinglePreview() {
        var price = parseFloat($('#doctor_invoice_service_id option:selected').data('price') || 0);
        var discount = 0;
        var taxRate = TAX_RATE_SINGLE;
        var subtotal = price - discount;
        var taxValue = subtotal * (taxRate / 100);
        $('#doctor_single_price').val(fmt(price));
        $('#doctor_single_discount').val(fmt(discount));
        $('#doctor_single_tax_rate').val(taxRate);
        $('#doctor_single_tax_value').val(fmt(taxValue));
        $('#doctor_single_total').val(fmt(subtotal + taxValue));
    }

    function calcGroupPreview() {
        var $opt = $('#doctor_invoice_group_id option:selected');
        var price = parseFloat($opt.data('price') || 0);
        var discount = parseFloat($opt.data('discount') || 0);
        var taxRate = parseFloat($opt.data('tax-rate') || 0);
        var subtotal = price - discount;
        var taxValue = subtotal * (taxRate / 100);
        $('#doctor_group_price').val(fmt(price));
        $('#doctor_group_discount').val(fmt(discount));
        $('#doctor_group_tax_rate').val(taxRate);
        $('#doctor_group_tax_value').val(fmt(taxValue));
        $('#doctor_group_total').val(fmt(subtotal + taxValue));
    }

    function setInvoiceType(type) {
        type = String(type);
        $('#doctor_invoice_type').val(type);
        var isSingle = type === '1';
        $('#doctor_invoice_service_id').prop('disabled', !isSingle).prop('required', isSingle);
        $('#doctor_invoice_group_id').prop('disabled', isSingle).prop('required', !isSingle);
        if (isSingle) {
            $('#doctor_invoice_group_id').val('');
            calcSinglePreview();
        } else {
            $('#doctor_invoice_service_id').val('');
            calcGroupPreview();
        }
    }

    window.openDoctorInvoiceModal = function (opts) {
        opts = opts || {};
        $('#doctor_invoice_appointment_id').val(opts.appointmentId || '');
        if (opts.patientId) {
            $('#doctor_invoice_patient_id').val(opts.patientId).trigger('change');
        }
        if (opts.invoiceType) {
            var tab = opts.invoiceType === '2' ? '#group-tab' : '#single-tab';
            $(tab).tab('show');
            setInvoiceType(opts.invoiceType);
        } else {
            $('#single-tab').tab('show');
            setInvoiceType('1');
        }
        if (opts.serviceId) {
            $('#doctor_invoice_service_id').val(opts.serviceId);
            calcSinglePreview();
        }
        $('#doctorAddInvoiceModal').modal('show');
    };

    $(function () {
        if ($.fn.select2 && $('.doctor-invoice-select2').length) {
            $('.doctor-invoice-select2').select2({
                width: '100%',
                dir: 'rtl',
                dropdownParent: $('#doctorAddInvoiceModal')
            });
        }

        $('#doctorInvoiceTypeTabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            setInvoiceType($(e.target).data('invoice-type'));
        });

        $('#doctor_invoice_service_id').on('change', calcSinglePreview);
        $('#doctor_invoice_group_id').on('change', calcGroupPreview);

        $(document).on('click', '.btn-open-doctor-invoice-modal', function (e) {
            e.preventDefault();
            openDoctorInvoiceModal({
                patientId: $(this).data('patient-id'),
                appointmentId: $(this).data('appointment-id'),
                serviceId: $(this).data('service-id')
            });
        });

        $(document).on('click', '.doctor-invoice-prefill', function () {
            openDoctorInvoiceModal({
                patientId: $(this).data('patient-id'),
                appointmentId: $(this).data('appointment-id')
            });
        });

        $('#doctorAddInvoiceModal').on('hidden.bs.modal', function () {
            $('#doctor-invoice-form')[0].reset();
            $('#doctor_invoice_appointment_id').val('');
            setInvoiceType('1');
            if ($('.doctor-invoice-select2').data('select2')) {
                $('.doctor-invoice-select2').val('').trigger('change');
            }
        });

        setInvoiceType('1');

        @if(request()->filled('patient_id') || request()->filled('appointment_id'))
        openDoctorInvoiceModal({
            patientId: @json(request('patient_id')),
            appointmentId: @json(request('appointment_id')),
            serviceId: @json(request('Service_id'))
        });
        @endif
    });
})();
</script>
