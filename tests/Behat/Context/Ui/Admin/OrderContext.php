<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Order\ShowPageInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

final class OrderContext implements Context
{
    /**
     * @var ShowPageInterface
     */
    private $showPage;

    /**
     * @param ShowPageInterface $showPage
     */
    public function __construct(ShowPageInterface $showPage)
    {
        $this->showPage = $showPage;
    }

    /**
     * @When /^I go to (the order) for this payment$/
     *
     * @param OrderInterface $order
     * @throws \Sylius\Behat\Page\UnexpectedPageException
     */
    public function iGoToTheOrderForThisPayment(OrderInterface $order): void
    {
       $this->showPage->open(['id' => $order->getId()]);
    }

    /**
     * @Then I should see that this order has the method marked as :paymentState
     *
     * @param string $paymentState
     */
    public function iShouldSeeThatThisOrderHasTheMethodMarkedAsPaymentState(string $paymentState): void
    {
        Assert::true($this->showPage->hasPayment($paymentState));
    }
}