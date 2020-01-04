<?php
declare(strict_types=1);

namespace PetWatcher\Tests\Validation;

use PetWatcher\Application\Validation\Validator;
use PHPUnit\Framework\TestCase;
use Respect\Validation\Validator as v;
use Slim\Psr7\Environment;
use Slim\Psr7\Factory\ServerRequestFactory;

class ValidatorTest extends TestCase
{
    protected $validator;

    protected function setUp(): void
    {
        $_SERVER = Environment::mock(
            [
                'HTTP_CONTENT_TYPE' => 'multipart/form-data',
                'REQUEST_METHOD' => 'POST',
            ]
        );

        $this->validator = new Validator();
    }

    public function testValidDataInputValidation(): void
    {
        $_POST = [
            'alphanumeric' => 'Alphanumeric1',
        ];
        $request = ServerRequestFactory::createFromGlobals();

        $this->validator->validate(
            $request,
            [
                'alphanumeric' => v::alnum(),
            ]
        );

        $this->assertFalse($this->validator->failed());
        $this->assertEmpty($this->validator->getErrors());
    }

    public function testInvalidDataInputValidation(): void
    {
        $_POST = [
            'alphanumeric' => 'Non Alphanumeric!',
        ];
        $request = ServerRequestFactory::createFromGlobals();

        $this->validator->validate(
            $request,
            [
                'alphanumeric' => v::alnum(),
            ]
        );

        $this->assertTrue($this->validator->failed());
        $this->assertNotEmpty($this->validator->getErrors());
    }

    public function testMissingDataInputValidation(): void
    {
        $_POST = [];
        $request = ServerRequestFactory::createFromGlobals();

        $this->validator->validate(
            $request,
            [
                'missing_element' => v::notEmpty(),
            ]
        );

        $this->assertTrue($this->validator->failed());
        $this->assertNotEmpty($this->validator->getErrors());
    }

    // TODO assess whether test is necessary/part of unit testing
    public function testValidImageInputValidation(): void
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    // TODO assess whether test is necessary/part of unit testing
    public function testInvalidImageInputValidation(): void
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function testMissingImageInputValidation(): void
    {
        $_FILES = [];

        $this->validator->validate(
            $_FILES,
            [
                'file' => v::image(),
                'size' => v::size(null, '1MB'),
            ],
            true
        );

        $this->assertTrue($this->validator->failed());
        $this->assertNotEmpty($this->validator->getErrors());
    }
}
