<?php declare(strict_types=1);

namespace Becklyn\FixCi\Task;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class TaskRunner
{
    /**
     * Runs the given task.
     *
     * @param string       $task
     * @param SymfonyStyle $io
     *
     * @return bool
     */
    public function run (string $task, SymfonyStyle $io) : bool
    {
        $io->section("Run: `{$task}`");

        $process = Process::fromShellCommandline($task);
        $process->setTimeout(null);
        $process->setTty(true);
        $process->run(
            function ($type, $buffer) : void
            {
                echo $buffer;
            }
        );

        $io->newLine(2);
        $io->writeln(
            $process->isSuccessful()
            ? '<fg=green>✓</>'
            : '❌'
        );
        $io->newLine();

        return $process->isSuccessful();
    }
}
