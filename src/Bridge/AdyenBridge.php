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

use Adyen\Util\HmacSignature;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\HttpClientInterface;

final class AdyenBridge implements AdyenBridgeInterface
{
    /**
     * @var array
     */
    protected $requiredFields = [
        'merchantReference' => null,
        'paymentAmount' => null,
        'currencyCode' => null,
        'shipBeforeDate' => null,
        'skinCode' => null,
        'merchantAccount' => null,
        'sessionValidity' => null,
        'shopperEmail' => null,
    ];

    /**
     * @var array
     */
    protected $optionalFields = [
        'merchantReturnData' => null,
        'shopperReference' => null,
        'allowedMethods' => null,
        'blockedMethods' => null,
        'offset' => null,
        'shopperStatement' => null,
        'recurringContract' => null,
        'billingAddressType' => null,
        'deliveryAddressType' => null,
        'resURL' => null,
    ];
    /**
     * @var array
     */
    protected $othersFields = [
        'brandCode' => null,
        'countryCode' => null,
        'shopperLocale' => null,
        'orderData' => null,
        'offerEmail' => null,
        'issuerId' => null,
    ];

    /**
     * @var array
     */
    protected $notificationFields = [
        'pspReference' => null,
        'originalReference' => null,
        'merchantAccountCode' => null,
        'merchantReference' => null,
        'value' => null,
        'currency' => null,
        'eventCode' => null,
        'success' => null,
    ];

    /**
     * @var ArrayObject
     */
    protected $options = [
        'skinCode' => null,
        'merchantAccount' => null,
        'hmacKey' => null,
        'environment' => null,
        'notification_method' => null,
        'notification_hmac' => null,
        'default_payment_fields' => [],
        'ws_user' => null,
        'ws_user_password' => null,
    ];

    /**
     * @var \SoapClient|object
     */
    protected $soapClient;

    /**
     * @param array               $options
     * @param HttpClientInterface $client
     *
     * @throws \Payum\Core\Exception\InvalidArgumentException if an option is invalid
     * @throws \Payum\Core\Exception\LogicException if a sandbox is not boolean
     */
    public function __construct(array $options, HttpClientInterface $client = null)
    {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->options);
        $options->validateNotEmpty([
            'skinCode',
            'merchantAccount',
            'hmacKey',
            'notification_hmac',
            'ws_user',
            'ws_user_password',
        ]);

        $this->options = $options;
    }

    /**
     * @return string
     */
    public function getApiEndpoint(): string
    {
        return sprintf('https://%s.adyen.com/hpp/select.shtml', $this->options['environment']);
    }

    /**
     * @param array $params
     * @param bool $isNotify
     * @param string $hmacKey
     *
     * @return string
     */
    public function merchantSig(array $params, bool $isNotify = false, string $hmacKey = 'hmacKey'): string
    {
        if (false === $isNotify) {
            ksort($params, SORT_STRING);
        }

        $escapedPairs = [];

        foreach ($params as $key => $value) {
            $escapedPairs[$key] = str_replace(':','\\:', str_replace('\\', '\\\\', $value));
        }

        if (false === $isNotify) {
            $signingString = implode(":", array_merge(array_keys($escapedPairs), array_values($escapedPairs)));
        } else {
            $signingString = implode(":", array_merge(array_values($escapedPairs)));
        }

        $binaryHmacKey = pack("H*" , $this->options[$hmacKey]);

        $binaryHmac = hash_hmac('sha256', $signingString, $binaryHmacKey, true);

        $signature = base64_encode($binaryHmac);

        return $signature;
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function verifySign(array $params): bool
    {
        if (empty($params['merchantSig'])) {
            return false;
        }

        $merchantSig = $params['merchantSig'];

        unset($params['merchantSig']);

        return $merchantSig === $this->merchantSig($params);
    }

    /**
     * @param array $params
     * @return bool
     * @throws \Adyen\AdyenException
     */
    public function verifyNotification(array $params): bool
    {
        return (new HmacSignature())->isValidNotificationHMAC($this->options['notification_hmac'], $params);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function createSignatureForNotification(array $params): string
    {
        $data = [];

        foreach (array_keys($this->notificationFields) as $fieldKey) {
            $data[$fieldKey] = $params[$fieldKey];
        }

        return $this->merchantSig($data, true,'notification_hmac');
    }

    /**
     * @param array $params
     * @param array $details
     *
     * @return bool
     */
    public function verifyRequest(array $params, array $details): bool
    {
        if (!isset($params['merchantReference']) || empty($params['merchantReference']) ||
            !isset($params['authResult']) || empty($params['authResult'])) {
            return false;
        }

        if (!isset($details['merchantReference']) || ($details['merchantReference'] !== $params['merchantReference'])) {
            return false;
        }

        return $this->verifySign($params);
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function prepareFields(array $params): array
    {
        if (false !== empty($this->options['default_payment_fields'])) {
            $params = array_merge($params, (array) $this->options['default_payment_fields']);
        }

        $params['shipBeforeDate'] = date('Y-m-d', strtotime('+1 hour'));
        $params['sessionValidity'] = date(DATE_ATOM, strtotime('+1 hour'));

        $params['skinCode'] = $this->options['skinCode'];
        $params['merchantAccount'] = $this->options['merchantAccount'];

        $supportedParams = array_merge($this->requiredFields, $this->optionalFields, $this->othersFields);

        $params = array_filter(array_replace(
            $supportedParams,
            array_intersect_key($params, $supportedParams)
        ));

        $params['merchantSig'] = $this->merchantSig($params);

        return $params;
    }

    public function createSoapClient(): void
    {
        $this->soapClient = new \SoapClient(
            $this->getWsdl(), [
                "login" => $this->options['ws_user'],
                "password" => $this->options['ws_user_password'],
                "style" => SOAP_DOCUMENT,
                "encoding" => SOAP_LITERAL,
                "cache_wsdl" => WSDL_CACHE_BOTH,
                "trace" => 1
            ]
        );
    }

    /**
     * @return string
     */
    public function getWsdl(): string
    {
        return sprintf('https://pal-%s.adyen.com/pal/Payment.wsdl', $this->options['environment']);
    }

    /**
     * @return string
     */
    public function getMerchantAccount(): string
    {
        return $this->options['merchantAccount'];
    }

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
    ): \stdClass
    {
        $this->createSoapClient();

        $data = [
            'modificationRequest' => [
                'merchantAccount' => $this->getMerchantAccount(),
                'modificationAmount' => $modificationAmount,
                'originalReference' => $originalReference,
                'reference' => $reference,
            ]
        ];

        return $this->soapClient->refund($data);
    }
}
