Getting started
===============

Include this bundle in your Symfony 3 project using Composer as follows
(assuming it is installed globally):

```bash
$ composer require xsolve/model-factory-bundle
```

For more information on Composer see its
[Introduction](https://getcomposer.org/doc/00-intro.md).

Afterwards you need to enable this bundle by adding a line to `app/AppKernel.php`
file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Xsolve\ModelFactoryBundle\XsolveModelFactoryBundle(),
        );

        // ...
    }
}
```

That's all and now you're ready to go!


Usage examples
==============

Implementing model factory
--------------------------

This bundle defines a simple interface for model factory that provides
information about whether it supports given object (i.e. is able to produce
model object appropriate for given object) and instantiate such model object.
It also include convenient methods allowing to operate on multiple objects at
once. See `Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryInterface` for
more details.

One you are free to implement this interface with your own model factory
classes, a basic abstract class for model factory is included as well in
`Xsolve\ModelFactoryBundle\ModelFactory\ModelFactory`. It includes all the
necessary logic and leaves out only `supportsObject` and `instantiateModel`
methods. Using it as a base class creating a new model factory class becomes
very easy:

```php
<?php

namespace Example;

use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactory;

class FooModelFactory extends ModelFactory
{
    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return ($object instanceof Foo);
    }
    
    /**
     * {@inheritdoc}
     */
    public function instantiateModel($object)
    {
        /* @var Foo $object */
        return new FooModel($object);
    }
}
```

Using external dependencies in model
------------------------------------

There are cases where some external dependency is required in model object
to return some value. Simple example would be having an model object
representing a package for which volumetric weight needs to be calculated
(which results from multiplying its volume by some coefficient specific for
each shipment company). A helper class calculating such value would usually
be defined as a service in Symfony's DI container, with coefficient provided
via `config.yml` or fetched from some data storage.

With this bundle it is extremely easy to gain access to such services in model
object by utilizing
`Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryAwareModelInterface`. If
`Xsolve\ModelFactoryBundle\ModelFactory\ModelFactory` was used as a base class
for your model factory class, then every model implementing aforementioned
interface will be injected with model factory that was used to produce it.
Since model factories can be defined as services themselves, they can be
injected with any service from DI container and can expose public proxy methods
for model objects to access them.

Following example presents sample usage of this interface. First we define
model factory class:

```php
<?php

namespace Example;

use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactory;

class BazModelFactory extends ModelFactory
{
    /**
     * @var VolumetricWeightCalculator
     */
    protected $volumetricWeightCalculator;
    
    /**
     * @param VolumetricWeightCalculator $volumetricWeightCalculator
     */
    public function __constructor(VolumetricWeightCalculator $volumetricWeightCalculator)
    {
        $this->volumetricWeightCalculator = $volumetricWeightCalculator;
    }
    
    /**
     * @return VolumetricWeightCalculator
     */
    public function getVolumetricWeightCalculator()
    {
        return $this->volumetricWeightCalculator;
    }
    
    /**
     * {@inheritdoc}
     */
    public function supports($object)
    {
        return ($object instanceof Baz);
    }
    
    /**
     * {@inheritdoc}
     */
    public function instantiateModel($object)
    {
        /* @var Baz $object */
        return new BazModel($object);
    }
}
```

Our model class would look as follows (note that
`Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryAwareModelTrait` is
used here to provide convenient `setModelFactory` and `getModelFactory` methods):

```php
<?php

namespace Example;

use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryAwareModelInterface;

class BazModel implements ModelFactoryAwareModelInterface
{
    /**
     * @var Baz
     */
    protected $baz;
    
    /**
     * @param Baz $baz
     */
    public function __construct(Baz $baz)
    {
        $this->baz = $baz;
    }
    
    /**
     * @return float
     */
    public function getVolume()
    {
        return ($this->baz->getLength() * $this->baz->getWidth() * $this->baz->getHeight());
    }
    
    /**
     * @return float
     */
    public function getVolumetricWeight()
    {
        return $this
            ->getModelFactory()
            ->getVolumetricWeightCalculator()
            ->calculate($this->getVolume());
    }
}
```
