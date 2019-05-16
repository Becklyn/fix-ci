<?php declare(strict_types=1);

namespace Becklyn\FixCi\CI\Parser;

use Becklyn\FixCi\CI\ParserInterface;
use Becklyn\FixCi\Exception\ParserException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class TravisParser implements ParserInterface
{
    /**
     * @inheritDoc
     */
    public function supports (string $filePath) : bool
    {
        return ".travis.yml" === \basename($filePath);
    }


    /**
     * @inheritDoc
     */
    public function parse (string $content) : array
    {
        try
        {
            $config = Yaml::parse($content);
            $allowedKeys = [
                "before_script",
                "script",
            ];
            $tasks = [];

            foreach ($allowedKeys as $key)
            {
                foreach (($config[$key] ?? []) as $script)
                {
                    $tasks[] = $script;
                }
            }

            return $tasks;
        }
        catch (ParseException $e)
        {
            throw new ParserException("Parsing of .travis.yml failed: {$e->getMessage()}", $e);
        }
    }
}
