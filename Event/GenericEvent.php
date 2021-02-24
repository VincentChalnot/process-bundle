<?php declare(strict_types=1);
/*
 * This file is part of the CleverAge/ProcessBundle package.
 *
 * Copyright (C) 2017-2021 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\ProcessBundle\Event;

/**
 * This class aims to provide compatibility between different Symfony versions
 *
 * @deprecated once sf3.4 support is dropped, only keep the "Contracts"
 */
if (class_exists('\Symfony\Component\EventDispatcher\Event')) {
    class GenericEvent extends \Symfony\Component\EventDispatcher\Event
    {
    }
} else {
    class GenericEvent extends \Symfony\Contracts\EventDispatcher\Event
    {
    }
}

