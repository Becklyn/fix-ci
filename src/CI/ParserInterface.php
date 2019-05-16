<?php declare(strict_types=1);

namespace Becklyn\FixCi\CI;

use Becklyn\FixCi\Exception\ParserException;

interface ParserInterface
{
    /**
     * Returns whether the parser supports the file at the given file path.
     *
     * @param string $filePath
     *
     * @return bool
     */
    public function supports (string $filePath) : bool;


    /**
     * Parses the file content and returns the parsed tasks.
     *
     * @param string $content
     *
     * @throws ParserException
     *
     * @return string[]
     */
    public function parse (string $content) : array;
}
