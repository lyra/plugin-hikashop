document.addEventListener('DOMContentLoaded', function () {
    var pattern = /^payment_radio_\d+_\d+__payzenmulti/;
    var radios = Array.from(document.querySelectorAll('input.hikashop_checkout_payment_radio')).filter(function (el) {
        return pattern.test(el.id);
    });

    radios.forEach(function (radio) {
        // Search in parent element
        var parent = radio.parentElement;
        var submitDiv = parent ? parent.querySelector('.hikashop_checkout_payment_submit') : null;

        // If not found, search in next siblings
        if (!submitDiv) {
            var el = radio.nextElementSibling;
            while (el) {
                if (el.classList.contains('hikashop_checkout_payment_submit')) {
                    submitDiv = el;
                    break;
                }
                el = el.nextElementSibling;
            }
        }

        if (submitDiv) {
            submitDiv.style.display = 'none';
        }
    });
});

