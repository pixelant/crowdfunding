(function($, w, undefined){

    'use strict';

    var T3 = w.T3 || {};
    var campaignId = 0;
    var pledgeId = 0;

    $(document).ready(function() {
        $('.js__pledge').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();
                campaignId = $(this).data('campaignid');
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
        $('.js_update-numbers').on('click', function(e) {
            e.preventDefault();
            var campaignId = $(this).data('campaignid');
            updateCampaignNumbers(campaignId);
        });
    });

    var handler = StripeCheckout.configure({
        key: T3.settings.stripe.publishableKey,
        image: T3.settings.stripe.image,
        locale: 'auto',
        token: function(token) {
            // You can access the token ID with `token.id`.
            // Get the token ID to your server-side code for use.
            $.post(T3.settings.uriAjax, {
                method: 'charge',
                campaignId: campaignId,
                pledgeId: pledgeId,
                stripeToken: token
            }, function(data, textStatus, jqXHR) {
                console.log(data);
                updateCampaignNumbers(campaignId);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log('error: ' + textStatus);
            });
        }
    });

    // Close Checkout on page navigation:
    window.addEventListener('popstate', function() {
        campaignId = 0;
        pledgeId = 0;
        handler.close();
    });

    function updateCampaignNumbers(campaignId) {
        $.post(T3.settings.uriAjax, {
            method: 'campaignNumbers',
            campaignId: campaignId,
            toString: 1
        }, function(data, textStatus, jqXHR) {
            if (data.success =! 1) {
                console.log('Failed to fetch numbers for campaign (' + campaignId + ')');
            } else {
                for (var key in data.message){
                    if (data.message.hasOwnProperty(key)) {
                        console.log("Key is " + key + ", value is" + data.message[key]);
                        var obj = $('[data-identifier="' + key + '"]');
                        if (obj) {
                            obj.html(data.message[key]);
                        }
                        
                    }
                }
            }
            /*$(data.message).each(function() {
                console.log('this');
                console.log(this);
            });*/
            // $('[data-identifier="totalamountbacked"]').value(data.totalAmount)
            // console.log(data);
        }, 'json'
        ).fail(function(jqXHR, textStatus, errorThrown) {
            console.log('updateCampaignNumbers failed : ' + textStatus);
        });
    }
})(jQuery, window);