<?php declare(strict_types=1);

namespace Tests\Becklyn\FixCi\CI\Parser;

use Becklyn\FixCi\CI\Parser\CircleCiParser;
use Becklyn\FixCi\CI\ParserInterface;

class CircleCiParserTest extends AbstractParserTest
{
    /**
     * @inheritDoc
     */
    public function provideParse () : array
    {
        return [
            ['config.yml', [
                'nested command',
                'simple command',
                'nested command 2',
                'simple command 2',
            ]],
        ];
    }


    /**
     * @inheritDoc
     */
    public function provideSupports () : array
    {
        return [
            [".circleci/config.yml", true],
            ["/.circleci/config.yml", true],
            [".circleci/config.yaml", false],
            [".circleci/circleci.yaml", false],
        ];
    }


    /**
     * @inheritDoc
     */
    protected function createParser () : ParserInterface
    {
        return new CircleCiParser();
    }


    /**
     * @inheritDoc
     */
    protected function getFixturesDirectory () : string
    {
        return "circleci";
    }
}
