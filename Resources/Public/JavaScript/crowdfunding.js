(function($, swal, w, undefined){

    'use strict';

    var T3 = w.T3 || {};
    var campaignId = 0;
    var pledgeId = 0;
    var amount = 0;
    var amountStr = '';
    var description = '';
    var checksum = '';

    $.fn.exists = function () {
        return this.length !== 0;
    }

    $(document).ready(function() {

        // add click event to pledge buttons 
        $('.js__pledge').each(function() {
            $(this).prop("disabled", T3.settings.stripe.disableStripe);
            $(this).on('click', function(e) {
                e.preventDefault();

                campaignId = $(this).data('campaignid');
                pledgeId = $(this).data('pledgeid');
                amount = $(this).data('pledgeamount');
                amountStr = $(this).data('pledgeamountstr');
                description = $(this).data('pledgetitle');

                // generate checksum
                $.post(T3.settings.uriAjax, {
                    method: 'checksum',
                    campaignId: campaignId,
                    pledgeId: pledgeId,
                    amount: amount
                }, function(data, textStatus, jqXHR) {
                    if (data.success != 1) {
                        swal('could not generate checksum', 'error');
                    } else {
                        checksum = data.message;
                        // open stripe checkout
                        handler.open({
                            name: T3.settings.stripe.name,
                            description: description,
                            zipCode: true,
                            currency: T3.settings.stripe.currency,
                            amount: amount * 100,
                            allowRememberMe: false
                        });
                    }
                }, 'json'
                ).fail(function(jqXHR, textStatus, errorThrown) {
                    swal('could not generate checksum:' + errorThrown, 'error');
                });
            });
        });

        // add click event to update numbers button
        $('.js_update-numbers').on('click', function(e) {
            e.preventDefault();
            var campaignId = $(this).data('campaignid');
            updateCampaignNumbers(campaignId);
        });

        // check input on back amount
        $('[data-identifier="backamount"]').on('input', function(e) {
            validateCustomAmount()
        });

        // check enter on back amount
        $('[data-identifier="backamount"]').on('keypress', function(e) {
            if (e.which === 13) {
                if (validateCustomAmount()) {
                    $('[data-identifier="backcampaign"]').trigger('click');
                }
            }
        });

        // disable buttons if no https
        $('[data-identifier="backcampaign"]').prop("disabled", T3.settings.stripe.disableStripe);
        // add click event for "free" amount button
        $('[data-identifier="backcampaign"]').on('click', function(e) {
            e.preventDefault();
            var backInput = $('[data-identifier="backamount"]').first();

            if (backInput.exists()) {
                amount = $(backInput).val();
                campaignId = $(backInput).data('campaignid');
                pledgeId = 0;
                amountStr = '';
                description = $(backInput).data('pledgetitle');

                // Check that amount is valid
                $.post(T3.settings.uriAjax, {
                    method: 'isAmountValid',
                    campaignId: campaignId,
                    amount: amount
                }, function(data, textStatus, jqXHR) {
                    if (data.success != 1) {
                        swal('Amount is not valid!', data.message, 'error');
                    } else {
                        amountStr = data.message;
                        // generate checksum
                        $.post(T3.settings.uriAjax, {
                            method: 'checksum',
                            campaignId: campaignId,
                            pledgeId: 0,
                            amount: amount
                        }, function(data, textStatus, jqXHR) {
                            if (data.success != 1) {
                                swal('could not generate checksum', 'error');
                            } else {
                                checksum = data.message;
                                // open stripe checkout
                                handler.open({
                                    name: T3.settings.stripe.name,
                                    description: description,
                                    zipCode: true,
                                    currency: T3.settings.stripe.currency,
                                    amount: amount * 100,
                                    allowRememberMe: false
                                });
                            }
                        }, 'json'
                        ).fail(function(jqXHR, textStatus, errorThrown) {
                            swal('could not generate checksum:' + errorThrown, 'error');
                        });
                    }
                }, 'json'
                ).fail(function(jqXHR, textStatus, errorThrown) {
                    swal('could not validate amount:' + errorThrown, 'error');
                });
            } else {
                console.log('else');
            }
        });
    });

    // handler for stripe chekout
    var handler = StripeCheckout.configure({
        key: T3.settings.stripe.publishableKey,
        image: T3.settings.stripe.image,
        locale: 'auto',
        token: displayConfirmCharge
    });

    // close checkout on page navigation
    window.addEventListener('popstate', function() {
        resetVars();
        handler.close();
    });

    // display confirm charge swal
    function displayConfirmCharge(token) {
        // Let strip close...
        setTimeout(function(){
            swal({
                title: T3.labels['label.confirmCharge.title'],
                text: T3.labels['label.confirmCharge.text'] + amountStr,
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                doCharge(token);
            });
        }, 500);
    }

    // charge
    function doCharge(token) {
        $.post(T3.settings.uriAjax, {
            method: 'charge',
            campaignId: campaignId,
            pledgeId: pledgeId,
            amount: amount,
            stripeToken: token,
            checksum: checksum
        }, chargeResponseProcess
        , 'json'
        ).fail(chargeResponseFailed);
    }

    // handle response from charge
    function chargeResponseProcess(data, textStatus, jqXHR) {
        updateCampaignNumbers(campaignId);
        if (data.success != 1) {
            swal(
                T3.labels['label.chargeResponse.failedTitle'],
                T3.labels['label.chargeResponse.failedText'] + ' ' + data.message,
                'error'
            );
        } else {
            swal(
                T3.labels['label.chargeResponse.successTitle'],
                data.message,
                'success'
            );
        }
        resetVars();
    }

    // handle fail of charge
    function chargeResponseFailed(jqXHR, textStatus, errorThrown) {
        // TODO: proper error message
        swal(errorThrown, textStatus, 'error');
        resetVars();
    }

    // reset variables
    function resetVars() {
        campaignId = 0;
        pledgeId = 0;
        amount = 0;
        amountStr = '';
        description = '';
        checksum = '';
    }

    // update campaign numbers
    function updateCampaignNumbers(campaignId) {
        $.post(T3.settings.uriAjax, {
            method: 'campaignNumbers',
            campaignId: campaignId,
            toString: 1
        }, function(data, textStatus, jqXHR) {
            if (data.success != 1) {
                console.log('Failed to fetch numbers for campaign (' + campaignId + ')');
            } else {
                // loop through data.message
                // key is used to find objects
                // property is used as html
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
            console.log(T3.labels['js.ajax.fail'] + ' updateCampaignNumbers failed : ' + errorThrown);
        });
    }

    // validate custom amount
    function validateCustomAmount() {
        var customAmount = $('[data-identifier="backamount"]');
        var isValid = false;
        if (customAmount.exists) {
            var checkAmount = $(customAmount).val();
            var checkCampaignId = $(customAmount).data('campaignid');
            var minAmount = $(customAmount).data('minamount');
            if (checkAmount < minAmount) {
                $(customAmount).parent().addClass('has-error');
                $(customAmount).focus();
                $('[data-identifier="backcampaign"]').prop("disabled", true);
            } else {
                $(customAmount).parent().removeClass('has-error');
                $('[data-identifier="backcampaign"]').prop("disabled", T3.settings.stripe.disableStripe);
                isValid = true;
            }
        }
        return isValid;
    }
})(jQuery, swal, window);
