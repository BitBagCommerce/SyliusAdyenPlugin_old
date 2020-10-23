<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusAdyenPlugin\Bridge;

use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridge;
use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use PhpSpec\ObjectBehavior;

final class AdyenBridgeSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith([
            'skinCode' => 'test',
            'merchantAccount' => 'test',
            'environment' => 'test',
            'hmacKey' => 111,
            'notification_hmac' => 111,
            'ws_user_password' => 'test',
            'ws_user' => 'test',
        ]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AdyenBridge::class);
    }

    function it_implements_adyen_bridge_interface(): void
    {
        $this->shouldHaveType(AdyenBridgeInterface::class);
    }

    function it_get_api_endpoint(): void
    {
        $this->getApiEndpoint()->shouldReturn('https://test.adyen.com/hpp/select.shtml');
    }

    function it_failed_to_verify_sign(): void
    {
        $this->verifySign(['merchantSig' => 'test'])->shouldReturn(false);
    }

    function it_failed_to_verify_request(): void
    {
        $data = [
            'merchantReference' => 'test',
            'authResult' => 'test',
            'merchantSig' => 'test',
        ];

        $this->verifyRequest($data, $data)->shouldReturn(false);
    }
}
