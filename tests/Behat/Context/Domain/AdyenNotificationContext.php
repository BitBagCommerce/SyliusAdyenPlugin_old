<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Tests\BitBag\SyliusAdyenPlugin\Behat\Page\External\AdyenCheckoutPageInterface;
use Webmozart\Assert\Assert;

final class AdyenNotificationContext implements Context
{
    /**
     * @var AdyenCheckoutPageInterface
     */
    private $adyenCheckoutPage;

    /**
     * @var EntityRepository
     */
    private $paymentRepository;

    /**
     * @param AdyenCheckoutPageInterface $adyenCheckoutPage
     * @param EntityRepository $paymentRepository
     */
    public function __construct(
        AdyenCheckoutPageInterface $adyenCheckoutPage,
        EntityRepository $paymentRepository
    )
    {
        $this->adyenCheckoutPage = $adyenCheckoutPage;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @Given /^there is a refunded Adyen payment$/
     *
     * @throws \Behat\Mink\Exception\DriverException
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function thereIsARefundedAdyenPayment(): void
    {
        $this->adyenCheckoutPage->successRefundedPaymentNotify();
    }
}