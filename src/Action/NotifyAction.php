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
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;

final class NotifyAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var AdyenBridgeInterface
     */
    protected $api;

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
     * @param Notify $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if (!isset($httpRequest->request['merchantReference']) || empty($httpRequest->request['merchantReference'])) {
            $details['response_status'] = 401;
            return;
        }

        if (!isset($details['merchantReference']) || ($details['merchantReference'] !== $httpRequest->request['merchantReference'])) {
            $details['response_status'] = 402;
            return;
        }

        if (false === $this->api->verifyNotification($httpRequest->request)) {
            $details['response_status'] = 403;
            return;
        }

        if (isset($httpRequest->request['eventCode'])) {
            $httpRequest->request['authResult'] = $httpRequest->request['eventCode'];

            if (AdyenBridgeInterface::AUTHORISATION === $httpRequest->request['eventCode']) {
                if (true === filter_var($httpRequest->request['success'], FILTER_VALIDATE_BOOLEAN)) {
                    $httpRequest->request['authResult'] = AdyenBridgeInterface::AUTHORISED;
                } elseif (!empty($httpRequest->request['reason'])) {
                    $httpRequest->request['authResult'] = AdyenBridgeInterface::REFUSED;
                }
            }

            if (AdyenBridgeInterface::REFUND === $httpRequest->request['eventCode']) {
                if (true === filter_var($httpRequest->request['success'], FILTER_VALIDATE_BOOLEAN)) {
                    $httpRequest->request['authResult'] = AdyenBridgeInterface::REFUND;
                } elseif (!empty($httpRequest->request['reason'])) {
                    $httpRequest->request['authResult'] = AdyenBridgeInterface::REFUSED;
                }
            }
        }

        $details['authResult'] = $httpRequest->request['authResult'];

        $details['pspReference'] = $httpRequest->request['pspReference'];

        $details['response_status'] = 200;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request): bool
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
