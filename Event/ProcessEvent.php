<?php declare(strict_types=1);
/*
 * This file is part of the CleverAge/ProcessBundle package.
 *
 * Copyright (c) 2017-2023 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\ProcessBundle\Event;

use Throwable;
/**
 * Event object for process start/stop/fail
 *
 * @author Valentin Clavreul <vclavreul@clever-age.com>
 */
class ProcessEvent extends GenericEvent
{

    final public const EVENT_PROCESS_STARTED = 'cleverage_process.start';
    final public const EVENT_PROCESS_ENDED = 'cleverage_process.end';
    final public const EVENT_PROCESS_FAILED = 'cleverage_process.fail';

    /**
     * ProcessEvent constructor.
     */
    public function __construct(protected string $processCode, protected mixed $processInput = null, protected array $processContext = [], protected mixed $processOutput = null, protected ?Throwable $processError = null)
    {
    }

    public function getProcessCode(): string
    {
        return $this->processCode;
    }

    /**
     * @return mixed
     */
    public function getProcessInput()
    {
        return $this->processInput;
    }

    /**
     * @return mixed
     */
    public function getProcessOutput()
    {
        return $this->processOutput;
    }

    public function getProcessContext(): array
    {
        return $this->processContext;
    }

    public function getProcessError(): ?Throwable
    {
        return $this->processError;
    }

}
