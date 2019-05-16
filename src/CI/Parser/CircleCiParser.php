<?php declare(strict_types=1);

namespace Becklyn\FixCi\CI\Parser;

use Becklyn\FixCi\CI\ParserInterface;
use Becklyn\FixCi\Exception\ParserException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class CircleCiParser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    public function supports (string $filePath) : bool
    {
        return "config.yml" === \basename($filePath) && ".circleci" === \basename(\dirname($filePath));
    }


    /**
     * @inheritDoc
     */
    public function parse (string $content) : array
    {
        try
        {
            $content = Yaml::parse($content);
            $tasks = [];

            foreach ($content["jobs"] as $job)
            {
                foreach ($job["steps"] as $step)
                {
                    if (!isset($step["run"]))
                    {
                        continue;
                    }

                    if (\is_string($step["run"]))
                    {
                        $tasks[] = $step["run"];
                    }
                    elseif (isset($step["run"]["command"]))
                    {
                        $tasks[] = $step["run"]["command"];
                    }
                }
            }

            return \array_filter($tasks);
        }
        catch (ParseException $e)
        {
            throw new ParserException("Parsing of .travis.yml failed: {$e->getMessage()}", $e);
        }
    }
}
