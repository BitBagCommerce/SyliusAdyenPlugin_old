<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Page\External;

use Behat\Mink\Session;
use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridge;
use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use Payum\Core\Security\TokenInterface;
use Sylius\Behat\Page\Page;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\BrowserKit\Client;

final class AdyenCheckoutPage extends Page implements AdyenCheckoutPageInterface
{
    /**
     * @var RepositoryInterface
     */
    private $securityTokenRepository;

    /**
     * @var AdyenBridgeInterface
     */
    private $adyenBridge;

    /**
     * @var EntityRepository
     */
    private $paymentRepository;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $resultData = [
        'authResult' => 'AUTHORISED',
        'merchantReference' => null,
        'paymentMethod' => 'test',
        'pspReference' => '8815114300414883',
        'shopperLocale' => 'en_US',
        'skinCode' => 'test',
    ];

    /**
     * @var array
     */
    private $notifyData = [
        'pspReference'=>  '8815113926211293',
        'originalReference'=> '',
        'merchantAccountCode'=>  'TestPL',
        'merchantReference'=>  '000000026-26',
        'value'=> '9630',
        'currency'=>  'USD',
        'eventCode'=> 'AUTHORISATION',
        'success'=> 'true',
        'reason'=> 'null',
        'operations'=>  '',
        'paymentMethod'=>  'dotpay',
        'live' =>  'false',
        'eventDate' => '2017-11-22T23:17:12.06Z',
    ];


    public function __construct(
        Session $session,
        array $parameters,
        RepositoryInterface $securityTokenRepository,
        EntityRepository $paymentRepository,
        Client $client
    )
    {
        parent::__construct($session, $parameters);

        $this->paymentRepository = $paymentRepository;
        $this->securityTokenRepository = $securityTokenRepository;
        $this->client = $client;

        $this->adyenBridge = new AdyenBridge([
            'skinCode' => 'test',
            'merchantAccount' => 'test',
            'hmacKey' => 111,
            'notification_hmac' => 111,
            'environment' => 'test',
            'notification_method' => 'basic',
            'ws_user' => 'test',
            'ws_user_password' => 'test',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function pay(): void
    {
        $token = $this->findToken();

        $queryData = $this->resultData;
        $queryData['merchantReference'] = $this->createMerchantReferenceWithToken($token);
        $queryData['merchantSig'] = $this->adyenBridge->merchantSig($queryData);

        $this->getDriver()->visit($token->getTargetUrl() . '?' . http_build_query($queryData));
    }

    /**
     * {@inheritDoc}
     */
    public function cancel(): void
    {
        $token = $this->findToken();

        $queryData = $this->resultData;
        $queryData['authResult'] = 'CANCELLED';
        $queryData['merchantReference'] = $this->createMerchantReferenceWithToken($token);
        $queryData['merchantSig'] = $this->adyenBridge->merchantSig($queryData);

        $this->getDriver()->visit($token->getTargetUrl() . '?' . http_build_query($queryData));
    }

    /**
     * {@inheritDoc}
     */
    public function successNotify(): void
    {
        $token = $this->findToken('notify');

        $data = $this->notifyData;
        $data['merchantReference'] = $this->createMerchantReferenceWithToken($token);
        $data['additionalData_hmacSignature'] = $this->adyenBridge->createSignatureForNotification($data);

        $this->client->request('POST', '/payment/adyen/notify', $data);
    }

    /**
     * {@inheritDoc}
     */
    public function successRefundedPaymentNotify(): void
    {
        $token = $this->findToken('refund');

        $data = $this->notifyData;
        $data['merchantReference'] = $this->createMerchantReferenceWithToken($token);
        $data['eventCode'] = 'REFUND';
        $data['additionalData_hmacSignature'] = $this->adyenBridge->createSignatureForNotification($data);

        $this->client->request('POST', '/payment/adyen/notify', $data);
    }

    /**
     * {@inheritDoc}
     */
    public function failedRefundedPaymentNotify(): void
    {
        $token = $this->findToken('refund');

        $data = $this->notifyData;
        $data['merchantReference'] = $this->createMerchantReferenceWithToken($token);
        $data['eventCode'] = 'REFUND';
        $data['success'] = 'false';
        $data['reason'] = 'Insufficient balance on payment';
        $data['additionalData_hmacSignature'] = $this->adyenBridge->createSignatureForNotification($data);

        $this->client->request('POST', '/payment/adyen/notify', $data);
    }

    /**
     * {@inheritDoc}
     */
    protected function getUrl(array $urlParameters = []): string
    {
        return 'https://test.adyen.com/hpp/pay.shtml';
    }

    /**
     * @param string $type
     *
     * @return TokenInterface
     */
    private function findToken(string $type = 'capture'): TokenInterface
    {
        $tokens = $this->securityTokenRepository->findAll();

        /** @var TokenInterface $token */
        foreach ($tokens as $token) {
            if (strpos($token->getTargetUrl(), $type)) {
                return $token;
            }
        }

        throw new \RuntimeException('Cannot find capture token, check if you are after proper checkout steps');
    }

    /**
     * @param TokenInterface $token
     *
     * @return string
     */
    private function createMerchantReferenceWithToken(TokenInterface $token): string
    {
        /** @var PaymentInterface $payment */
        $payment = $this->paymentRepository->find($token->getDetails()->getId());

        return (string)($payment->getOrder()->getNumber() . '-' . $payment->getId());
    }
}