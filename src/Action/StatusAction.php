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
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Sylius\Component\Core\Model\PaymentInterface;

final class StatusAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
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
     * @param GetStatusInterface $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $details = $payment->getDetails();

        if (!isset($details['authResult'])) {

            $this->gateway->execute($httpRequest = new GetHttpRequest());

            if (true === $this->api->verifyRequest($httpRequest->query, $details)) {
                $details['authResult'] = $httpRequest->query['authResult'];
            } else {
                $request->markNew();
                return;
            }
        }

        if (isset($details['response_status'])) {
            if (200 !== $details['response_status']) {
                $request->markFailed();
                return;
            }
        }

        $payment->setDetails($details);

        $this->resolvePaymentStatus($details['authResult'], $request);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof PaymentInterface
        ;
    }

    /**
     * @param string $authResult
     * @param GetStatusInterface $request
     */
    private function resolvePaymentStatus(string $authResult, GetStatusInterface $request)
    {
        switch ($authResult) {
            case null:
                $request->markNew();
                break;
            case AdyenBridgeInterface::AUTHORISED:
            case AdyenBridgeInterface::AUTHORISATION:
                $request->markCaptured();
                break;
            case AdyenBridgeInterface::PENDING:
                $request->markPending();
                break;
            case AdyenBridgeInterface::CAPTURE:
                $request->markCaptured();
                break;
            case AdyenBridgeInterface::CANCELLED:
            case AdyenBridgeInterface::CANCELLATION:
            case AdyenBridgeInterface::CANCEL_OR_REFUND:
                $request->markCanceled();
                break;
            case AdyenBridgeInterface::REFUSED:
            case AdyenBridgeInterface::ERROR:
                $request->markFailed();
                break;
            case AdyenBridgeInterface::NOTIFICATION_OF_CHARGEBACK:
            case AdyenBridgeInterface::CHARGEBACK:
            case AdyenBridgeInterface::CHARGEBACK_REVERSED:
            case AdyenBridgeInterface::REFUND_FAILED:
            case AdyenBridgeInterface::CAPTURE_FAILED:
                $request->markSuspended();
                break;
            case AdyenBridgeInterface::EXPIRE:
                $request->markExpired();
                break;
            case AdyenBridgeInterface::REFUND:
            case AdyenBridgeInterface::REFUNDED_REVERSED:
                $request->markRefunded();
                break;
            default:
                $request->markUnknown();
                break;
        }
    }
}
