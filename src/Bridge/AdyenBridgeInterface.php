<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Bridge;

interface AdyenBridgeInterface
{
    const TEST_ENVIRONMENT = 'test';
    const LIVE_ENVIRONMENT = 'live';
    const AUTHORISATION = 'AUTHORISATION';
    const AUTHORISED = 'AUTHORISED';
    const REFUSED = 'REFUSED';
    const PENDING = 'PENDING';
    const CAPTURE = 'CAPTURE';
    const CANCELLED = 'CANCELLED';
    const CANCELLATION = 'CANCELLATION';
    const CANCEL_OR_REFUND = 'CANCEL_OR_REFUND';
    const ERROR = 'ERROR';
    const NOTIFICATION_OF_CHARGEBACK = 'NOTIFICATION_OF_CHARGEBACK';
    const CHARGEBACK = 'CHARGEBACK';
    const CHARGEBACK_REVERSED = 'CHARGEBACK_REVERSED';
    const REFUND_FAILED = 'REFUND_FAILED';
    const CAPTURE_FAILED = 'CAPTURE_FAILED';
    const EXPIRE = 'EXPIRE';
    const REFUND = 'REFUND';
    const REFUNDED_REVERSED = 'REFUNDED_REVERSED';

    /**
     * @return string
     */
    public function getApiEndpoint(): string;

    /**
     * @param array $params
     * @param bool $isNotify
     * @param string $hmacKey
     *
     * @return string
     */
    public function merchantSig(array $params, bool $isNotify = false, string $hmacKey = 'hmacKey'): string;

    /**
     * @param array $params
     *
     * @return bool
     */
    public function verifySign(array $params): bool;

    /**
     * @param array $params
     *
     * @return bool
     */
    public function verifyNotification(array $params): bool;

    /**
     * @param array $params
     *
     * @return string
     */
    public function createSignatureForNotification(array $params): string;

    /**
     * @param array $params
     * @param array $details
     *
     * @return bool
     */
    public function verifyRequest(array $params, array $details): bool;

    /**
     * @param array $params
     *
     * @return array
     */
    public function prepareFields(array $params): array;

    public function createSoapClient(): void;

    /**
     * @return string
     */
    public function getWsdl(): string;

    /**
     * @return string
     */
    public function getMerchantAccount(): string;

    /**
     * @param array $modificationAmount
     * @param string $originalReference
     * @param string $reference
     *
     * @return \stdClass
     */
    public function refundAction(
        array $modificationAmount,
        string $originalReference,
        string $reference
    ): \stdClass;
}
