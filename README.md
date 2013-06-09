Silex-Direct
============

An ExtDirect service provider for Silex.

Installation
------------
 
Through [Composer](http://getcomposer.org):

```php
{
   require: {
       "oaugustus/direct-silex-provider": "dev-master"
   }        
}
```

Usage
-----

To get up and running, register `DirectExtension` and
manually specify the bundles directories that will be the controllers exposed
to ExtDirect.

Register the DirectServiceProvider;

```php
// app.php
...

$app->register(new Direct\DirectServiceProvider(), array());
```


Expose the controllers.

```php
// app.php
...

// method call without formHandler
$app->post('/controller/method', function() use($app){
    return $app["request"]->get("name");
})->direct();

// method call with formHandler
$app->post('/controller/secondMethod', function(){

})->direct(true);

```

Add the api call into your page templates:

```php
<script type="text/javascript" src="{{url('directapi')}}"></script>
```

Ready, now call the remote method from ExtJS code:

```php
Actions.Controller.method({name: 'Otavio'}, function(result, ev){
    if (ev.type != 'exception')
        console.log(result);
});
```

