<?php

namespace App\Managers;

use Throwable;

class HookManager
{
    public const SEQUENCE_CORE = 0;

    public const SEQUENCE_NORMAL = 256;

    public const SEQUENCE_LATE = 512;

    public const SEQUENCE_LAST = 768;

    public const CONTINUE = false;

    public const ABORT = true;

    protected array $hooks = [];

    /**
     * Register Hook
     */
    public function add(string $name, callable $callback, int $sequence = self::SEQUENCE_NORMAL): void
    {
        $this->hooks[$name][$sequence][] = &$callback;
    }

    /**
     * Call Hook
     */
    public function call(string $name, mixed $params): bool
    {
        $hooks = $this->getHooks();
        if (! isset($hooks[$name])) {
            return self::CONTINUE;
        }

        ksort($hooks[$name], SORT_NUMERIC);
        foreach ($hooks[$name] as $priority => $hookList) {
            foreach ($hookList as $callback) {
                if (call_user_func_array($callback, array_merge([$name], $params)) === self::ABORT) {
                    return self::ABORT;
                }
            }
        }

        return self::CONTINUE;
    }

    public function getHooks(?string $hookName = null): ?array
    {
        $hooks = $this->hooks;
        if ($hookName === null) {
            return $hooks;
        }

        if (isset($hooks[$hookName])) {
            return $hooks[$hookName];
        }

        $returner = null;

        return $returner;
    }

    /**
     * Clear hook by name
     *
     *
     * @return [type]
     */
    public function clear(string $name): void
    {
        if (isset($this->hooks[$name])) {
            unset($this->hooks[$name]);
        }
    }

    /**
     * Handle the given exception.
     *
     * @param  mixed  $passable
     * @return mixed
     *
     * @throws Throwable
     */
    protected function handleException(mixed $arguments, Throwable $e)
    {
        throw $e;
    }
}
