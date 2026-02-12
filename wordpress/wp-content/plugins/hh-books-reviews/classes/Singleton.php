<?php

namespace HHBooksReviews;

/**
 * Tiny base class to make any class a “singleton.”
 *
 * In plain English:
 * - Only one instance of each subclass can exist.
 * - Call `YourClass::getInstance()` to get it.
 * - The constructor is protected (in the child) so you can’t `new` it directly.
 * - Cloning is blocked so you can’t make a second copy.
 */
abstract class Singleton
{
    /**
     * Where we keep the one-and-only instance for each subclass.
     * (Each child class gets its own copy of this via late static binding.)
     *
     * @var static|null
     */
    protected static $instance;

    /**
     * Child classes must define their own constructor.
     * Make it `protected` so outside code can’t call `new` directly.
     */
    abstract protected function __construct();

    /**
     * Block cloning (so nobody can duplicate the singleton).
     */
    private function __clone() {}

    /**
     * Get the one-and-only instance of the subclass.
     * Creates it on first call, then returns the same one after that.
     *
     * Usage:
     *   $obj = YourClass::getInstance();
     *
     * @return static
     */
    public static function getInstance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static(); // late static binding = correct child class
        }
        return static::$instance;
    }
}
