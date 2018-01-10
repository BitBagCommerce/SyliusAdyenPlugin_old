<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Mocker;

use BitBag\SyliusAdyenPlugin\Bridge\ModificationRequestAdyenBridgeInteface;
use Sylius\Behat\Service\Mocker\Mocker;

final class ModificationRequestAdyenBridgeMocker
{
    /**
     * @var Mocker
     */
    private $mocker;

    /**
     * @param Mocker $mocker
     */
    public function __construct(Mocker $mocker)
    {
        $this->mocker = $mocker;
    }

    public function refundRequest()
    {
        $modificationRequestAdyenBridge = $this->mocker
            ->mockService('bitbag_sylius_adyen_plugin.bridge.modification_request_adyen', ModificationRequestAdyenBridgeInteface::class)
        ;

        $modificationRequestAdyenBridge
            ->shouldReceive('refundRequest')
            ->andReturn((object)[
                'refundResult' => (object)[
                    'response' => ''
                ]
            ])
        ;
    }
}