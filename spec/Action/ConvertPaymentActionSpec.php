<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusAdyenPlugin\Action;

use BitBag\SyliusAdyenPlugin\Action\ConvertPaymentAction;
use Payum\Core\Action\ActionInterface;
use PhpSpec\ObjectBehavior;
use Payum\Core\Request\Convert;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class ConvertPaymentActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ConvertPaymentAction::class);
    }

    function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ActionInterface::class);
    }

    function it_executes(
        Convert $request,
        PaymentInterface $payment,
        OrderInterface $order,
        CustomerInterface $customer
    ): void
    {
        $customer->getEmail()->willReturn('user@example.com');
        $customer->getId()->willReturn(1);

        $order->getNumber()->willReturn(000001);
        $order->getCustomer()->willReturn($customer);
        $order->getLocaleCode()->willReturn('en_Us');
        $order->getCurrencyCode()->willReturn('USD');
        $order->getShippingAddress()->willReturn(null);

        $payment->getOrder()->willReturn($order);
        $payment->getId()->willReturn(1);
        $payment->getAmount()->willReturn(445535);

        $request->getSource()->willReturn($payment);
        $request->getTo()->willReturn('array');
        $request->setResult([
            'merchantReference' => '1-1', 
            'paymentAmount' => 445535, 
            'shopperEmail' => 'user@example.com', 
            'currencyCode' => 'USD', 
            'shopperReference' => 1,
            'shopperLocale' => 'en_Us',
            'countryCode' => null
        ])->shouldBeCalled();

        $this->execute($request);
    }

    function it_supports_only_convert_request_payment_source_and_array_to(
        Convert $request,
        PaymentInterface $payment
    ): void
    {
        $request->getSource()->willReturn($payment);
        $request->getTo()->willReturn('array');

        $this->supports($request)->shouldReturn(true);
    }
}
