<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusAdyenPlugin\Controller;

use BitBag\SyliusAdyenPlugin\Controller\NotifyController;
use Payum\Core\GatewayInterface;
use Payum\Core\Payum;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Storage\StorageInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class NotifyControllerSpec extends ObjectBehavior
{
    function let(Payum $payum, PaymentRepositoryInterface $paymentRepository): void
    {
        $this->beConstructedWith($payum, $paymentRepository);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(NotifyController::class);
    }

    function it_executes(
        PaymentRepositoryInterface $paymentRepository,
        PaymentInterface $payment,
        Payum $payum,
        StorageInterface $storage,
        TokenInterface $token,
        GatewayInterface $gateway
    ): void
    {
        $request = new Request([], ['notificationItems' => [[
            'NotificationRequestItem' => [
                'merchantReference' => '0000001-11'
            ],
        ]]]);

        $payment->getDetails()->willReturn([
            'extraData' => '{"notifyToken":"test"}'
        ]);

        $paymentRepository->findOneBy(['id' => 11])->willReturn($payment);

        $token->getGatewayName()->willReturn('Adyen');

        $storage->find('test')->willReturn($token);

        $payum->getTokenStorage()->willReturn($storage);
        $payum->getGateway('Adyen')->willReturn($gateway);

        $this->doAction($request);
    }
}
