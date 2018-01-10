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

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

final class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function setAdyenPlatform(string $platform): void
    {
        $this->getDocument()->selectFieldOption('Platform', $platform);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdyenMerchantAccount(string $merchantAccount): void
    {
        $this->getDocument()->fillField('Merchant account', $merchantAccount);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdyenHmacKey(string $hmacKey): void
    {
        $this->getDocument()->fillField('HMAC key', $hmacKey);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdyenSkinCode(string $skinCode): void
    {
        $this->getDocument()->fillField('Skin code', $skinCode);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdyenHmacNotification(string $hmacNotification): void
    {
        $this->getDocument()->fillField('HMAC notification', $hmacNotification);
    }

    /**
     * {@inheritdoc}
     */
    public function setWsUser(string $wsUser): void
    {
        $this->getDocument()->fillField('WS user', $wsUser);
    }

    /**
     * {@inheritdoc}
     */
    public function setWsUserPassword(string $wsUserPassword): void
    {
        $this->getDocument()->fillField('WS user password', $wsUserPassword);
    }
}