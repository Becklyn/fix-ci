<?php declare(strict_types=1);

namespace Tests\Becklyn\FixCi\CI\Parser;

use Becklyn\FixCi\CI\Parser\TravisParser;
use Becklyn\FixCi\CI\ParserInterface;

class TravisParserTest extends AbstractParserTest
{
    /**
     * @inheritDoc
     */
    public function provideParse () : array
    {
        return [
            ['php.yml', [
                'mkdir -p build/logs',
                'composer normalize --indent-size 4 --indent-style space --dry-run',
                'php vendor/bin/php-cs-fixer fix --diff --config vendor/becklyn/php-cs/.php_cs.dist --dry-run --no-interaction',
                'php vendor/bin/phpstan analyse -l 4 --memory-limit 4G --ansi -c phpstan.neon . --no-interaction --no-progress',
                './vendor/bin/simple-phpunit -c phpunit.xml --coverage-clover build/logs/clover.xml',
            ]],
            ['js.yml', [
                'npm run-script build',
                'npm audit',
                'npx prettier-package-json package.json --list-different --tab-width 4',
                'npm test',
            ]],
        ];
    }


    /**
     * @inheritDoc
     */
    public function provideSupports () : array
    {
        return [
            [".travis.yml", true],
            ["/.travis.yml", true],
            ["/test.yml", false],
        ];
    }


    /**
     * @inheritDoc
     */
    protected function createParser () : ParserInterface
    {
        return new TravisParser();
    }


    /**
     * @inheritDoc
     */
    protected function getFixturesDirectory () : string
    {
        return "travis";
    }
}
