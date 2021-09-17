<?php declare(strict_types=1);
/*
 * This file is part of the CleverAge/ProcessBundle package.
 *
 * Copyright (C) 2017-2021 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\ProcessBundle\Task;

use CleverAge\ProcessBundle\Model\AbstractConfigurableTask;
use CleverAge\ProcessBundle\Model\ProcessState;
use CleverAge\ProcessBundle\Transformer\ConditionTrait;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Skip inputs under given matching conditions
 *
 * Matching use the following rules
 * * equality is softly checked
 * * missing key is the same as `null`
 *
 * ##### Task reference
 *
 * * **Service**: `CleverAge\ProcessBundle\Task\FilterTask`
 * * **Input**: `array` or `object` that can be used by the [PropertyAccess component](https://symfony.com/components/PropertyAccess)
 * * **Output**: the same as input, unmodified
 *
 * ##### Options
 *
 * The list of options is the same than the {@see \CleverAge\ProcessBundle\Transformer\ConditionTrait}.
 *
 * @example "Resources/tests/task/filter_task.yml" Some basic examples
 */
class FilterTask extends AbstractConfigurableTask
{

    use ConditionTrait;

    /**
     * {@inheritDoc}
     *
     * @internal
     */
    public function initialize(ProcessState $state)
    {
        parent::initialize($state);
        $this->accessor = new PropertyAccessor();
    }

    /**
     * {@inheritDoc}
     *
     * @internal
     */
    public function execute(ProcessState $state)
    {
        $input = $state->getInput();
        if (!$this->checkCondition($input, $this->getOptions($state))) {
            $state->setErrorOutput($input);
            $state->setSkipped(true);

            return;
        }

        $state->setOutput($input);
    }

    /**
     * {@inheritDoc}
     *
     * @internal
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $this->configureConditionOptions($resolver);
    }
}
