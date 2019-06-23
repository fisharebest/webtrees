<?php
declare(strict_types = 1);

namespace Middleland\Matchers;

use Psr\Http\Message\ServerRequestInterface;

class Path implements MatcherInterface
{
    use NegativeResultTrait;

    private $path;

    public function __construct(string $path)
    {
        $this->path = rtrim($this->getValue($path), '/');
    }

    public function __invoke(ServerRequestInterface $request): bool
    {
        $path = $request->getUri()->getPath();

        return (($path === $this->path) || stripos($path, $this->path.'/') === 0) === $this->result;
    }
}
