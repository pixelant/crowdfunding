
plugin.tx_crowdfunding_crowdfunding {
    view {
        templateRootPaths.0 = EXT:crowdfunding/Resources/Private/Templates/
        templateRootPaths.1 = {$plugin.tx_crowdfunding_crowdfunding.view.templateRootPath}
        partialRootPaths.0 = EXT:crowdfunding/Resources/Private/Partials/
        partialRootPaths.1 = {$plugin.tx_crowdfunding_crowdfunding.view.partialRootPath}
        layoutRootPaths.0 = EXT:crowdfunding/Resources/Private/Layouts/
        layoutRootPaths.1 = {$plugin.tx_crowdfunding_crowdfunding.view.layoutRootPath}
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
            currencySign = {$plugin.tx_crowdfunding_crowdfunding.settings.currencySign}
            decimalSeparator = {$plugin.tx_crowdfunding_crowdfunding.settings.decimalSeparator}
            thousandsSeparator = {$plugin.tx_crowdfunding_crowdfunding.settings.thousandsSeparator}
            prependCurrency = {$plugin.tx_crowdfunding_crowdfunding.settings.prependCurrency}
            separateCurrency = {$plugin.tx_crowdfunding_crowdfunding.settings.separateCurrency}
            decimals = {$plugin.tx_crowdfunding_crowdfunding.settings.decimals}
        }
        stripe {
            name = {$plugin.tx_crowdfunding_crowdfunding.settings.stripe.name}
            currency = {$plugin.tx_crowdfunding_crowdfunding.settings.stripe.currency}
            secretKey = {$plugin.tx_crowdfunding_crowdfunding.settings.stripe.secretKey}
            publishableKey = {$plugin.tx_crowdfunding_crowdfunding.settings.stripe.publishableKey}
        }
        ajaxPageType = {$plugin.tx_crowdfunding_crowdfunding.settings.ajaxPageType}
    }
}

plugin.tx_crowdfunding {
    persistence {
        storagePid = {$plugin.tx_crowdfunding_crowdfunding.persistence.storagePid}
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
	typeNum = {$plugin.tx_crowdfunding_crowdfunding.settings.ajaxPageType}

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

plugin.tx_floorgulliespurus._CSS_DEFAULT_STYLE >



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
