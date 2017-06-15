(function($, swal, w, undefined){

    'use strict';

    var T3 = w.T3 || {};
    var campaignId = 0;
    var pledgeId = 0;
    var amount = 0;
    var amountStr = '';
    var description = '';

    $(document).ready(function() {
        $('.js__pledge').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();

                campaignId = $(this).data('campaignid');
                pledgeId = $(this).data('pledgeid');
                amount = $(this).data('pledgeamount');
                amountStr = $(this).data('pledgeamountstr');
                description = $(this).data('pledgetitle');

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
        token: displayConfirmCharge
    });

    // Close Checkout on page navigation:
    window.addEventListener('popstate', function() {
        resetVars();
        handler.close();
    });

    function displayConfirmCharge(token) {
        setTimeout(function(){
            swal({
                title: "Proceed",
                text: "Proceed and charge card with " + amountStr,
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                doCharge(token);
            });
        }, 500);
    }

    function doCharge(token) {
        $.post(T3.settings.uriAjax, {
            method: 'charge',
            campaignId: campaignId,
            pledgeId: pledgeId,
            amount: amount,
            stripeToken: token
        }, chargeResponseProcess
        , 'json'
        ).fail(chargeResponseFailed);
    }

    function chargeResponseProcess(data, textStatus, jqXHR) {
        updateCampaignNumbers(campaignId);
        if (data.success != 1) {
            // TODO: proper title
            swal("FAIL Your message here…", data.message, 'error');
        } else {
            // TODO: proper title
            swal("Your message here…", data.message, 'success');
        }
        resetVars();
    }

    function chargeResponseFailed(jqXHR, textStatus, errorThrown) {
        // TODO: proper error message
        swal(errorThrown, textStatus, 'error');
        resetVars();
    }

    function resetVars() {
        campaignId = 0;
        pledgeId = 0;
        amount = 0;
        amountStr = '';
        description = '';
    }

    function updateCampaignNumbers(campaignId) {
        $.post(T3.settings.uriAjax, {
            method: 'campaignNumbers',
            campaignId: campaignId,
            toString: 1
        }, function(data, textStatus, jqXHR) {
            if (data.success != 1) {
                console.log('Failed to fetch numbers for campaign (' + campaignId + ')');
            } else {
                for (var key in data.message){
                    if (data.message.hasOwnProperty(key)) {
                        var obj = $('[data-identifier="' + key + '"]');
                        if (obj) {
                            obj.html(data.message[key]);
                        }
                    }
                }
            }
        }, 'json'
        ).fail(function(jqXHR, textStatus, errorThrown) {
            console.log('updateCampaignNumbers failed : ' + textStatus);
        });
    }

})(jQuery, swal, window);
