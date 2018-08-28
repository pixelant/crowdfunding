plugin.tx_crowdfunding_crowdfunding {
    view {
        templateRootPaths.0 = EXT:crowdfunding/Resources/Private/Templates/
        partialRootPaths.0 = EXT:crowdfunding/Resources/Private/Partials/
        layoutRootPaths.0 = EXT:crowdfunding/Resources/Private/Layouts/
    }
    persistence {
        storagePid = 0
    }
    features {
        #skipDefaultArguments = 1
        # if set to 1, the enable fields are ignored in BE context
        ignoreAllEnableFieldsInBe = 0
        # Should be on by default, but can be disabled if all action in the plugin are uncached
        requireCHashArgumentForActionArguments = 1
    }
    mvc {
        #callDefaultActionIfActionCantBeResolved = 1
    }
    settings {
        currency {
            currencySign = $
            decimalSeparator = .
            thousandsSeparator = ,
            prependCurrency = 1
            separateCurrency = 1
            decimals = 0
        }
        stripe {
            secretKey = 1234
            publishableKey = 1234
            currency = USD
            name = FuncionalTestingOfCrowdfunding
            image =
        }
        ajaxPageType = 201706131
        adminEmail = 
    }
}

plugin.tx_crowdfunding {
        #recursive = 1
        classes {
            Pixelant\Crowdfunding\Domain\Model\Campaign {
                mapping {
                    tableName = tx_crowdfunding_domain_model_campaign
                    columns {
                        crdate.mapOnProperty = crdate
                    }
                }
            }
        }
    }
}

ajaxPage = PAGE
ajaxPage {
	typeNum = 201706131

	config {
		disableAllHeaderCode = 1
		additionalHeaders = Content-type:application/json
		xhtml_cleaning = 0
		admPanel = 0
		debug = 0
		no_cache = 1
	}
	10 < tt_content.list.20.crowdfunding_crowdfunding
    10.switchableControllerActions {
        Campaign {
            1 = ajax
        }
    }
}

page.includeCSS.crowdfunding = EXT:crowdfunding/Resources/Public/Css/styles.css
page.includeCSS.sweetalert = EXT:crowdfunding/Resources/Public/Css/sweetalert.css
page.includeJSFooter.stripecheckout = https://checkout.stripe.com/checkout.js
page.includeJSFooter.stripecheckout {
    external = 1
    disableCompression = 1
    excludeFromConcatenation = 1
}
page.includeJSFooter.sweetalert = EXT:crowdfunding/Resources/Public/JavaScript/sweetalert.min.js
page.includeJSFooter.crowdfunding = EXT:crowdfunding/Resources/Public/JavaScript/crowdfunding.js
