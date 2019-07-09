<?php declare(strict_types=1);

namespace Tests\Becklyn\FixCi\Task;

use Becklyn\FixCi\Task\TaskTransformer;
use PHPUnit\Framework\TestCase;

class TaskTransformerTest extends TestCase
{
    /**
     * @return array
     */
    public function provideTransform () : array
    {
        return [
            "prettier-package-json" => ['npx prettier-package-json package.json --list-different --tab-width 4', 'npx prettier-package-json package.json --tab-width 4 --write'],
            "composer normalize" => ['composer normalize --indent-size 4 --indent-style space --dry-run', 'composer normalize --indent-size 4 --indent-style space'],
            "php-cs-fixer" => ['php vendor/bin/php-cs-fixer fix --diff --config vendor/becklyn/php-cs/.php_cs.dist --dry-run --no-interaction', 'php vendor/bin/php-cs-fixer fix --diff --config vendor/becklyn/php-cs/.php_cs.dist'],
            "phpstan" => ['php vendor/bin/phpstan analyse -l 4 --memory-limit 4G -c phpstan.neon . --no-interaction --no-progress', 'php vendor/bin/phpstan analyse -l 4 --memory-limit 4G -c phpstan.neon .'],
            "detect duplicate param" => ['composer normalize --indent-size 4 --indent-style space --dry-run --ansi', 'composer normalize --indent-size 4 --indent-style space --ansi'],
            "match duplicate parameter correctly" => ['npx prettier-package-json package.json --tab-width 4 --write-test', 'npx prettier-package-json package.json --tab-width 4 --write-test --write']
        ];
    }

    /**
     * @dataProvider provideTransform
     *
     * @param string $task
     * @param string $expected
     */
    public function testTransform (string $task, string $expected) : void
    {
        $transformer = new TaskTransformer();
        $resultAll = $transformer->transformTasks([$task], false);
        self::assertCount(1, $resultAll);
        self::assertSame($expected, $resultAll[0]);

        $resultOnlyFix = $transformer->transformTasks([$task], true);
        self::assertCount(1, $resultOnlyFix);
        self::assertSame($expected, $resultOnlyFix[0]);
    }


    /**
     * @return array
     */
    public function provideIsExcluded () : array
    {
        // arguments:
        //  1. bool whether it should be excluded or not
        //  2. the command
        //  3. whether to run with --fix-only

        return [
            // always exclude
            [true, 'composer install --no-interaction --prefer-dist --no-progress', false],
            [true, 'composer install --no-interaction --prefer-dist --no-progress', true],
            [true, 'composer global require localheinz/composer-normalize --no-interaction --prefer-dist --no-progress', false],
            [true, 'composer global require localheinz/composer-normalize --no-interaction --prefer-dist --no-progress', true],
            [true, 'mkdir test', true],
            [true, 'mkdir test', false],
            [true, 'echo "//registry.npmjs.org/:_authToken=${NPM_TOKEN}" > ~/.npmrc', true],
            [true, 'echo "//registry.npmjs.org/:_authToken=${NPM_TOKEN}" > ~/.npmrc', false],
            [true, 'npm run-script build', true],
            [true, 'npm run-script build', false],
            [true, 'npm install', true],
            [true, 'npm install', false],
            [true, 'npm i', true],
            [true, 'npm i', false],

            [true, 'apt install', true],
            [true, 'apt install', false],
            [false, 'aapt install', true],
            [false, 'aapt install', false],
            [true, 'test ; apt install', true],
            [true, 'test ; apt install', false],

            [true, 'pip install', true],
            [true, 'pip install', false],
            [false, 'apip install', true],
            [false, 'apip install', false],
            [true, 'test ; pip install', true],
            [true, 'test ; pip install', false],

            [true, 'source file', true],
            [true, 'source file', false],
            [false, 'asource file', true],
            [false, 'asource file', false],
            [true, 'test ; source file', true],
            [true, 'test ; source file', false],

            // also exclude in --only-fix
            [true, 'npm audit', true],
            [true, './vendor/bin/simple-phpunit -c phpunit.xml --colors=always', true],
            [true, 'npm test', true],
            [true, 'npm t', true],
        ];
    }

    /**
     * @dataProvider provideIsExcluded
     * @param string $task
     */
    public function testIsExcluded (bool $shouldBeExcluded, string $task, bool $onlyFix) : void
    {
        $transformer = new TaskTransformer();
        $result = $transformer->transformTasks([$task], $onlyFix);
        self::assertCount($shouldBeExcluded ? 0 : 1, $result);
    }
}
