<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on mikolaj.krol@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusAdyenPlugin\Controller;

use Payum\Core\Exception\LogicException;
use Payum\Core\Payum;
use Payum\Core\Registry\RegistryInterface;
use Payum\Core\Request\Notify;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class NotifyController
{
    /**
     * @var Payum
     */
    private $payum;

    /**
     * @var PaymentRepositoryInterface
     */
    private $paymentRepository;

    /**
     * @param RegistryInterface $payum
     * @param PaymentRepositoryInterface $paymentRepository
     */
    public function __construct(RegistryInterface $payum, PaymentRepositoryInterface $paymentRepository)
    {
        $this->payum = $payum;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Payum\Core\Reply\ReplyInterface
     */
    public function doAction(Request $request): Response
    {
        if (null === $merchantReference = $request->request->get('merchantReference', null)) {
            throw new LogicException("A parameter merchantReference not be found.");
        }

        $paymentId = 2 === count(explode('-', $merchantReference)) ?
            explode('-', $merchantReference)[1] : null
        ;

        /** @var PaymentInterface $payment */
        $payment = $this->paymentRepository->findOneBy(['id' => $paymentId]);

        if (null === $payment) {
            throw new NotFoundHttpException("Payment not found ");
        }

        $hash = null !== $payment ? json_decode($payment->getDetails()['extraData'], true)['notifyToken'] : '';

        if (false === $token = $this->payum->getTokenStorage()->find($hash)) {
            throw new NotFoundHttpException(sprintf("A token with hash `%s` could not be found.", $hash));
        }

        $gateway = $this->payum->getGateway($token->getGatewayName());

        $gateway->execute(new Notify($token));

        return new Response("[accepted]");
    }
}