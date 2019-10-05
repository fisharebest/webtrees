<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Router;

use Aura\Router\Exception;
use Aura\Router\Rule\RuleIterator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 *
 * Matches against the route map.
 *
 * @package Aura.Router
 *
 */
class Matcher
{
    /**
     *
     * Logging information about which routes were attempted to match.
     *
     * @var array
     *
     */
    protected $logger;

    /**
     *
     * The map of all routes.
     *
     * @var Map
     *
     */
    protected $map;

    /**
     *
     * A collection of matching rules to iterate through.
     *
     * @var RuleIterator
     *
     */
    protected $ruleIterator;

    /**
     *
     * The Route object matched by the router.
     *
     * @var Route|false
     *
     */
    protected $matchedRoute;

    /**
     *
     * The first of the closest-matching failed routes.
     *
     * @var Route
     *
     */
    protected $failedRoute;

    /**
     *
     * The score of the closest-matching failed route.
     *
     * @var int
     *
     */
    protected $failedScore = 0;

    /**
     *
     * Constructor.
     *
     * @param Map $map A route collection object.
     *
     * @param LoggerInterface $logger A logger object.
     *
     * @param RuleIterator $ruleIterator A collection of matching rules.
     *
     */
    public function __construct(
        Map $map,
        LoggerInterface $logger,
        RuleIterator $ruleIterator
    ) {
        $this->map = $map;
        $this->logger = $logger;
        $this->ruleIterator = $ruleIterator;
    }

    /**
     *
     * Gets a route that matches the request.
     *
     * @param ServerRequestInterface $request The incoming request.
     *
     * @return Route|false Returns a route object when it finds a match, or
     * boolean false if there is no match.
     *
     */
    public function match(ServerRequestInterface $request)
    {
        $this->matchedRoute = false;
        $this->failedRoute = null;
        $this->failedScore = 0;
        $path = $request->getUri()->getPath();

        foreach ($this->map as $name => $proto) {
            $route = $this->requestRoute($request, $proto, $name, $path);
            if ($route) {
                return $route;
            }
        }

        return false;
    }

    /**
     *
     * Match a request to a route.
     *
     * @param ServerRequestInterface $request The request to match against.
     *
     * @param Route $proto The proto-route to match against.
     *
     * @param string $name The route name.
     *
     * @param string $path The request path.
     *
     * @return mixed False on failure, or a Route on match.
     *
     */
    protected function requestRoute($request, $proto, $name, $path)
    {
        if (! $proto->isRoutable) {
            return;
        }
        $route = clone $proto;
        return $this->applyRules($request, $route, $name, $path);
    }

    /**
     *
     * Does the request match a route per the matching rules?
     *
     * @param ServerRequestInterface $request The request to match against.
     *
     * @param Route $route The route to match against.
     *
     * @param string $name The route name.
     *
     * @param string $path The request path.
     *
     * @return mixed False on failure, or a Route on match.
     *
     */
    protected function applyRules($request, $route, $name, $path)
    {
        $score = 0;
        foreach ($this->ruleIterator as $rule) {
            if (! $rule($request, $route)) {
                return $this->ruleFailed($request, $route, $name, $path, $rule, $score);
            }
            $score ++;
        }
        return $this->routeMatched($route, $name, $path);
    }

    /**
     *
     * A matching rule failed.
     *
     * @param ServerRequestInterface $request The request to match against.
     *
     * @param Route $route The route to match against.
     *
     * @param string $name The route name.
     *
     * @param string $path The request path.
     *
     * @param mixed $rule The rule that failed.
     *
     * @param int $score The failure score.
     *
     * @return false
     *
     */
    protected function ruleFailed($request, $route, $name, $path, $rule, $score)
    {
        $ruleClass = get_class($rule);
        $route->failedRule($ruleClass);

        if (! $this->failedRoute || $score > $this->failedScore) {
            $this->failedRoute = $route;
            $this->failedScore = $score;
        }

        $this->logger->debug("{path} FAILED {ruleClass} ON {name}", [
            'path' => $path,
            'ruleClass' => $ruleClass,
            'name' => $name
        ]);

        return false;
    }

    /**
     *
     * The route matched.
     *
     * @param Route $route The route to match against.
     *
     * @param string $name The route name.
     *
     * @param string $path The request path.
     *
     * @return Route
     *
     */
    protected function routeMatched($route, $name, $path)
    {
        $this->logger->debug("{path} MATCHED ON {name}", [
            'path' => $path,
            'name' => $name,
        ]);
        $this->matchedRoute = $route;
        return $route;
    }

    /**
     *
     * Get the first of the closest-matching failed routes.
     *
     * @return Route
     *
     */
    public function getFailedRoute()
    {
        return $this->failedRoute;
    }

    /**
     *
     * Returns the result of the call to match() again so you don't need to
     * run the matching process again.
     *
     * @return Route|false|null Returns null if match() has not been called
     * yet, false if it has and there was no match, or a Route object if there
     * was a match.
     *
     */
    public function getMatchedRoute()
    {
        return $this->matchedRoute;
    }
}
