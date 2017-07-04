# TYPO3 crowdfunding

[![TYPO3](https://img.shields.io/badge/TYPO3-8.7.0-orange.svg?style=flat-square)](https://typo3.org/) [![Build Status](https://travis-ci.org/pixelant/crowdfunding.svg?branch=master)](https://travis-ci.org/pixelant/crowdfunding)

## TYPO3 crowdfunding (crowdfunding)
This extension provides crowdfunding capability to TYPO3 CMS, and transactions are done through Stripe (https://stripe.com/).

## Functional tests

Are set to use db on localhost, to add user and privileges:

    CREATE USER 'crowdfunding'@'localhost' IDENTIFIED BY 'crowdfunding1234';

    GRANT ALL ON `crowdfunding_%`.* TO `crowdfunding`@`localhost`;

## Documentation

For all kind of documentation which covers install to how to develop the extension:

[Local Documentation](Documentation/Index.rst)

