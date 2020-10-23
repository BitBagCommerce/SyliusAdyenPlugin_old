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
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Symfony\Component\HttpFoundation\Response;

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

        $data = $httpRequest->request['notificationItems'][0]['NotificationRequestItem'];

        if (!isset($data['merchantReference']) || empty($data['merchantReference'])) {
            $details['response_status'] = 401;

            throw new HttpResponse(null, Response::HTTP_UNAUTHORIZED);
        }

        if (!isset($details['merchantReference']) || ($details['merchantReference'] !== $data['merchantReference'])) {
            $details['response_status'] = 402;

            throw new HttpResponse(null, Response::HTTP_PAYMENT_REQUIRED);
        }

        if (false === $this->api->verifyNotification($data)) {
            $details['response_status'] = 403;

            throw new HttpResponse(null, Response::HTTP_FORBIDDEN);
        }

        if (isset($data['eventCode'])) {
            $data['authResult'] = $data['eventCode'];

            if (AdyenBridgeInterface::AUTHORISATION === $data['eventCode']) {
                if (true === filter_var($data['success'], FILTER_VALIDATE_BOOLEAN)) {
                    $data['authResult'] = AdyenBridgeInterface::AUTHORISED;
                } else {
                    $data['authResult'] = AdyenBridgeInterface::REFUSED;
                }
            }

            if (AdyenBridgeInterface::REFUND === $data['eventCode']) {
                if (true === filter_var($data['success'], FILTER_VALIDATE_BOOLEAN)) {
                    $data['authResult'] = AdyenBridgeInterface::REFUND;
                } else {
                    $data['authResult'] = AdyenBridgeInterface::REFUSED;
                }
            }
        }

        $details['authResult'] = $data['authResult'];

        $details['pspReference'] = $data['pspReference'];

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
