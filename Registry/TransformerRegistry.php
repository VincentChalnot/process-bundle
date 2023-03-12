<?php

declare(strict_types=1);

/*
 * This file is part of the CleverAge/ProcessBundle package.
 *
 * Copyright (c) 2017-2023 Clever-Age
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CleverAge\ProcessBundle\Registry;

use CleverAge\ProcessBundle\Exception\MissingTransformerException;
use CleverAge\ProcessBundle\Transformer\TransformerInterface;
use UnexpectedValueException;

/**
 * Holds all tagged transformer services
 */
class TransformerRegistry
{
    /**
     * @var TransformerInterface[]
     */
    protected $transformers = [];

    public function addTransformer(TransformerInterface $transformer)
    {
        if (array_key_exists($transformer->getCode(), $this->transformers)) {
            throw new UnexpectedValueException("Transformer {$transformer->getCode()} is already defined");
        }
        $this->transformers[$transformer->getCode()] = $transformer;
    }

    /**
     * @return TransformerInterface[]
     */
    public function getTransformers()
    {
        return $this->transformers;
    }

    /**
     * @param string $code
     *
     * @return TransformerInterface
     */
    public function getTransformer($code)
    {
        if (! $this->hasTransformer($code)) {
            throw MissingTransformerException::create($code);
        }

        return $this->transformers[$code];
    }

    /**
     * @param string $code
     */
    public function hasTransformer($code): bool
    {
        return array_key_exists($code, $this->transformers);
    }
}
