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
use Payum\Core\Reply\HttpPostRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenInterface;

final class CaptureAction implements ActionInterface, ApiAwareInterface, GenericTokenFactoryAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @var AdyenBridgeInterface
     */
    protected $api;

    /**
     * @var GenericTokenFactoryInterface
     */
    protected $tokenFactory;

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
     * @param GenericTokenFactoryInterface $genericTokenFactory
     *
     * @return void
     */
    public function setGenericTokenFactory(GenericTokenFactoryInterface $genericTokenFactory = null): void
    {
        $this->tokenFactory = $genericTokenFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var TokenInterface $token */
        $token = $request->getToken();

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if (true === $this->api->verifyRequest($httpRequest->query, $model->toUnsafeArray())) {
            $model['authResult'] = $httpRequest->query['authResult'];

            if (true === isset($httpRequest->query['pspReference'])) {
                $model['pspReference'] = $httpRequest->query['pspReference'];
            }

            $model->toUnsafeArray();
            return;
        }

        $extraData = $model['extraData'] ? json_decode($model['extraData'], true) : [];

        if (false === isset($extraData['capture_token']) && $token) {
            $extraData['captureToken'] = $token->getHash();
            $extraData['refundToken'] = $this->tokenFactory->createRefundToken($token->getGatewayName(), $token->getDetails() ?? $model)->getHash();
            $model['resURL'] = $token->getTargetUrl();
        }

        if (false === isset($extraData['notify_token']) && $token && $this->tokenFactory) {
            $notifyToken = $this->tokenFactory->createNotifyToken(
                $token->getGatewayName(),
                $token->getDetails()
            );

            $extraData['notifyToken'] = $notifyToken->getHash();
            $model['notifyURL'] = $notifyToken->getTargetUrl();
        }

        $model['extraData'] = json_encode($extraData);

        throw new HttpPostRedirect(
            $this->api->getApiEndpoint(),
            $this->api->prepareFields($model->toUnsafeArray())
        );
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
