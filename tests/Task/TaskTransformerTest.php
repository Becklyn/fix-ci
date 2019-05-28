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
    public function provideExclude () : array
    {
        return [
            // always exclude
            ['composer install --no-interaction --prefer-dist --no-progress', false],
            ['composer install --no-interaction --prefer-dist --no-progress', true],
            ['composer global require localheinz/composer-normalize --no-interaction --prefer-dist --no-progress', false],
            ['composer global require localheinz/composer-normalize --no-interaction --prefer-dist --no-progress', true],
            ['mkdir test', true],
            ['mkdir test', false],
            ['echo "//registry.npmjs.org/:_authToken=${NPM_TOKEN}" > ~/.npmrc', true],
            ['echo "//registry.npmjs.org/:_authToken=${NPM_TOKEN}" > ~/.npmrc', false],
            ['npm run-script build', true],
            ['npm run-script build', false],
            ['npm install', true],
            ['npm install', false],
            ['npm i', true],
            ['npm i', false],

            // also exclude in --only-fix
            ['npm audit', true],
            ['./vendor/bin/simple-phpunit -c phpunit.xml --colors=always', true],
            ['npm test', true],
            ['npm t', true],
        ];
    }

    /**
     * @dataProvider provideExclude
     * @param string $task
     */
    public function testExclude (string $task, bool $onlyFix) : void
    {
        $transformer = new TaskTransformer();
        $result = $transformer->transformTasks([$task], $onlyFix);
        self::assertCount(0, $result);
    }
}
