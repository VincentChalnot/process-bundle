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

/**
 * Event object for process start/stop/fail
 *
 * @author Valentin Clavreul <vclavreul@clever-age.com>
 */
class ProcessEvent extends GenericEvent
{

    const EVENT_PROCESS_STARTED = 'cleverage_process.start';
    const EVENT_PROCESS_ENDED = 'cleverage_process.end';
    const EVENT_PROCESS_FAILED = 'cleverage_process.fail';

    /** @var string */
    protected $processCode;

    /** @var array */
    protected $processContext;

    /** @var \Throwable|null */
    protected $processError;

    /**
     * ProcessEvent constructor.
     *
     * @param string          $processCode
     * @param mixed           $processInput
     * @param array           $processContext
     * @param mixed           $processOutput
     * @param \Throwable|null $processError
     */
    public function __construct(
        string $processCode,
        protected $processInput = null,
        array $processContext = [],
        protected $processOutput = null,
        \Throwable $processError = null
    ) {
        $this->processCode = $processCode;
        $this->processContext = $processContext;
        $this->processError = $processError;
    }

    /**
     * @return string
     */
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

    /**
     * @return array
     */
    public function getProcessContext(): array
    {
        return $this->processContext;
    }

    /**
     * @return \Throwable|null
     */
    public function getProcessError(): ?\Throwable
    {
        return $this->processError;
    }

}
