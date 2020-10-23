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

use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use BitBag\SyliusAdyenPlugin\Bridge\ModificationRequestAdyenBridgeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ModificationRequestAdyenBridge implements ModificationRequestAdyenBridgeInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function refundRequest(
        AdyenBridgeInterface $adyenBridge,
        array $modificationAmount,
        string $originalReference,
        string $reference
    ): \stdClass {
        return $this->container->get('bitbag_sylius_adyen_plugin.bridge.modification_request_adyen')->refundRequest(
            $adyenBridge,
            $modificationAmount,
            $originalReference,
            $reference
        );
    }
}
