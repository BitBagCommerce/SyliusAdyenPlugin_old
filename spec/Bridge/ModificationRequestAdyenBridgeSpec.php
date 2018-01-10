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

use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use BitBag\SyliusAdyenPlugin\Bridge\ModificationRequestAdyenBridge;
use BitBag\SyliusAdyenPlugin\Bridge\ModificationRequestAdyenBridgeInteface;
use PhpSpec\ObjectBehavior;

final class ModificationRequestAdyenBridgeSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ModificationRequestAdyenBridge::class);
    }

    function it_implements_adyen_bridge_interface(): void
    {
        $this->shouldHaveType(ModificationRequestAdyenBridgeInteface::class);
    }

    function it_refund_request(
        AdyenBridgeInterface $adyenBridge
    ): void
    {
        $adyenBridge->refundAction(
            [
                "currency" => 'USD',
                "value" => 1000,
            ],
            'test',
            'test'
        )->willReturn((object)[]);

        $this->refundRequest(
            $adyenBridge,
            [
                "currency" => 'USD',
                "value" => 1000,
            ],
            'test',
            'test'
        );
    }
}
