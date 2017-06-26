#customsubcategory=stripe=LLL:EXT:crowdfunding/Resources/Private/Language/locallang.xlf:constants.subcategory.stripe
#customsubcategory=currency=LLL:EXT:crowdfunding/Resources/Private/Language/locallang.xlf:constants.subcategory.currency

plugin.tx_crowdfunding_crowdfunding {
    view {
        # cat=plugin.tx_crowdfunding_crowdfunding/file; type=string; label=Path to template root (FE)
        templateRootPath = EXT:crowdfunding/Resources/Private/Templates/
        # cat=plugin.tx_crowdfunding_crowdfunding/file; type=string; label=Path to template partials (FE)
        partialRootPath = EXT:crowdfunding/Resources/Private/Partials/
        # cat=plugin.tx_crowdfunding_crowdfunding/file; type=string; label=Path to template layouts (FE)
        layoutRootPath = EXT:crowdfunding/Resources/Private/Layouts/
    }
    persistence {
        # cat=plugin.tx_crowdfunding_crowdfunding//a; type=string; label=Default storage PID
        storagePid =
    }
    settings {
        # cat=plugin.tx_crowdfunding_crowdfunding/currency/100; type=string; label=The currency symbol, for example £ or €. This symbol is usually appended after the numeric value.
        currencySign = €
        # cat=plugin.tx_crowdfunding_crowdfunding/currency/110; type=string; label=Decimal separator, which separates (for example) Euro and Cent.
        decimalSeparator = ,
        # cat=plugin.tx_crowdfunding_crowdfunding/currency/120; type=string; label=Character to be used as a thousands separator.
        thousandsSeparator = 
        # cat=plugin.tx_crowdfunding_crowdfunding/currency/130; type=string; label=Set to 1 to indicate that the currency symbol should be placed before the numeric value.
        prependCurrency = 0
        # cat=plugin.tx_crowdfunding_crowdfunding/currency/140; type=string; label=If this property is 1, a space character will be placed between the currency symbol and the numeric value.
        separateCurrency = 1
        # cat=plugin.tx_crowdfunding_crowdfunding/currency/150; type=string; label=To how many decimal places should the number be rounded?
        decimals = 0
        # cat=plugin.tx_crowdfunding_crowdfunding/stripe/100; type=string; label=Your secret API key (test or live)
        stripe.secretKey = 
        # cat=plugin.tx_crowdfunding_crowdfunding/stripe/110; type=string; label=Your publishable API key (test or live)
        stripe.publishableKey =
        # cat=plugin.tx_crowdfunding_crowdfunding/stripe/120; type=string; label=The currency of the amount (3-letter ISO code). The default is EUR.
        stripe.currency = EUR
        # cat=plugin.tx_crowdfunding_crowdfunding/stripe/130; type=string; label=The name of your company or website to show in payment
        stripe.name =
        # cat=plugin.tx_crowdfunding_crowdfunding/stripe/140; type=string; label=URL pointing to a square image of your brand or product: The recommended minimum size is 128x128px. (supported image types .gif, .jpeg, and .png)
        stripe.image =
        # cat=plugin.tx_crowdfunding_crowdfunding/advanced/150; type=string; label=Ajax page type (for ajax javascript calls)
        ajaxPageType = 201706131
        # cat=plugin.tx_crowdfunding_crowdfunding/advanced/160; type=string; label=Admin email
        adminEmail = 
    }
}
