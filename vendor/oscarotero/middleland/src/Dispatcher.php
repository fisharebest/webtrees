<?php
declare(strict_types = 1);

namespace Middleland;

use Closure;
use InvalidArgumentException;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Dispatcher implements MiddlewareInterface, RequestHandlerInterface
{
    /**
     * @var MiddlewareInterface[]
     */
    private $middleware;

    /**
     * @var ContainerInterface|null
     */
    private $container;

    /**
     * @var RequestHandlerInterface|null
     */
    private $next;

    /**
     * @param MiddlewareInterface[]|string[]|array[]|Closure[] $middleware
     */
    public function __construct(array $middleware, ?ContainerInterface $container = null)
    {
        if (empty($middleware)) {
            throw new LogicException('Empty middleware queue');
        }

        $this->middleware = $middleware;
        $this->container = $container;
    }

    /**
     * Magic method to execute the dispatcher as a callable
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->dispatch($request);
    }

    /**
     * Return the next available middleware in the stack.
     *
     * @return MiddlewareInterface|false
     */
    private function get(ServerRequestInterface $request)
    {
        $middleware = current($this->middleware);
        next($this->middleware);

        if ($middleware === false) {
            return $middleware;
        }

        if (is_array($middleware)) {
            $conditions = $middleware;
            $middleware = array_pop($conditions);

            foreach ($conditions as $condition) {
                if ($condition === true) {
                    continue;
                }

                if ($condition === false) {
                    return $this->get($request);
                }

                if (is_string($condition)) {
                    $condition = new Matchers\Path($condition);
                } elseif (!is_callable($condition)) {
                    throw new InvalidArgumentException('Invalid matcher. Must be a boolean, string or a callable');
                }

                if (!$condition($request)) {
                    return $this->get($request);
                }
            }
        }

        if (is_string($middleware)) {
            if ($this->container === null) {
                throw new InvalidArgumentException(sprintf('No valid middleware provided (%s)', $middleware));
            }

            $middleware = $this->container->get($middleware);
        }

        if ($middleware instanceof Closure) {
            return self::createMiddlewareFromClosure($middleware);
        }

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        throw new InvalidArgumentException(sprintf('No valid middleware provided (%s)', is_object($middleware) ? get_class($middleware) : gettype($middleware)));
    }

    /**
     * Dispatch the request, return a response.
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        reset($this->middleware);

        return $this->handle($request);
    }

    /**
     * @see RequestHandlerInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middleware = $this->get($request);

        if ($middleware === false) {
            if ($this->next !== null) {
                return $this->next->handle($request);
            }

            throw new LogicException('Middleware queue exhausted');
        }

        return $middleware->process($request, $this);
    }

    /**
     * @see MiddlewareInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $this->next = $next;

        return $this->dispatch($request);
    }

    /**
     * Create a middleware from a closure
     */
    private static function createMiddlewareFromClosure(Closure $handler): MiddlewareInterface
    {
        return new class($handler) implements MiddlewareInterface {
            private $handler;

            public function __construct(Closure $handler)
            {
                $this->handler = $handler;
            }

            public function process(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
            {
                return call_user_func($this->handler, $request, $next);
            }
        };
    }
}
