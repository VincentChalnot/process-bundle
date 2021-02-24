<?php declare(strict_types=1);
/*
 * This file is part of the CleverAge/ProcessBundle package.
 *
 * Copyright (C) 2017-2021 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\ProcessBundle\Task\Validation;

use CleverAge\ProcessBundle\Model\AbstractConfigurableTask;
use CleverAge\ProcessBundle\Model\ProcessState;
use CleverAge\ProcessBundle\Validator\ConstraintLoader;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Validate the input and pass it to the output
 *
 * @author Valentin Clavreul <vclavreul@clever-age.com>
 * @author Vincent Chalnot <vchalnot@clever-age.com>
 */
class ValidatorTask extends AbstractConfigurableTask
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var ValidatorInterface */
    protected $validator;

    /**
     * @param LoggerInterface    $logger
     * @param ValidatorInterface $validator
     */
    public function __construct(LoggerInterface $logger, ValidatorInterface $validator)
    {
        $this->logger = $logger;
        $this->validator = $validator;
    }

    /**
     * @param ProcessState $state
     *
     * @throws ExceptionInterface
     * @throws \UnexpectedValueException
     */
    public function execute(ProcessState $state)
    {
        $options = $this->getOptions($state);
        $violations = $this->validator->validate(
            $state->getInput(),
            $options['constraints'],
            $options['groups']
        );

        if (0 < $violations->count()) {
            /** @var  $violation ConstraintViolationInterface */
            foreach ($violations as $violation) {
                $invalidValue = $violation->getInvalidValue();

                $logContext = [
                    'property' => $violation->getPropertyPath(),
                    'violation_code' => $violation->getCode(),
                    'invalid_value' => $invalidValue,
                ];
                if ($options['log_errors']) {
                    $this->logger->log($options['log_errors'], $violation->getMessage(), $logContext);
                }
            }

            if ($options['error_output_violations']) {
                $state->setErrorOutput($violations);
                $state->setSkipped(true);

                return;
            }

            throw new \UnexpectedValueException("{$violations->count()} constraint violations detected on validation");
        }

        $state->setOutput($state->getInput());
    }

    /**
     * {@inheritDoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('log_errors', LogLevel::CRITICAL);
        $resolver->setAllowedValues(
            'log_errors',
            [
                LogLevel::ALERT,
                LogLevel::CRITICAL,
                LogLevel::DEBUG,
                LogLevel::EMERGENCY,
                LogLevel::ERROR,
                LogLevel::INFO,
                LogLevel::NOTICE,
                LogLevel::WARNING,
                true,
                false,
            ]
        );
        $resolver->setNormalizer(
            'log_errors',
            static function (Options $options, $value) {
                if (true === $value) {
                    return LogLevel::CRITICAL;
                }

                return $value;
            }
        );

        $resolver->setDefault('groups', null);
        $resolver->setAllowedTypes('groups', ['null', 'array']);

        $resolver->setDefault('constraints', null);
        $resolver->setAllowedTypes('constraints', ['null', 'array']);
        $resolver->setNormalizer(
            'constraints',
            static function (Options $options, $constraints) {
                if (null === $constraints) {
                    return null;
                }

                return (new ConstraintLoader())->buildConstraints($constraints);
            }
        );

        $resolver->setDefault('error_output_violations', false);
        $resolver->setAllowedTypes('error_output_violations', ['bool']);
    }
}
