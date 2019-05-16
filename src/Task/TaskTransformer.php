<?php declare(strict_types=1);

namespace Becklyn\FixCi\Task;

/**
 * Transforms the list of tasks and removes obsolete / wrong parameters.
 */
class TaskTransformer
{
    /**
     * List of tasks that will never be run.
     */
    private const EXCLUDES_ALWAYS = [
        '~composer install~',
        '~composer.*?require~',
        '~mkdir~',
        '~npm.*? build~',
        '~npm.*? install~',
        '~npm.*? i$~',
        '~^echo~',
    ];

    /**
     * List of tasks that should be skipped in --only-fix mode.
     */
    private const EXCLUDES_ONLY_FIX = [
        '~simple-phpunit~',
        '~npm audit~',
        '~npm t(est|$)~',
    ];

    /**
     * Mapping of pattern to all parameters that should be added.
     */
    private const ADD_PARAMETERS = [
        '~prettier-package-json~' => [
            '--write',
        ],
        '~(composer|phpstan|php-cs-fixer|bin/console)~' => [
            '--ansi',
        ],
    ];

    /**
     * Mapping of pattern to all parameters that should be remove.
     */
    private const REMOVE_PARAMETERS = [
        '~composer normalize~' => [
            '--dry-run',
        ],
        '~phpstan~' => [
            '--no-interaction',
            '--no-progress',
        ],
        '~prettier-package-json~' => [
            '--list-different',
        ],
        '~php-cs-fixer~' => [
            '--dry-run',
            '--no-interaction',
        ],
    ];


    /**
     * @param array $tasks
     * @param bool  $onlyFix
     *
     * @return array
     */
    public function transformTasks (array $tasks, bool $onlyFix) : array
    {
        $result = [];

        foreach ($tasks as $task)
        {
            $task = \trim($task);

            if ($this->isExcluded($task, $onlyFix))
            {
                continue;
            }

            foreach (self::REMOVE_PARAMETERS as $pattern => $removals)
            {
                if (\preg_match($pattern, $task))
                {
                    $task = \trim(\str_replace(
                        \array_map(function (string $arg) { return " {$arg}"; }, $removals),
                        '',
                        $task
                    ));
                }
            }

            foreach (self::ADD_PARAMETERS as $pattern => $additions)
            {
                if (\preg_match($pattern, $task))
                {
                    foreach ($additions as $addition)
                    {
                        // don't add duplicate arguments
                        if (!\preg_match('~\\s' . \preg_quote($addition, '~') . '(\\s|$)~', $task))
                        {
                            $task .= " {$addition}";
                        }
                    }
                }
            }

            $result[] = $task;
        }

        return $result;
    }


    /**
     * Checks whether the task is excluded.
     *
     * @param string $task
     * @param bool   $onlyFix
     *
     * @return bool
     */
    private function isExcluded (string $task, bool $onlyFix) : bool
    {
        foreach (self::EXCLUDES_ALWAYS as $exclude)
        {
            if (\preg_match($exclude, $task))
            {
                return true;
            }
        }

        if ($onlyFix)
        {
            foreach (self::EXCLUDES_ONLY_FIX as $exclude)
            {
                if (\preg_match($exclude, $task))
                {
                    return true;
                }
            }
        }

        return false;
    }
}
