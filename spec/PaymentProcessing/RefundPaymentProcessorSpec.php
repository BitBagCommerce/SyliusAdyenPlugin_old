<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusAdyenPlugin\PaymentProcessing;

use BitBag\SyliusAdyenPlugin\AdyenGatewayFactory;
use BitBag\SyliusAdyenPlugin\PaymentProcessing\PaymentProcessorInterface;
use BitBag\SyliusAdyenPlugin\PaymentProcessing\RefundPaymentProcessor;
use Payum\Core\GatewayInterface;
use Payum\Core\Model\GatewayConfigInterface;
use Payum\Core\Payum;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Storage\StorageInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class RefundPaymentProcessorSpec extends ObjectBehavior
{
    function let(Payum $payum, Session $session): void
    {
        $this->beConstructedWith($payum, $session);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RefundPaymentProcessor::class);
    }

    function it_implements_action_interface(): void
    {
        $this->shouldHaveType(PaymentProcessorInterface::class);
    }

    function it_process(
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
        GatewayConfigInterface $gatewayConfig,
        Payum $payum,
        StorageInterface $storage,
        TokenInterface $token,
        GatewayInterface $gateway
    ): void
    {
        $gatewayConfig->getFactoryName()->willReturn(AdyenGatewayFactory::FACTORY_NAME);

        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);

        $payment->getDetails()->willReturn([
            'extraData' => '{"refundToken":"test"}'
        ]);
        $payment->getMethod()->willReturn($paymentMethod);

        $token->getGatewayName()->willReturn('Adyen');

        $storage->find('test')->willReturn($token);

        $payum->getTokenStorage()->willReturn($storage);
        $payum->getGateway('Adyen')->willReturn($gateway);

        $this->process($payment);
    }
}