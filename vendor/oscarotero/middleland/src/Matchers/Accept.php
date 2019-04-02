<?php
declare(strict_types = 1);

namespace Middleland\Matchers;

use Psr\Http\Message\ServerRequestInterface;

class Accept implements MatcherInterface
{
    use NegativeResultTrait;

    private $accept;

    public function __construct(string $accept)
    {
        $this->accept = $this->getValue($accept);
    }

    public function __invoke(ServerRequestInterface $request): bool
    {
        return is_int(stripos($request->getHeaderLine('Accept'), $this->accept)) === $this->result;
    }
}
