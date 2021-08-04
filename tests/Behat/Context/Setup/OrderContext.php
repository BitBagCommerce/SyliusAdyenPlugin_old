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
use BitBag\SyliusAdyenPlugin\AdyenGatewayFactory;
use Doctrine\ORM\EntityManager;
use Payum\Core\Payum;
use Payum\Core\Registry\RegistryInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Tests\BitBag\SyliusAdyenPlugin\Behat\Mocker\ModificationRequestAdyenBridgeMocker;

final class OrderContext implements Context
{
    /**
     * @var EntityManager
     */
    private $objectManager;

    /**
     * @var StateMachineFactoryInterface
     */
    private $stateMachineFactory;

    /**
     * @var RegistryInterface|Payum
     */
    private $payum;

    /**
     * @var ModificationRequestAdyenBridgeMocker
     */
    private $modificationRequestAdyenBridgeMocker;

    /**
     * @param ObjectManager $objectManager
     * @param StateMachineFactoryInterface $stateMachineFactory
     * @param RegistryInterface $payum
     * @param ModificationRequestAdyenBridgeMocker $modificationRequestAdyenBridgeMocker
     */
    public function __construct(
        ModificationRequestAdyenBridgeMocker $modificationRequestAdyenBridgeMocker,
        EntityManager $objectManager,
        StateMachineFactoryInterface $stateMachineFactory,
        RegistryInterface $payum
    ) {
        $this->modificationRequestAdyenBridgeMocker = $modificationRequestAdyenBridgeMocker;
        $this->objectManager = $objectManager;
        $this->stateMachineFactory = $stateMachineFactory;
        $this->payum = $payum;
    }

    /**
     * @Given /^(this order) with adyen payment is already paid$/
     * @Given the order :order with adyen payment is already paid
     *
     * @param OrderInterface $order
     * @throws \SM\SMException
     */
    public function thisOrdersWithAdyenPaymentIsAlreadyPaid(OrderInterface $order)
    {
        $this->applyAdyenPaymentTransitionOnOrder($order, PaymentTransitions::TRANSITION_COMPLETE);

        $this->modificationRequestAdyenBridgeMocker->refundRequest();

        $this->objectManager->flush();
    }

    /**
     * @param OrderInterface $order
     * @param $transition
     * @throws \SM\SMException
     */
    private function applyAdyenPaymentTransitionOnOrder(OrderInterface $order, $transition)
    {
        foreach ($order->getPayments() as $payment) {
            /** @var PaymentMethodInterface $paymentMethod */
            $paymentMethod = $payment->getMethod();

            if (AdyenGatewayFactory::FACTORY_NAME === $paymentMethod->getGatewayConfig()->getFactoryName()) {
                $refundToken = $this->payum->getTokenFactory()->createRefundToken('adyen', $payment);
                $notifyToken = $this->payum->getTokenFactory()->createNotifyToken('adyen', $payment);

                $extraData = [];
                $model = [];

                $extraData['refundToken'] = $refundToken->getHash();
                $extraData['notifyToken'] = $notifyToken->getHash();

                $model['currencyCode'] = $payment->getCurrencyCode();
                $model['paymentAmount'] = $payment->getAmount();
                $model['pspReference'] = 'test';
                $model['merchantReference'] = $order->getNumber() . '-' . $payment->getId();
                $model['extraData'] = json_encode($extraData);

                $payment->setDetails($model);
            }

            $this->stateMachineFactory->get($payment, PaymentTransitions::GRAPH)->apply($transition);
        }
    }
}
