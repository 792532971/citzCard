<?php

include __DIR__ . '/../vendor/autoload.php';

class ParserTest extends PHPUnit_Framework_TestCase
{
    protected $parser;

    public function setUp()
    {
        $this->parser = new \cszchen\citizenid\Parser();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage param "id" must not be null.
     */
    public function testIsNull()
    {
        $this->parser->setId('');
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessageRegExp  /.*must be string./
     */
    public function testIsString()
    {
        $id = 350626198701084431;
        $this->parser->setId($id);
    }

    public function test18MaleValidate()
    {
        $id = '350626198701084431';
        $this->parser->setId($id);
        $isValidate = $this->parser->isValidate();
        $this->assertTrue($isValidate);
        $this->assertEquals($this->parser->getGender(), \cszchen\citizenid\Parser::GENDER_MALE);
        $this->assertEquals($this->parser->getBirthday(), 19870108);
    }



    public function testNotValidate()
    {
        $id = '350626198701084433';
        $this->parser->setId($id);
        $isValidate = $this->parser->isValidate();
        $this->assertTrue(!$isValidate);
    }
}