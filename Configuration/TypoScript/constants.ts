
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
        currencySign = €
        decimalSeparator = ,
        thousandsSeparator = 
        prependCurrency = 0
        separateCurrency = 1
        decimals = 0
    }
}
