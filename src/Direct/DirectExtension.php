<?php
namespace Direct;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Direct\Api\Api;
use Direct\Router\Router;
use Symfony\Component\HttpFoundation\Response;

class DirectExtension implements ServiceProviderInterface
{
    /**
     * Register the Extension parameters and services.
     * 
     * @param Application $app
     */
    public function register(Application $app)
    {        
        // direct api
        $app['direct.api'] = function() use ($app){
            return new Api($app);
        };
        
        // direct router
        $app['direct.router'] = function() use ($app){
            return new Router($app);
        };
        
        // the direct api route
        $app->get('/api.js', function(Application $app){            
            return $app['direct.api']->getApi();
        })->bind('directapi');
        
        // the direct router route
        $app->post('/route', function(Application $app){
            // handle the route 
            return $app['direct.router']->route();
        });        
    }

    /**
     * Setup the extension.
     * 
     * @param Application $app 
     */
    public function boot(Application $app)
    {
        // default configs
        $app['direct.bundles'] = isset($app['direct.bundles']) ? $app['direct.bundles'] : array();
        $app['direct.route.pattern'] = isset($app['direct.route.pattern']) ? $app['direct.route.pattern'] : '/route';
        $app['direct.api.type'] = isset($app['direct.api.type']) ? $app['direct.api.type'] : 'remoting';            
        $app['direct.api.namespace'] = isset($app['direct.api.namespace']) ? $app['direct.api.namespace'] : 'Actions';
        $app['direct.api.id'] = isset($app['direct.api.id']) ? $app['direct.api.id'] : 'API';                    
        $app['direct.exception.message'] = isset($app['direct.exception.message']) ? $app['direct.exception.message'] : 'Whoops, looks like something went wrong.';
    }    
}