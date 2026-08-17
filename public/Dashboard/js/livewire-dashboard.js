(function ($) {
    'use strict';

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
        if (typeof Livewire === 'undefined') {
            return;
        }
        Livewire.hook('message.processed', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });
})(window.jQuery);
