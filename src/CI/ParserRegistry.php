<?php declare(strict_types=1);

namespace Becklyn\FixCi\CI;

use Becklyn\FixCi\CI\Parser\CircleCiParser;
use Becklyn\FixCi\CI\Parser\TravisParser;

class ParserRegistry
{
    /**
     * @var ParserInterface[]
     */
    private $parsers = [];


    /**
     *
     */
    public function __construct ()
    {
        $this->parsers = [
            new TravisParser(),
            new CircleCiParser(),
        ];
    }


    /**
     * @return array
     */
    public function parse (string $filePath) : ?array
    {
        foreach ($this->parsers as $parser)
        {
            if ($parser->supports($filePath))
            {
                return $parser->parse(\file_get_contents($filePath));
            }
        }

        return null;
    }
}
