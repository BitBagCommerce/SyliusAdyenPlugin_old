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

use BitBag\SyliusAdyenPlugin\Action\RefundAction;
use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use BitBag\SyliusAdyenPlugin\Bridge\ModificationRequestAdyenBridgeInteface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Refund;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;

final class RefundActionSpec extends ObjectBehavior
{
    function let(ModificationRequestAdyenBridgeInteface $modificationRequestAdyenBridge): void
    {
        $this->beConstructedWith($modificationRequestAdyenBridge);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RefundAction::class);
    }

    function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ActionInterface::class);
    }

    function it_implements_api_aware_interface(): void
    {
        $this->shouldHaveType(ApiAwareInterface::class);
    }

    function it_implements_gateway_aware_interface(): void
    {
        $this->shouldHaveType(GatewayAwareInterface::class);
    }

    function it_executes(
        Refund $request,
        \ArrayObject $arrayObject,
        GatewayInterface $gateway,
        PaymentInterface $payment,
        ModificationRequestAdyenBridgeInteface $modificationRequestAdyenBridge,
        AdyenBridgeInterface $adyenBridge
    ): void
    {

        $arrayObject = ArrayObject::ensureArrayObject($arrayObject);

        $arrayObject['currencyCode'] = 'USD';
        $arrayObject['paymentAmount'] = 1000;
        $arrayObject['pspReference'] = 'test';
        $arrayObject['merchantReference'] = 'test';

        $arrayObject->toUnsafeArray();

        $request->getModel()->willReturn($arrayObject);

        $this->setApi($adyenBridge);

        $modificationRequestAdyenBridge->refundRequest(
            $adyenBridge,
            [
                "currency" => 'USD',
                "value" => 1000,
            ],
            'test',
            'test'
        )->willReturn((object)[
            'refundResult' => (object)[
                'response' => ''
            ]
        ]);

        $this->setGateway($gateway);

        $request->getFirstModel()->willReturn($payment);

        $this->execute($request);
    }

    function it_supports_only_refund_request_and_array_access(
        Refund $request,
        \ArrayAccess $arrayAccess
    ): void
    {
        $request->getModel()->willReturn($arrayAccess);

        $this->supports($request)->shouldReturn(true);
    }
}
