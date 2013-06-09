<?php
namespace Direct;

use Direct\Router\Request as DirectRequest;
use Direct\Router\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Router is the ExtDirect Router class.
 *
 * It provide the ExtDirect Router mechanism.
 *
 * @author Otavio Fernandes <otavio@neton.com.br>
 */
class Router
{
    /**
     * The ExtDirect Request object.
     * 
     * @var \Direct\Request
     */
    protected $request;
    
    /**
     * The ExtDirect Response object.
     * 
     * @var \Direct\Response
     */
    protected $response;
    
    /**
     * The Silex application framework.
     * 
     * @var \Silex\Application
     */
    protected $app = null;
    
    /**
     * Initialize the router object.
     * 
     * @param \Silex\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->request = new DirectRequest($app['request']);
        $this->response = new Response($this->request->getCallType());
    }
        
    /**
     * Do the ExtDirect routing processing.
     *
     * @return JSON
     */
    public function route()
    {
        $batch = array();
                
        foreach ($this->request->getCalls() as $call) {
            $batch[] = $this->dispatch($call);
        }

        return $this->response->encode($batch);
    }

    /**
     * Dispatch a remote method call.
     * 
     * @param  \Direct\Router\Call $call
     * @return Mixed
     */
    private function dispatch($call)
    {

        $path = $call->getAction();
        $method = $call->getMethod();

        $matches = preg_split('/(?=[A-Z])/',$path);
        $route = strtolower(implode('/', $matches));
        $route .= "/".$method;

        $request = $this->app['request'];

        // create the route request
        $routeRequest = Request::create($route, 'POST', $call->getData(), $request->cookies->all(), $request->files->all(), $request->server->all());

        if ('form' == $this->request->getCallType()) {
            $result = $this->app->handle($routeRequest, HttpKernelInterface::SUB_REQUEST);

            $response = $call->getResponse($result);
        } else {
            try{
                $result = $this->app->handle($routeRequest, HttpKernelInterface::SUB_REQUEST);

                $response = $call->getResponse($result);
            }catch(\Exception $e){
                $response = $call->getException($e);
            }            
        }

        return $response;
    }

    /**
     * Retorna o tipo de chamada.
     * 
     * @return String
     */
    private function getCallType()
    {
        return $this->request->getCallType();
    }
    
}