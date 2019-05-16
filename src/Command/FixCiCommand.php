<?php declare(strict_types=1);

namespace Becklyn\FixCi\Command;

use Becklyn\FixCi\CI\ParserRegistry;
use Becklyn\FixCi\Task\TaskRunner;
use Becklyn\FixCi\Task\TaskTransformer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class FixCiCommand extends Command
{
    public static $defaultName = "fix-ci";


    /**
     * @var ParserRegistry
     */
    private $parserRegistry;


    /**
     * @var TaskTransformer
     */
    private $taskTransformer;


    /**
     * @var TaskRunner
     */
    private $taskRunner;


    /**
     * @inheritDoc
     */
    public function __construct ()
    {
        parent::__construct();
        $this->parserRegistry = new ParserRegistry();
        $this->taskTransformer = new TaskTransformer();
        $this->taskRunner = new TaskRunner();
    }


    /**
     * @inheritDoc
     */
    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title("Fix CI");

        $tasks = null;
        $files = [
            "/.circleci/config.yml",
            "/.travis.yml",
        ];

        foreach ($files as $file)
        {
            $filePath = \getcwd() . $file;

            if (\is_file($filePath) && \is_readable($filePath))
            {
                $io->comment("Found <fg=blue>{$file}</>");
                $tasks = $this->parserRegistry->parse($filePath);
                break;
            }
        }

        if (null === $tasks)
        {
            $io->error("No CI config found.");
            return 1;
        }

        $tasks = $this->taskTransformer->transformTasks($tasks);
        $tasksWithErrors = 0;

        foreach ($tasks as $task)
        {
            $taskSuccess = $this->taskRunner->run($task, $io);

            if (!$taskSuccess)
            {
                ++$tasksWithErrors;
            }
        }

        if ($tasksWithErrors > 0)
        {
            $io->error(\sprintf(
                "All tasks run, but %d %s failed",
                $tasksWithErrors,
                1 !== $tasksWithErrors ? "tasks" : "task"
            ));
            return 1;
        }

        $io->success("All tasks run successfully.");
        return 0;
    }
}
