<?php

namespace Direct;

use Silex\Route as BaseRoute;

/**
 * Extend the Silex\Route standard route class.
 *
 * @package Direct
 */
class Route extends BaseRoute
{
    /**
     * Define if route is exposed to DirectApi.
     *
     * @var bool
     */
    private $directExposed = false;

    /**
     * Define if route exposed to DirectApi is a Form exposed.
     *
     * @var bool
     */
    private $formDirectExposed = false;

    /**
     * Set a route as DirectApi exposed.
     *
     * @param bool $form
     */
    public function direct($form = false)
    {
        $this->directExposed = true;
        $this->formDirectExposed = $form;
    }

    /**
     * Check if a route is DirectExposed route.
     *
     * @return bool
     */
    public function isDirect()
    {
        return $this->directExposed;
    }

    /**
     * Check if a route exposed to Direct is a form call.
     *
     * @return bool
     */
    public function isFormDirect()
    {
        return $this->formDirectExposed;
    }
}