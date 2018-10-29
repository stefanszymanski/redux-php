<?php
declare(strict_types=1);

namespace Ctefan\Redux\Reducer;

use Ctefan\Redux\Action\ActionInterface;

/**
 * Each reducer of the CombinedReducer reduces another part of the state.
 *
 * Which part is defined by its key in the reducer array.
 */
class CombinedReducer
{
    /**
     * @var array<string,callable>
     */
    protected $reducers = [];

    /**
     * CombinedReducer constructor.
     *
     * @param array<string,callable> $reducers
     */
    public function __construct(array $reducers)
    {
        foreach ($reducers as $key => $reducer) {
            $this->addReducer($key, $reducer);
        }
    }

    /**
     * @param array $state
     * @param ActionInterface $action
     * @return array
     */
    public function __invoke(array $state, ActionInterface $action): array
    {
        foreach ($this->getReducers() as $key => $reducer) {
            $state[$key] = call_user_func(
                $reducer,
                isset($state[$key]) ? $state[$key] : null,
                $action
            );
        }
        return $state;
    }

    /**
     * @param callable $reducer
     */
    protected function addReducer(string $key, callable $reducer): void
    {
        $this->reducers[$key] = $reducer;
    }

    /**
     * @return array<string,callable>
     */
    protected function getReducers(): array
    {
        return $this->reducers;
    }
}