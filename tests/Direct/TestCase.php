<?php

namespace Direct\Tests;

use Silex\WebTestCase;

class TestCase extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../../../app.php';
    }
}