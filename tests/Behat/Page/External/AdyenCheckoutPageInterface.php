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

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Sylius\Behat\Page\PageInterface;

interface AdyenCheckoutPageInterface extends PageInterface
{
    /**
     * @throws UnsupportedDriverActionException
     * @throws DriverException
     */
    public function pay(): void;

    /**
     * @throws UnsupportedDriverActionException
     * @throws DriverException
     */
    public function cancel(): void;

    /**
     * @throws UnsupportedDriverActionException
     * @throws DriverException
     */
    public function successNotify(): void;

    /**
     * @throws UnsupportedDriverActionException
     * @throws DriverException
     */
    public function failedAuthorisationWithoutReasonNotify(): void;

    /**
     * @throws UnsupportedDriverActionException
     * @throws DriverException
     */
    public function successRefundedPaymentNotify(): void;

    /**
     * @throws UnsupportedDriverActionException
     * @throws DriverException
     */
    public function failedRefundedPaymentNotify(): void;
}