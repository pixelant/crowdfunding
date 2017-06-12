
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
    }
}

plugin.tx_crowdfunding {
    persistence {
        storagePid = {$plugin.tx_crowdfunding_crowdfunding.persistence.storagePid}
        #recursive = 1
    }
}
# these classes are only used in auto-generated templates
plugin.tx_crowdfunding._CSS_DEFAULT_STYLE (
    textarea.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    input.f3-form-error {
        background-color:#FF9F9F;
        border: 1px #FF0000 solid;
    }

    .tx-crowdfunding table {
        border-collapse:separate;
        border-spacing:10px;
    }

    .tx-crowdfunding table th {
        font-weight:bold;
    }

    .tx-crowdfunding table td {
        vertical-align:top;
    }

    .typo3-messages .message-error {
        color:red;
    }

    .typo3-messages .message-ok {
        color:green;
    }
)
