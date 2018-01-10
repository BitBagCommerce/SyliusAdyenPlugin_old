<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $environment
     */
    public function setAdyenPlatform(string $environment): void;

    /**
     * @param string $merchantAccount
     */
    public function setAdyenMerchantAccount(string $merchantAccount): void;

    /**
     * @param string $hmacKey
     */
    public function setAdyenHmacKey(string $hmacKey): void;

    /**
     * @param string $skinCode
     */
    public function setAdyenSkinCode(string $skinCode): void;

    /**
     * @param string $hmacNotification
     */
    public function setAdyenHmacNotification(string $hmacNotification): void;

    /**
     * @param string $wsUser
     */
    public function setWsUser(string $wsUser): void;

    /**
     * @param string $wsUserPassword
     */
    public function setWsUserPassword(string $wsUserPassword): void;
}