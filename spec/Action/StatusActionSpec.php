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

use BitBag\SyliusAdyenPlugin\Action\StatusAction;
use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetStatusInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;

final class StatusActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(StatusAction::class);
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
        GetStatusInterface $request,
        PaymentInterface $payment,
        GatewayInterface $gateway,
        AdyenBridgeInterface $adyenBridge
    ): void
    {
        $httpRequest = new GetHttpRequest();

        $gateway->execute($httpRequest);

        $adyenBridge->verifyRequest([], [])->willReturn(false);

        $this->setApi($adyenBridge);
        $this->setGateway($gateway);

        $payment->getDetails()->willReturn([]);

        $request->getModel()->willReturn($payment);

        $request->markNew()->shouldBeCalled();

        $this->execute($request);
    }

    function it_supports_only_get_status_request_and_array_access(
        GetStatusInterface $request,
        PaymentInterface $payment
    ): void
    {
        $request->getModel()->willReturn($payment);

        $this->supports($request)->shouldReturn(true);
    }
}
