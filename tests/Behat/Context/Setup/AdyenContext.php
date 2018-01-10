<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusAdyenPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use BitBag\SyliusAdyenPlugin\Bridge\AdyenBridgeInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Doctrine\Common\Persistence\ObjectManager;

final class AdyenContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var PaymentMethodRepositoryInterface
     */
    private $paymentMethodRepository;

    /**
     * @var ExampleFactoryInterface
     */
    private $paymentMethodExampleFactory;

    /**
     * @var FactoryInterface
     */
    private $paymentMethodTranslationFactory;

    /**
     * @var ObjectManager
     */
    private $paymentMethodManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param PaymentMethodRepositoryInterface $paymentMethodRepository
     * @param ExampleFactoryInterface $paymentMethodExampleFactory
     * @param FactoryInterface $paymentMethodTranslationFactory
     * @param ObjectManager $paymentMethodManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        ExampleFactoryInterface $paymentMethodExampleFactory,
        FactoryInterface $paymentMethodTranslationFactory,
        ObjectManager $paymentMethodManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->paymentMethodRepository = $paymentMethodRepository;
        $this->paymentMethodExampleFactory = $paymentMethodExampleFactory;
        $this->paymentMethodTranslationFactory = $paymentMethodTranslationFactory;
        $this->paymentMethodManager = $paymentMethodManager;
    }

    /**
     * @Given the store has a payment method :paymentMethodName with a code :paymentMethodCode and Adyen Checkout gateway
     */
    public function theStoreHasAPaymentMethodWithACodeAndAdyenCheckoutGateway(
        string $paymentMethodName,
        string $paymentMethodCode
    ): void
    {
        $paymentMethod = $this->createPaymentMethod($paymentMethodName, $paymentMethodCode, 'Adyen');

        $paymentMethod->getGatewayConfig()->setConfig([
            'skinCode' => 'test',
            'merchantAccount' => 'test',
            'hmacKey' => 111,
            'hmacNotification' => 111,
            'environment' => AdyenBridgeInterface::TEST_ENVIRONMENT,
            'wsUser' => 'test',
            'wsUserPassword' => 'test',
        ]);

        $this->paymentMethodManager->flush();
    }

    /**
     * @param string $name
     * @param string $code
     * @param string $description
     * @param bool $addForCurrentChannel
     * @param int|null $position
     *
     * @return PaymentMethodInterface
     */
    private function createPaymentMethod(
        string $name,
        string $code,
        string $description = '',
        bool $addForCurrentChannel = true,
        int $position = null
    ): PaymentMethodInterface
    {

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodExampleFactory->create([
            'name' => ucfirst($name),
            'code' => $code,
            'description' => $description,
            'gatewayName' => 'adyen',
            'gatewayFactory' => 'adyen',
            'enabled' => true,
            'channels' => ($addForCurrentChannel && $this->sharedStorage->has('channel')) ? [$this->sharedStorage->get('channel')] : [],
        ]);

        if (null !== $position) {
            $paymentMethod->setPosition($position);
        }

        $this->sharedStorage->set('payment_method', $paymentMethod);
        $this->paymentMethodRepository->add($paymentMethod);

        return $paymentMethod;
    }
}