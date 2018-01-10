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

use BitBag\SyliusAdyenPlugin\Action\CaptureAction;
use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Payum;
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactory;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\GatewayAwareInterface;

final class CaptureActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(CaptureAction::class);
    }

    function it_implements_action_interface(): void
    {
        $this->shouldHaveType(ActionInterface::class);
    }

    function it_implements_generic_token_factory_aware(): void
    {
        $this->shouldHaveType(GenericTokenFactoryAwareInterface::class);
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
        Capture $request,
        \ArrayObject $arrayObject,
        PaymentInterface $payment,
        TokenInterface $token,
        TokenInterface $notifyToken,
        TokenInterface $refundToken,
        Payum $payum,
        GenericTokenFactory $genericTokenFactory,
        GatewayInterface $gateway,
        AdyenBridgeInterface $adyenBridge
    ): void
    {
        $this->setGateway($gateway);

        $adyenBridge->verifyRequest([], [])->willReturn(false);

        $adyenBridge->getApiEndpoint()->willReturn('www.example.com');
        $adyenBridge->prepareFields(['resURL' => 'url', 'notifyURL' => 'url', 'extraData' => '{"captureToken":"test","refundToken":"test","notifyToken":"test"}'])->willReturn([]);

        $this->setApi($adyenBridge);

        $notifyToken->getTargetUrl()->willReturn('url');
        $notifyToken->getHash()->willReturn('test');

        $refundToken->getHash()->willReturn('test');
        $refundToken->getTargetUrl()->willReturn('url');

        $token->getTargetUrl()->willReturn('url');
        $token->getGatewayName()->willReturn('test');
        $token->getDetails()->willReturn([]);
        $token->getHash()->willReturn('test');

        $genericTokenFactory->createNotifyToken('test', [])->willReturn($notifyToken);
        $genericTokenFactory->createRefundToken('test', [])->willReturn($refundToken);

        $this->setGenericTokenFactory($genericTokenFactory);

        $payum->getTokenFactory()->willReturn($genericTokenFactory);

        $request->getModel()->willReturn($arrayObject);
        $request->getFirstModel()->willReturn($payment);
        $request->getToken()->willReturn($token);

        $this
            ->shouldThrow(HttpPostRedirect::class)
            ->during('execute', [$request])
        ;
    }

    function it_supports_only_capture_request_and_array_access(
        Capture $request,
        \ArrayAccess $arrayAccess
    ): void
    {
        $request->getModel()->willReturn($arrayAccess);

        $this->supports($request)->shouldReturn(true);
    }
}
