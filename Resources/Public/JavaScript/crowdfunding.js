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
        $('.js__pledge').each(function() {
            $(this).on('click', function(e) {
                e.preventDefault();

                campaignId = $(this).data('campaignid');
                pledgeId = $(this).data('pledgeid');
                amount = $(this).data('pledgeamount');
                amountStr = $(this).data('pledgeamountstr');
                description = $(this).data('pledgetitle');

                $.post(T3.settings.uriAjax, {
                    method: 'checksum',
                    campaignId: campaignId,
                    pledgeId: pledgeId,
                    amount: amount
                }, function(data, textStatus, jqXHR) {
                    if (data.success != 1) {
                        swal('could not generate checksum', 'error');
                    } else {
                        //callback(checksum);
                        checksum = data.message;
                        // Open Checkout with further options:
                        handler.open({
                            name: T3.settings.stripe.name,
                            description: description,
                            zipCode: true,
                            currency: T3.settings.stripe.currency,
                            amount: amount * 100
                        });
                    }
                }, 'json'
                ).fail(function(jqXHR, textStatus, errorThrown) {
                    swal('could not generate checksum:' + errorThrown, 'error');
                });                        
            });
        });

        $('.js_update-numbers').on('click', function(e) {
            e.preventDefault();
            var campaignId = $(this).data('campaignid');
            updateCampaignNumbers(campaignId);
        });

        $('[data-identifier="backamount"]').on('input', function(e) {
            validateCustomAmount()
        });

        $('[data-identifier="backamount"]').on('keypress', function(e) {
            if (validateCustomAmount()) {
                $('[data-identifier="backcampaign"]').trigger('click');
            }
        });

        $('[data-identifier="backcampaign"]').on('click', function(e) {
            e.preventDefault();
            var backInput = $('[data-identifier="backamount"]').first();

            if (backInput.exists()) {
                amount = $(backInput).val();
                campaignId = $(backInput).data('campaignid');

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
                        $.post(T3.settings.uriAjax, {
                            method: 'checksum',
                            campaignId: campaignId,
                            pledgeId: 0,
                            amount: amount
                        }, function(data, textStatus, jqXHR) {
                            if (data.success != 1) {
                                swal('could not generate checksum', 'error');
                            } else {
                                //callback(checksum);
                                checksum = data.message;
                                // Open Checkout with further options:
                                handler.open({
                                    name: T3.settings.stripe.name,
                                    description: description,
                                    zipCode: true,
                                    currency: T3.settings.stripe.currency,
                                    amount: amount * 100
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
            
                // Generate a checksum

                // stripe open

            } else {
                console.log('else');
            }
            /*
            console.log('campaignId : ' + campaignId);

            $.post(T3.settings.uriAjax, {
                method: 'checksum',
                campaignId: campaignId,
                pledgeId: pledgeId,
                amount: amount
            }, function(data, textStatus, jqXHR) {
                if (data.success != 1) {
                    swal('could not generate checksum', 'error');
                } else {
                    //callback(checksum);
                    checksum = data.message;
                    swal('backcampaign amount : ' + amount + ', checksum : ' + checksum );
                }
            }, 'json'
            ).fail(function(jqXHR, textStatus, errorThrown) {
                swal('could not generate checksum:' + errorThrown, 'error');
            });
            */
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
        // Let strip close...
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
            stripeToken: token,
            checksum: checksum
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
        checksum = '';
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

    function generateChecksum(campaignId, pledgeId, amount) {
        $.post(T3.settings.uriAjax, {
            method: 'checksum',
            campaignId: campaignId,
            pledgeId: pledgeId,
            amount: amount
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
                $('[data-identifier="backcampaign"]').prop("disabled", false);
                isValid = true;
            }
        }
        return isValid;
    }
})(jQuery, swal, window);
