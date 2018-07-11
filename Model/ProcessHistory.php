<?php
/*
 * This file is part of the CleverAge/ProcessBundle package.
 *
 * Copyright (C) 2017-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\ProcessBundle\Model;

use CleverAge\ProcessBundle\Configuration\ProcessConfiguration;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Logs information about a process
 *
 * @author Valentin Clavreul <vclavreul@clever-age.com>
 * @author Vincent Chalnot <vchalnot@clever-age.com>
 */
class ProcessHistory
{
    public const STATE_STARTED = 'started';
    public const STATE_SUCCESS = 'success';
    public const STATE_FAILED = 'failed';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $processCode;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var \DateTime
     */
    protected $endDate;

    /**
     * @var string
     */
    protected $state = self::STATE_STARTED;

    /**
     * @var TaskHistory[]
     */
    protected $taskHistories;

    /**
     * @param ProcessConfiguration $processConfiguration
     */
    public function __construct(ProcessConfiguration $processConfiguration)
    {
        $this->id = microtime(true);
        $this->processCode = $processConfiguration->getCode();
        $this->startDate = new \DateTime();
        $this->taskHistories = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getProcessCode(): string
    {
        return $this->processCode;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return TaskHistory[]
     */
    public function getTaskHistories()
    {
        return $this->taskHistories;
    }

    /**
     * @param TaskHistory $taskHistory
     */
    public function addTaskHistory(TaskHistory $taskHistory)
    {
        $this->taskHistories[] = $taskHistory;
    }

    /**
     * Set the process as failed
     */
    public function setFailed()
    {
        $this->endDate = new \DateTime();
        $this->state = self::STATE_FAILED;
    }

    /**
     * Set the process as succeded
     */
    public function setSuccess()
    {
        $this->endDate = new \DateTime();
        $this->state = self::STATE_SUCCESS;
    }

    /**
     * Is true when the process is running
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->state === self::STATE_STARTED;
    }

    /**
     * Get process duration in seconds
     *
     * @return int|null
     */
    public function getDuration()
    {
        if ($this->getEndDate()) {
            return $this->getEndDate()->getTimestamp() - $this->getStartDate()->getTimestamp();
        }

        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $reference = $this->getProcessCode().'['.$this->getState().']';
        $time = $this->getStartDate()->format(\DateTime::ATOM);

        return $reference.': '.$time;
    }
}
