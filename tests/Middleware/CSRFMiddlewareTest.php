<?php

namespace SilverStripe\GraphQL\Tests\Middleware;

use Exception;
use SilverStripe\GraphQL\Middleware\CSRFMiddleware;
use SilverStripe\Security\SecurityToken;

class CSRFMiddlewareTest extends MiddlewareProcessTestBase
{
    private bool $securityTokenWasEnabled = false;

    protected function setUp(): void
    {
        parent::setUp();
        // CSRF token check is normally disabled for unit-tests
        $this->securityTokenWasEnabled = SecurityToken::is_enabled();
        if (!$this->securityTokenWasEnabled) {
            SecurityToken::enable();
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (!$this->securityTokenWasEnabled) {
            SecurityToken::disable();
        }
    }

    public function testItDoesntDoAnythingIfNotAMutation()
    {
        $this->assertEquals('resolved', $this->simulateMiddlewareProcess(
            new CSRFMiddleware(),
            'query testQuery { foo }'
        ));
        $this->assertEquals('resolved', $this->simulateMiddlewareProcess(
            new CSRFMiddleware(),
            ' not a valid graphql query and no one cares'
        ));
    }

    public function testItThrowsIfNoTokenIsProvided()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/must provide a CSRF token/');
        $result = $this->simulateMiddlewareProcess(
            new CSRFMiddleware(),
            ' mutation someMutation { tester }'
        );
        $this->assertNotEquals('resolved', $result);
        $result = $this->simulateMiddlewareProcess(
            new CSRFMiddleware(),
            ' mutation someMutation { tester }'
        );
        $this->assertNotEquals('resolved', $result);
        $graphql = <<<GRAPHQL
mutation MyMutation(\$SomeArg:string!) {
    someMutation(Foo:\$SomeArg) {
        tester
    }
}
GRAPHQL;

        $result = $this->simulateMiddlewareProcess(
            new CSRFMiddleware(),
            $graphql
        );
        $this->assertNotEquals('resolved', $result);
        $graphql = <<<GRAPHQL
fragment myFragment on File {
    id
    width
}
mutation someMutation {
        tester
    }
}
GRAPHQL;

        $result = $this->simulateMiddlewareProcess(
            new CSRFMiddleware(),
            $graphql
        );
        $this->assertNotEquals('resolved', $result);
    }


    public function testItThrowsIfTokenIsInvalid()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Invalid CSRF token/');
        $result = $this->simulateMiddlewareProcess(
            new CSRFMiddleware(),
            ' mutation someMutation { tester }',
            ['token' => 'fail']
        );
        $this->assertNotEquals('resolved', $result);
    }

    public function testItResolvesIfTokenIsValid()
    {
        $token = SecurityToken::inst()->getValue();
        $result = $this->simulateMiddlewareProcess(
            new CSRFMiddleware(),
            ' mutation someMutation { tester }',
            ['token' => $token]
        );
        $this->assertEquals('resolved', $result);
    }
}
