<?php

namespace Neton\Controller;

class TestController
{
    /**
     * @remote
     */
    public function testAction()
    {
        return 'Success';
    }
}