<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Action;

use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use BitBag\SyliusAdyenPlugin\Bridge\ModificationRequestAdyenBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Refund;
use Sylius\Component\Resource\Exception\UpdateHandlingException;

final class RefundAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var AdyenBridgeInterface
     */
    protected $api;

    /**
     * @var ModificationRequestAdyenBridgeInterface
     */
    private $modificationRequestAdyenBridge;

    /**
     * @param ModificationRequestAdyenBridgeInterface $modificationRequestAdyenBridge
     */
    public function __construct(ModificationRequestAdyenBridgeInterface $modificationRequestAdyenBridge)
    {
        $this->modificationRequestAdyenBridge = $modificationRequestAdyenBridge;
    }

    /**
     * {@inheritDoc}
     */
    public function setApi($api): void
    {
        if (false === $api instanceof AdyenBridgeInterface) {
            throw new UnsupportedApiException(sprintf('Not supported. Expected %s instance to be set as api.', AdyenBridgeInterface::class));
        }

        $this->api = $api;
    }

    /**
     * {@inheritDoc}
     *
     * @param Refund $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        try {
            $this->modificationRequestAdyenBridge->refundRequest(
                $this->api,
                [
                    "currency" => $details['currencyCode'],
                    "value" => $details['paymentAmount'],
                ],
                $details['pspReference'],
                $details['merchantReference']
            )->refundResult;

            $details['authResult'] = AdyenBridgeInterface::REFUND;

            $details['response_status'] = 200;
        } catch(\SoapFault $ex){
            throw new UpdateHandlingException($ex->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request): bool
    {
        return
            $request instanceof Refund &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
