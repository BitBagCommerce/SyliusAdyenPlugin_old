<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\PaymentInterface;
use Tests\BitBag\SyliusAdyenPlugin\Behat\Page\External\AdyenCheckoutPageInterface;
use Webmozart\Assert\Assert;

final class AdyenCheckoutContext implements Context
{
    /**
     * @var CompletePageInterface
     */
    private $summaryPage;

    /**
     * @var AdyenCheckoutPageInterface
     */
    private $adyenCheckoutPage;

    /**
     * @var ShowPageInterface
     */
    private $orderDetails;

    /**
     * @var EntityRepository
     */
    private $paymentRepository;

    /**
     * @param CompletePageInterface $summaryPage
     * @param AdyenCheckoutPageInterface $adyenCheckoutPage
     * @param ShowPageInterface $orderDetails
     * @param EntityRepository $paymentRepository
     */
    public function __construct(
        CompletePageInterface $summaryPage,
        AdyenCheckoutPageInterface $adyenCheckoutPage,
        ShowPageInterface $orderDetails,
        EntityRepository $paymentRepository
    )
    {
        $this->summaryPage = $summaryPage;
        $this->adyenCheckoutPage = $adyenCheckoutPage;
        $this->orderDetails = $orderDetails;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @When I confirm my order with Adyen payment
     * @Given I have confirmed my order with Adyen payment
     */
    public function iConfirmMyOrderWithAdyenPayment(): void
    {
        $this->summaryPage->confirmOrder();
    }

    /**
     * @When I sign in to Adyen and pay successfully
     */
    public function iSignInToAdyenAndPaySuccessfully(): void
    {
        $this->adyenCheckoutPage->pay();
    }

    /**
     * @When I cancel my Adyen payment
     * @Given I have cancelled Adyen payment
     */
    public function iCancelMyAdyenPayment(): void
    {
        $this->adyenCheckoutPage->cancel();
    }

    /**
     * @When I try to pay again Adyen payment
     */
    public function iTryToPayAgainAdyenPayment(): void
    {
        $this->orderDetails->pay();
    }

    /**
     * @Then I should get a notification of a successful transaction
     */
    public function iShouldGetANotificationOfASuccessfulTransaction(): void
    {
        $this->adyenCheckoutPage->successNotify();
    }

    /**
     * @Then Payment status should has been completed
     */
    public function paymentStatusShouldHasBeenCompleted(): void
    {
        /** @var PaymentInterface[] $payments */
        $payments = $this->paymentRepository->findAll();

        Assert::true(0 < count($payments));

        foreach ($payments as $payment) {
            Assert::true(PaymentInterface::STATE_COMPLETED === $payment->getState());
        }
    }
}