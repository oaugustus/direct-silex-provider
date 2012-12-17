<?php
namespace Direct\Controller;

class DirectController
{
    /**
     * Set the application reference.
     * 
     * @param \Silex\Application $app 
     */
    public function setApplication(\Silex\Application $app)
    {
        $this->app = $app;
    }
}