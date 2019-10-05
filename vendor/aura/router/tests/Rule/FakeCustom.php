<?php
namespace Aura\Router\Rule;

use Aura\Router\Route;
use Psr\Http\Message\ServerRequestInterface;

class FakeCustom implements RuleInterface
{
    public function __invoke(ServerRequestInterface $request, Route $route)
    {
        $pass = isset($route->extras['aura/router:fake'])
             && $route->extras['aura/router:fake'];

        if ($pass) {
            $route->attributes(['aura/router:fake' => 'fake']);
        }

        return $pass;
    }
}
