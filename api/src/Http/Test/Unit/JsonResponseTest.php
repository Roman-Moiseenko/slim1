<?php

namespace App\Http\Test\Unit;

use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use function DI\string;

class JsonResponseTest extends TestCase
{

    /**
     * @covers \JsonResponse
     * @dataProvider getCases
     * @param mixed $source
     * @param mixed $expert
     */
    public function testResponse($source, $expert): void
    {
        $response = new JsonResponse($source, 200);
        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals($expert, $response->getBody()->getContents());
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return array<mixed>
     */
    public function getCases(): array
    {
        $object = new \stdClass();
        $object->str = 'value';
        $object->int = 1;
        $object->none = null;
        $array = [
            'str' => 'value',
            'int' => 1,
            'none' => null
        ];
        return [
            'null' => [null, 'null'],
            'empty' => ['', '""'],
            'number' => [12, '12'],
            'string' => ['12', '"12"'],
            'object' => [$object, '{"str":"value","int":1,"none":null}'],
            'array' => [$array, '{"str":"value","int":1,"none":null}'],
        ];
    }

}