<?php
namespace Direct;

use Symfony\Component\HttpFoundation\Response;


/**
 * Api is the ExtDirect Api class.
 *
 * It provide the ExtDirect Api descriptor of exposed Controllers and methods.
 *
 * @author Otavio Fernandes <otavio@neton.com.br>
 */
class Api
{
    /**
     * Bundles to extract the api.
     * 
     * @var array 
     */
    protected $bundles = array();
    
    /**
     * The ExtDirect JSON API description.
     * 
     * @var JSON
     */
    protected $api = null;
    
    /**
     * The Silex application framework.
     * 
     * @var \Silex\Application
     */
    protected $app = null;
    

    /**
     * Initialize the API.
     * 
     * @param array  $bundles The bundles array
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Return the API in JSON format.
     *
     * @return string JSON API description
     */
    public function  __toString()
    {        
        if ($this->app['debug']){
            $this->api = $this->createApi();
        } else {
            $this->api = $this->getCachedApi();
        }
                
        return json_encode($this->api);        
    }  
    
    /**
     * Get the ExtDirect API response.
     * 
     * @return String
     */
    public function getApi()
    {
        if ($this->app['debug']){
            $exceptionLogStr = 
                'Ext.direct.Manager.on("exception", function(error){console.error(Ext.util.Format.format("Remote Call: {0}.{1}\n{2}", error.action, error.method, error.message, error.where)); return false;});';            
        }else {
            $exceptionLogStr = 
                sprintf('Ext.direct.Manager.on("exception", function(error){alert("%s");});', $this->app['direct.exception.message']);
        }
        
        $apiStr = sprintf("Ext.Direct.addProvider(%s);\n%s", $this, $exceptionLogStr);
        
        // force convertion of api to string
        $response = new Response($apiStr);
        
        // set the response header
        $response->headers->set('Content-Type', 'text/javascript');
        
        return $response;
    }

    /**
     * Get the ExtDirect Api response Remoting descriptor.
     *
     * @return string;
     */
    public function getRemoting()
    {
        $apiStr = sprintf("Ext.app.REMOTING_API =%s;", $this);

        $response = new Response($apiStr);

        // set the response header
        $response->headers->set('Content-Type', 'text/javascript');

        // return the response
        return $response;
    }
    
    
    /**
     * Returns the cached api.
     * 
     * @todo Implements the Api cache feature
     * 
     * @return type 
     */
    private function getCachedApi()
    {        
        return $this->createApi();
    }
    
    /**
     * Create the ExtDirect API based on controllers files.
     *
     * @return string JSON description of Direct API
     */
    private function createApi()
    {
        $routes = $this->app["routes"];

        $actions = $this->getRouteActions($routes);

        return array(
            'url' => $this->app['request']->getBaseUrl().
                     $this->app['direct.route.pattern'],
            'type' => $this->app['direct.api.type'],
            'namespace' => $this->app['direct.api.namespace'],
            'id' => $this->app['direct.api.id'],
            'actions' => $actions
        );
    }

    /**
     * Return the cached ExtDirect API.
     *
     * @return string JSON description of Direct API
     */
    protected function getApiFromCache()
    {
        //@todo: implement the cache mechanism
        return json_encode($this->createApi());
    }

    /**
     * Get the route API definition
     *
     * @param array
     * @return array Controllers list
     */
    protected function getRouteActions($routes)
    {
        $actions = array();

        // iterate for all routes
        foreach ($routes->all() as $route){

            // if route is direct exposed
            if ($route->isDirect()){
                $apiParts = $this->getRouteApiParts($route);

                if ($apiParts['exposed']){
                    $path = $apiParts['path'];
                    unset($apiParts['path']);
                    unset($apiParts['exposed']);

                    $actions[$path][] = $apiParts;
                }
            }

        }

        return $actions;
    }

    /**
     * Return the route extdirect api parts.
     *
     * @param $route
     * @return mixed
     */
    protected function getRouteApiParts($route)
    {
        $name = explode('/', $route->getPattern());

        $parts = array(
            'exposed' => false
        );

        // if the name has more than one string pattern
        if (count($name) > 2){
            // get the method name
            $method = array_pop($name);

            // upcase the namespace names
            array_walk($name, function(&$n){
                $n = ucfirst($n);
            });

            $path = implode('', $name);

            $parts = array(
                'path' => $path,
                'exposed' => true,
                'name' => $method,
                'len' => 1
            );

            if ($route->isFormDirect()){
                $parts['formHandler'] = true;
            }

        }

        return $parts;
    }
}
