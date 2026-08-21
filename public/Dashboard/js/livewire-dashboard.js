(function ($) {
    'use strict';

    var livewireTableSelectors = [
        '#hms-single-invoices',
        '#hms-group-invoices',
        '#hms-group-services-table'
    ];

    function destroyLivewireDataTables() {
        if (typeof $ === 'undefined' || !$.fn.DataTable) {
            return;
        }

        livewireTableSelectors.forEach(function (selector) {
            var $table = $(selector);
            if ($table.length && $.fn.DataTable.isDataTable(selector)) {
                $table.DataTable().clear().destroy();
                $table.removeClass('dataTable no-footer');
            }
        });
    }

    window.addEventListener('hms-close-modal', function (event) {
        var modalId = event.detail && event.detail.modalId;
        if (!modalId || typeof $ === 'undefined') {
            return;
        }
        var $modal = $('#' + modalId);
        if ($modal.length) {
            $modal.modal('hide');
        }
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    });

    document.addEventListener('livewire:load', function () {
        destroyLivewireDataTables();

        if (typeof Livewire === 'undefined') {
            console.error('Livewire failed to load on this page.');
            return;
        }

        Livewire.hook('message.processed', function () {
            destroyLivewireDataTables();
            $('[data-toggle="tooltip"]').tooltip();
        });

        Livewire.hook('message.failed', function (message, component) {
            console.error('Livewire request failed', message, component);
        });
    });

    $(function () {
        destroyLivewireDataTables();
    });
})(window.jQuery);
