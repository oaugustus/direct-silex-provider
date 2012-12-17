<?php
namespace Direct\Router;

use Symfony\Component\HttpFoundation\Response as SfResponse;

/**
 * Response encapsule the ExtDirect response to Direct call.
 *
 * @author Otavio Fernandes <otavio@neton.com.br>
 */
class Response
{
    /**
     * Call type to respond. Where values in ('form','single).
     *   
     * @var string
     */
    protected $type;

    /**
     * Initialize the object setting it type.
     * 
     * @param string $type
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Encode the response into a valid json ExtDirect result.
     * 
     * @param  array $result
     * @return string
     */
    public function encode($result)
    {
        if ('form' == $this->type) {
            array_walk_recursive($result[0], array($this, 'utf8'));
            
            $response = new SfResponse("<html><body><textarea>".json_encode($result[0])."</textarea></body></html>");            
            $response->headers->set('Content-Type', 'text/html');
            
            return $response;
        } else {
            // @todo: check utf8 config option from bundle
            array_walk_recursive($result, array($this, 'utf8'));
            
            $response = new SfResponse(json_encode($result));            
            $response->headers->set('Content-Type', 'application/json');
            
            return $response;
        }
    }

    /**
     * Encode the result recursivily as utf8.
     *
     * @param mixed  $value
     * @param string $key
     */
    private function utf8(&$value, &$key)
    {
        if (is_string($value)) {
            $value = utf8_encode($value);
        }

        if (is_array($value)) {
            array_walk_recursive($value, array($this, 'utf8'));
        }
  }

}
