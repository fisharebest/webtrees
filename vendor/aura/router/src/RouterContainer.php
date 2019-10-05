<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Router;

use Aura\Router\Rule;
use Psr\Log\NullLogger;

/**
 *
 * A library-specific container.
 *
 * @package Aura.Router
 *
 */
class RouterContainer
{
    /**
     *
     * Generates paths from routes.
     *
     * @var Generator
     *
     */
    protected $generator;

    /**
     *
     * Logs activity in the Matcher.
     *
     * @var Psr\Log\LoggerInterface
     *
     */
    protected $logger;

    /**
     *
     * A factory to create the logger.
     *
     * @var callable
     *
     */
    protected $loggerFactory;

    /**
     *
     * A route map.
     *
     * @var Map
     *
     */
    protected $map;

    /**
     *
     * A factory to create the map.
     *
     * @var callable
     *
     */
    protected $mapFactory;

    /**
     *
     * The route matcher.
     *
     * @var Matcher
     *
     */
    protected $matcher;

    /**
     *
     * A proto-route for the map.
     *
     * @var Route
     *
     */
    protected $route;

    /**
     *
     * A factory to create the route.
     *
     * @var callable
     *
     */
    protected $routeFactory;

    /**
     *
     * An collection of route-matching rules to iterate through.
     *
     * @var RuleIterator
     *
     */
    protected $ruleIterator;

    /**
     *
     * The basepath to use for matching and generating.
     *
     * @var string
     *
     */
    protected $basepath;

    /**
     *
     * Constructor.
     *
     * @param string $basepath The basepath to use for matching and generating.
     *
     */
    public function __construct($basepath = null)
    {
        $this->basepath = $basepath;
        $this->setLoggerFactory([$this, 'loggerFactory']);
        $this->setRouteFactory([$this, 'routeFactory']);
        $this->setMapFactory([$this, 'mapFactory']);
        $this->setMapBuilder([$this, 'buildMap']);
    }

    /**
     * 
     * Creates a Logger
     *
     * @return NullLogger
     *
     * @access protected
     */
    protected function loggerFactory()
    {
        return new NullLogger();
    }

    /**
     * Creates a new Route
     *
     * @return Route
     *
     * @access protected
     */
    protected function routeFactory()
    {
        return new Route();
    }

    /**
     * mapFactory
     *
     * @return mixed
     *
     * @access protected
     */
    protected function mapFactory()
    {
        return new Map($this->getRoute());
    }

    /**
     * Builds a map
     *
     * @param Map $map DESCRIPTION
     *
     * @return mixed
     *
     * @access protected
     */
    protected function buildMap(Map $map)
    {
        // do nothing
    }

    /**
     *
     * Sets the logger factory.
     *
     * @param callable $loggerFactory The logger factory.
     *
     * @return null
     *
     */
    public function setLoggerFactory(callable $loggerFactory)
    {
        $this->loggerFactory = $loggerFactory;
    }

    /**
     *
     * Sets the proto-route factory.
     *
     * @param callable $routeFactory The proto-route factory.
     *
     * @return null
     *
     */
    public function setRouteFactory(callable $routeFactory)
    {
        $this->routeFactory = $routeFactory;
    }

    /**
     *
     * Sets the map factory.
     *
     * @param callable $mapFactory The map factory.
     *
     * @return null
     *
     */
    public function setMapFactory(callable $mapFactory)
    {
        $this->mapFactory = $mapFactory;
    }

    /**
     *
     * Sets the map builder.
     *
     * @param callable $mapBuilder The map builder.
     *
     * @return null
     *
     */
    public function setMapBuilder(callable $mapBuilder)
    {
        $this->mapBuilder = $mapBuilder;
    }

    /**
     *
     * Gets the shared Map instance. Creates it with the map factory, and runs
     * it through the map builder, on first call.
     *
     * @return Map
     *
     */
    public function getMap()
    {
        if (! $this->map) {
            $this->map = call_user_func($this->mapFactory);
            call_user_func($this->mapBuilder, $this->map);
        }
        return $this->map;
    }

    /**
     *
     * Gets the shared Matcher instance.
     *
     * @return Matcher
     *
     */
    public function getMatcher()
    {
        if (! $this->matcher) {
            $this->matcher = new Matcher(
                $this->getMap(),
                $this->getLogger(),
                $this->getRuleIterator()
            );
        }
        return $this->matcher;
    }

    /**
     *
     * Gets the shared Generator instance.
     *
     * @return Generator
     *
     */
    public function getGenerator()
    {
        if (! $this->generator) {
            $this->generator = new Generator($this->getMap(), $this->basepath);
        }
        return $this->generator;
    }

    /**
     *
     * Gets the shared Logger instance.
     *
     * @return Logger
     *
     */
    public function getLogger()
    {
        if (! $this->logger) {
            $this->logger = call_user_func($this->loggerFactory);
        }
        return $this->logger;
    }

    /**
     *
     * Gets the shared proto-route instance.
     *
     * @return Route
     *
     */
    public function getRoute()
    {
        if (! $this->route) {
            $this->route = call_user_func($this->routeFactory);
        }
        return $this->route;
    }

    /**
     *
     * Gets the rule iterator instance.
     *
     * @return RuleIterator
     *
     */
    public function getRuleIterator()
    {
        if (! $this->ruleIterator) {
            $this->ruleIterator = new Rule\RuleIterator([
                new Rule\Secure(),
                new Rule\Host(),
                new Rule\Path($this->basepath),
                new Rule\Allows(),
                new Rule\Accepts(),
                new Rule\Special(),
            ]);
        }
        return $this->ruleIterator;
    }

    /**
     *
     * Gets a new route generation helper
     *
     * @return Helper\Route
     *
     */
    public function newRouteHelper()
    {
        return new Helper\Route($this->getGenerator());
    }

    /**
     *
     * Gets a new raw route generation helper
     *
     * @return Helper\RouteRaw
     *
     */
    public function newRouteRawHelper()
    {
        return new Helper\RouteRaw($this->getGenerator());
    }
}
