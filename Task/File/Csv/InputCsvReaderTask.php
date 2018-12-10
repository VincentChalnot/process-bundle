<?php
/*
 * This file is part of the CleverAge/ProcessBundle package.
 *
 * Copyright (C) 2017-2018 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\ProcessBundle\Task\File\Csv;

use CleverAge\ProcessBundle\Model\ProcessState;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Reads the filepath from the input
 */
class InputCsvReaderTask extends CsvReaderTask
{
    /**
     * @param ProcessState $state
     *
     * @TODO refactor to get file path outside of options
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\ExceptionInterface
     *
     * @return array
     */
    protected function getOptions(ProcessState $state)
    {
        $options = parent::getOptions($state);
        if (null !== $state->getInput()) {
            $options['file_path'] = $this->getFilePath($options, $state->getInput());
        } else {
            throw new \InvalidArgumentException("Input must be defined");
        }

        if (!isset($options['file_path'])) {
            $state->addErrorContextValue('input', $state->getInput());
            throw new \UnexpectedValueException("Could not determine file path from input");
        }

        return $options;
    }

    /**
     * @param OptionsResolver $resolver
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->remove('file_path');

        // If there is no base_path, then the given path should be absolute
        $resolver->setDefault('base_path', '');
        $resolver->setAllowedTypes('base_path', 'string');
    }

    /**
     * If there is no base_path, then the given path from input should be absolute
     *
     * @param array  $options
     * @param string $input
     *
     * @return string
     */
    protected function getFilePath(array $options, string $input)
    {
        $basePath = $options['base_path'];
        if (\strlen($basePath) > 0) {
            $basePath = rtrim($options['base_path'], '/') . '/';
        }

        return $basePath . $input;
    }
}
