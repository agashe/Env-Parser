<?php

use PHPUnit\Framework\TestCase;
use EnvParser\Parser;

class ParserTest extends TestCase
{
    /**
     * @var Parser $parser
     */
    private $parser;

    /**
     * ParserTest SetUp
     * 
     * @return void
     */
    public function setUp(): void
    {
        $this->parser = new Parser();
    }

    /**
     * Test parser can read .env file.
     *
     * @runInSeparateProcess
     * @return void
     */
    public function testParserCanReadEnvFile()
    {
        $result = $this->parser->parse('tests/.env.example');

        $validResults = [
            'TEST_NO_QUOTES' => 'Parser',
            'TEST_SPACES' => "1.0",
            'TEST_QUOTES' => 'test',
            'TEST_TRUE' => true,
            'TEST_FALSE' => false,
            'TEST_NULL' => null,
            'TEST_EMPTY' => '',
            'TEST_FLOAT' => 1500.223,
            'TEST_INT' => 123,
            'TEST_COMMENT_1' => 'test',
            'TEST_COMMENT_2' => 'test#test',
            'TEST_COMMENT_3' => 'some text',
            'TEST_VAR' => 'test/Parser',
            'TEST_DEFAULT_VAL' => 'test or some text like Parser'
        ];

        foreach ($validResults as $key => $value) {
            $this->assertTrue(($result[$key] == $value) &&
                ($_ENV[$key] == $value) &&
                ($_SERVER[$key] == $value) &&
                (getenv($key) == $value)
            );
        }
    }
 
    /**
     * Test router will through exception if no routes were provided.
     *
     * @runInSeparateProcess
     * @return void
     */
    public function testRouterWillThroughExceptionIfNoRoutesWereProvided()
    {
        $this->expectException(\InvalidArgumentException::class);
        
        $this->parser->parse('.env.not.found');
    }
}