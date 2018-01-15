<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\PaymentProcessing;

use BitBag\SyliusAdyenPlugin\AdyenGatewayFactory;
use Payum\Core\Payum;
use Payum\Core\Request\Refund;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Symfony\Component\HttpFoundation\Session\Session;

final class RefundPaymentProcessor implements PaymentProcessorInterface
{
    /**
     * @var Payum
     */
    private $payum;

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Payum $payum
     * @param Session $session
     */
    public function __construct(Payum $payum, Session $session)
    {
        $this->payum = $payum;
        $this->session = $session;
    }

    /**
     * @param PaymentInterface $payment
     *
     * @throws UpdateHandlingException
     * @throws \Payum\Core\Reply\ReplyInterface
     */
    public function process(PaymentInterface $payment): void
    {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $payment->getMethod();

        if (AdyenGatewayFactory::FACTORY_NAME !== $paymentMethod->getGatewayConfig()->getFactoryName()) {
            return;
        }

        if (false === isset($payment->getDetails()['extraData'])) {
            $this->session->getFlashBag()->add("info", "The payment refund was made only locally.");
            return;
        }

        $hash = null !== $payment ? json_decode($payment->getDetails()['extraData'], true)['refundToken'] : '';

        if (false === $token = $this->payum->getTokenStorage()->find($hash)) {
            throw new UpdateHandlingException(sprintf("A token with hash `%s` could not be found.", $hash));
        }

        $gateway = $this->payum->getGateway($token->getGatewayName());

        $gateway->execute(new Refund($token));
    }
}
