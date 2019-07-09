<?php declare(strict_types=1);

namespace Tests\Becklyn\FixCi\CI\Parser;

use Becklyn\FixCi\CI\ParserInterface;
use Becklyn\FixCi\Exception\ParserException;
use PHPUnit\Framework\TestCase;

abstract class AbstractParserTest extends TestCase
{
    /**
     * @var string
     */
    private $fixtures;

    /**
     * @inheritDoc
     */
    protected function setUp () : void
    {
        $this->fixtures = __DIR__ . "/_fixtures/{$this->getFixturesDirectory()}";
    }


    /**
     * Provides the file name + expected tasks mapping
     *
     * @return array
     */
    abstract public function provideParse () : array;


    /**
     * @dataProvider provideParse
     * @param string $fileContent
     * @param array  $expectedTasks
     */
    public function testParse (string $fileName, array $expectedTasks) : void
    {
        $parser = $this->createParser();
        $actual = $parser->parse(\file_get_contents("{$this->fixtures}/{$fileName}"));

        self::assertCount(\count($expectedTasks), $actual, "count: {$this->getFixturesDirectory()}/{$fileName}");
        self::assertArraySubset($expectedTasks, $actual, false, "content: {$this->getFixturesDirectory()}/{$fileName}");
    }


    /**
     *
     */
    public function testInvalidYaml () : void
    {
        $this->expectException(ParserException::class);
        $parser = $this->createParser();
        $parser->parse('this: is: invalid::::');
    }


    /**
     * Provides the tests whether the file names are support
     *
     * @return array
     */
    abstract public function provideSupports () : array;


    /**
     * @dataProvider provideSupports
     * @param string $filePath
     * @param bool   $expected
     */
    public function testSupports (string $filePath, bool $expected) : void
    {
        $parser = $this->createParser();
        self::assertSame($expected, $parser->supports($filePath));
    }




    /**
     * Returns the parser for this test
     *
     * @return ParserInterface
     */
    abstract protected function createParser () : ParserInterface;


    /**
     * @return string
     */
    abstract protected function getFixturesDirectory () : string;
}
