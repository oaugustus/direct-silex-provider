Silex-Direct
============

ExtDirect Extension to Silex Micro-framework.

Installation
------------
 
Through [Composer](http://getcomposer.org):

```php
{
   require: {
       "oaugustus/silex-direct": "dev-master"
   }        
}
```

Usage
-----

To get up and running, register `DirectExtension` and
manually specify the bundles directories that will be the controllers exposed
to ExtDirect.

Register the DirectExtension;

```php
// app.php
use Direct\DirectExtension;
... 

$app->register(new DirectExtension(), array(    
    'direct.bundles' => array(
        'Neton' => __DIR__ // bundle namespace and the bundle directory location
    )
));
```


Create the controllers.

```php
// Neton/Controller/EventController.php
namespace Neton\Controller;

use Direct\Controller\DirectController;

class EventController extends DirectController
{
    /**
     * Use the 'form' annotation to implement a direct call that supports form
     * handlers and 'remote' annotation to exposes the method to Api.
     * 
     * @form
     * @remote
     */
    public function testeAction($params, $files)
    {        
        return sprintf('Hello %s', $params['name']);
    }
}
```

Add the api call into your page templates:

```php
<script type="text/javascript" src="{{url('directapi')}}"></script>
```

Ready, now call the remote method from ExtJS code:

```php
Actions.Neton_Event.teste({name: 'Otavio'}, function(result, ev){
    if (ev.type != 'exception')
        console.log(result);
});
```

