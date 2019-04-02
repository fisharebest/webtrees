<?php
declare(strict_types = 1);

namespace Middleland\Matchers;

use Psr\Http\Message\ServerRequestInterface;

class Pattern implements MatcherInterface
{
    use NegativeResultTrait;

    private $pattern;
    private $flags;

    public function __construct(string $pattern, $flags = 0)
    {
        $this->pattern = $this->getValue($pattern);
        $this->flags = $flags;
    }

    public function __invoke(ServerRequestInterface $request): bool
    {
        return fnmatch($this->pattern, $request->getUri()->getPath(), $this->flags) === $this->result;
    }
}
