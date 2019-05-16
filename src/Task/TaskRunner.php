<?php declare(strict_types=1);

namespace Becklyn\FixCi\Task;

use Symfony\Component\Console\Style\SymfonyStyle;

class TaskRunner
{
    /**
     * Runs the given task
     *
     * @param string       $task
     * @param SymfonyStyle $io
     *
     * @return bool
     */
    public function run (string $task, SymfonyStyle $io) : bool
    {
        $io->section("Run: `{$task}`");

        return true;
    }
}
