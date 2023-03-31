<?php

declare(strict_types=1);

namespace CleverAge\ProcessBundle\Task;

use CleverAge\ProcessBundle\Model\AbstractConfigurableTask;
use CleverAge\ProcessBundle\Model\BlockingTaskInterface;
use CleverAge\ProcessBundle\Model\ProcessState;
use Exception;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use function count;

/**
 * Attempt to aggregate inputs in an associative array with a key formed by configurable fields of the input.
 * This task could be used to remove duplicates from the aggregate.
 */
class GroupByAggregateIterableTask extends AbstractConfigurableTask implements BlockingTaskInterface
{
    /**
     * @var string
     */
    final public const GROUP_BY_OPTION = 'group_by_accessors';

    protected array $result = [];

    public function __construct(
        protected PropertyAccessorInterface $accessor
    ) {
    }

    public function execute(ProcessState $state): void
    {
        $options = $this->getOptions($state);
        $input = $state->getInput();
        $groupByAccessors = $options[self::GROUP_BY_OPTION];

        $keyParts = [];
        foreach ($groupByAccessors as $groupByAccessor) {
            try {
                $keyParts[] = $this->accessor->getValue($input, $groupByAccessor);
            } catch (Exception $e) {
                $state->addErrorContextValue('property', $groupByAccessor);
                $state->setException($e);

                return;
            }
        }

        $key = implode('-', $keyParts);
        $this->result[$key] = $input;
    }

    public function proceed(ProcessState $state): void
    {
        if (count($this->result) === 0) {
            $state->setSkipped(true);
        } else {
            $state->setOutput($this->result);
        }
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([self::GROUP_BY_OPTION]);
        $resolver->setAllowedTypes(self::GROUP_BY_OPTION, ['array']);
    }
}
