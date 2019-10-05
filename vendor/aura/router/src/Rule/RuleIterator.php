<?php
/**
 *
 * This file is part of Aura for PHP.
 *
 * @license http://opensource.org/licenses/bsd-license.php BSD
 *
 */
namespace Aura\Router\Rule;

use Aura\Router\Exception;
use Iterator;

/**
 *
 * Collection of rules to iterate through.
 *
 * @package Aura.Router
 *
 */
class RuleIterator implements Iterator
{
    /**
     *
     * The rules to iterate through.
     *
     * @var array
     *
     */
    protected $rules = [];

    /**
     *
     * Constructor.
     *
     * @param array $rules The rules to iterate through.
     *
     */
    public function __construct(array $rules = [])
    {
        $this->set($rules);
    }

    /**
     *
     * Sets the rules to iterate through.
     *
     * @param array $rules The rules to iterate through.
     *
     */
    public function set(array $rules)
    {
        $this->rules = [];
        foreach ($rules as $rule) {
            $this->append($rule);
        }
    }

    /**
     *
     * Appends a rule to iterate through.
     *
     * @param callable $rule The rule to iterate through.
     *
     */
    public function append(callable $rule)
    {
        $this->rules[] = $rule;
    }

    /**
     *
     * Prepends a rule to iterate through.
     *
     * @param callable $rule The rule to iterate through.
     *
     */
    public function prepend(callable $rule)
    {
        array_unshift($this->rules, $rule);
    }

    /**
     *
     * Iterator: gets the current rule.
     *
     * @return RuleInterface
     *
     */
    public function current()
    {
        $rule = current($this->rules);
        if ($rule instanceof RuleInterface) {
            return $rule;
        }

        $key = key($this->rules);
        $factory = $this->rules[$key];
        $rule = $factory();
        if ($rule instanceof RuleInterface) {
            $this->rules[$key] = $rule;
            return $rule;
        }

        $message = gettype($rule);
        $message .= ($message != 'object') ?: ' of type ' . get_class($rule);
        $message = "Expected RuleInterface, got {$message} for key {$key}";
        throw new Exception\UnexpectedValue($message);
    }

    /**
     *
     * Iterator: gets the current rule key.
     *
     * @return mixed
     *
     */
    public function key()
    {
        return key($this->rules);
    }

    /**
     *
     * Iterator: moves the iterator forward to the next rule.
     *
     * @return null
     *
     */
    public function next()
    {
        next($this->rules);
    }

    /**
     *
     * Iterator: rewinds to the first rule.
     *
     * @return null
     *
     */
    public function rewind()
    {
        reset($this->rules);
    }

    /**
     *
     * Iterator: is the current position valid?
     *
     * @return bool
     *
     */
    public function valid()
    {
        return current($this->rules) !== false;
    }
}
