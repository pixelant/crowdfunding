<?php

namespace Pixelant\Crowdfunding\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Pixelant\Crowdfunding\Utility\CrowdfundingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class StripeUtility
{

    /**
     * Create a Stripe Customer
     *
     * @param string $email The email of the customer
     * @param string $token The id in token recieved from stripe
     *
     * @return \Stripe\Customer
     */
    public static function createCustomer(string $email, string $token)
    {
        if (!GeneralUtility::validEmail($email)) {
            throw new \Exception('The supplied email is not a valid email');
        }
        if (empty(trim($token))) {
            throw new \Exception('The supplied token is not a valid');
        }

        // Fetch and validate settings
        $settings = CrowdfundingUtility::getSettings();
        $stripeSecretKey = $settings['stripe']['secretKey'];
        \Stripe\Stripe::setApiKey($stripeSecretKey);

        return \Stripe\Customer::create([
            'email' => $email,
            'source'  => $token['id']
        ]);
    }

    /**
     * Create Stripe Charge
     *
     * @param \Stripe\Customer $customer The stripe cusomer to charge
     * @param float $amount Amount to charge
     * @param string $description Description of charge
     * @param array $metadata Optional metadata to add to charge
     *
     * @return \Stripe\Charge
     */
    public static function createCharge(\Stripe\Customer $customer, float $amount, string $description, array $metadata)
    {
        if ($amount <= 0) {
            throw new \Exception('The amount must be greater than 0');
        }

        // Fetch and validate settings
        $settings = CrowdfundingUtility::getSettings();
        $stripeCurrency = $settings['stripe']['currency'];
        $stripeSecretKey = $settings['stripe']['secretKey'];

        \Stripe\Stripe::setApiKey($stripeSecretKey);
        /*
        return \Stripe\Charge::create([
            'customer' => $customer->id,
            'amount'   => $amount * 100,
            'currency' => $stripeCurrency,
            'description' => $description,
            'metadata' => $metadata
        ]);
        */
    }
}
