<?php
namespace Aura\Router\Rule;

class SecureTest extends AbstractRuleTest
{
    public function setup()
    {
        parent::setup();
        $this->rule = new Secure();
    }

    public function testIsSecureMatch_https()
    {
        /**
         * secure required
         */
        $proto = $this->newRoute('/foo/bar/baz')
            ->secure(true);

        // correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTPS' => 'on']);
        $this->assertIsMatch($request, $route);

        // not secure
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTPS' => 'off']);
        $this->assertIsNotMatch($request, $route);

        /**
         * not-secure required
         */
        $proto = $this->newRoute('/foo/bar/baz')
            ->secure(false);

        // correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTPS' => 'off']);
        $this->assertIsMatch($request, $route);

        // secured when it should not be
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['HTTPS' => 'on']);
        $this->assertIsNotMatch($request, $route);
    }

    public function testIsSecureMatch_serverPort()
    {
        /**
         * secure required
         */
        $proto = $this->newRoute('/foo/bar/baz')
            ->secure(true);

        // correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['SERVER_PORT' => '443']);
        $this->assertIsMatch($request, $route);

        // not secure
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['SERVER_PORT' => '80']);
        $this->assertIsNotMatch($request, $route);

        /**
         * not-secure required
         */
        $proto = $this->newRoute('/foo/bar/baz')
            ->secure(false);

        // correct
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['SERVER_PORT' => '80']);
        $this->assertIsMatch($request, $route);

        // secured when it should not be
        $route = clone $proto;
        $request = $this->newRequest('/foo/bar/baz', ['SERVER_PORT' => '443']);
        $this->assertIsNotMatch($request, $route);
    }
}
