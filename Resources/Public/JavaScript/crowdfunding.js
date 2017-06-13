(function($, w, undefined){

    'use strict';

    var T3 = w.T3 || {};
    var pledgeId = 0;

    $(document).ready(function() {
        $('.js__pledge').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();
                pledgeId = $(this).data('pledgeid');
                var description = $(this).data('pledgetitle');
                var amount = $(this).data('pledgeamount');
                // Open Checkout with further options:
                handler.open({
                    name: T3.settings.stripe.name,
                    description: description,
                    zipCode: true,
                    currency: T3.settings.stripe.currency,
                    amount: amount * 100
                });
            });
        });
    });

    var handler = StripeCheckout.configure({
        key: T3.settings.stripe.publishableKey,
        image: T3.settings.stripe.image,
        locale: 'auto',
        token: function(token) {
            console.log('token');
            console.log(token);
            console.log('pledgeId = ' + pledgeId);
            // You can access the token ID with `token.id`.
            // Get the token ID to your server-side code for use.
            $.post(T3.settings.uriAjax, {
                method: 'charge',
                pledgeId: pledgeId,
                token: token
            }, function(data, textStatus, jqXHR) {
                console.log(data);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log('error: ' + textStatus);
            });
        }
    });

    // Close Checkout on page navigation:
    window.addEventListener('popstate', function() {
        handler.close();
    });
})(jQuery, window);