[![Build Status](https://travis-ci.org/xsolve-pl/xsolve-model-factory-bundle.svg?branch=master)](https://travis-ci.org/xsolve-pl/xsolve-model-factory-bundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/xsolve-pl/xsolve-model-factory-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/xsolve-pl/xsolve-model-factory-bundle/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/xsolve-pl/model-factory-bundle/v/stable)](https://packagist.org/packages/xsolve-pl/model-factory-bundle)
[![Total Downloads](https://poser.pugx.org/xsolve-pl/model-factory-bundle/downloads)](https://packagist.org/packages/xsolve-pl/model-factory-bundle)
[![Monthly Downloads](https://poser.pugx.org/xsolve-pl/model-factory-bundle/d/monthly)](https://packagist.org/packages/xsolve-pl/model-factory-bundle)
[![License](https://poser.pugx.org/xsolve-pl/model-factory-bundle/license)](https://packagist.org/packages/xsolve-pl/model-factory-bundle)

Table of contents
=================

  * [Introduction](#introduction)
  * [License](#license)
  * [Getting started](#getting-started)
  * [Usage examples](#usage-examples)
    * [Grouping model factories into collections](#grouping-model-factories-into-collections)

Introduction
============

This bundle wraps [xsolve-pl/model-factory](https://packagist.org/packages/xsolve-pl/model-factory)
library and allows to compose collections of model factories declared as services
with the use of tags.

See the library documentation for more details on specific use cases.

License
=======

This bundle is under the MIT license. See the complete license in `LICENSE` file.

Getting started
===============

Include this bundle in your Symfony project using Composer as follows
(assuming it is installed globally):

```bash
$ composer require xsolve-pl/model-factory-bundle
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

That's all - now you're ready to go!


Usage example
=============

Grouping model factories into collections
-----------------------------------------

To make it easy to produce models for multiple objects it is possible to
group model factories into collections. If your application provides multiple
APIs (or multiple API versions that are so different that they utilize
completely different models) you are able to group factories in separate
collections and avoid the risk of producing incorrect models.

Grouping model factories into collections is made easier by providing
a dedicated compiler pass that uses tags on model factory service definitions
to inject them into appropriate collections. Consider following example of
`services.xml` file:

```xml
<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>

        <service id="example.model_factory_collection.first"
                 class="Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollection"
        />

        <service id="example.model_factory_collection.second"
                 class="Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollection"
        />

        <service id="example.model_factory.foo"
                 class="Example\FooModelFactory"
        >
            <tag name="xsolve.model_factory_bundle.model_factory"
                 model-factory-collection-id="example.model_factory_collection.first"
            />
            <tag name="xsolve.model_factory_bundle.model_factory"
                 model-factory-collection-id="example.model_factory_collection.second"
            />
        </service>

    </services>

</container>
```

This snippet defines two model factory collections (with ids
`example.model_factory_collection.first` and
`example.model_factory_collection.second` respectively). It also defines a
single model factory (with id `example.model_factory.foo`). This service has a
tag assigned with `name` attribute equal
`xsolve.model_factory_bundle.model_factory` (which will result in it being
processed by
`Xsolve\ModelFactoryBundle\DependencyInjection\CompilerPass\ModelFactoryCollectionCompilerPass`)
and `model-factory-collection-id` attribute containing service ids of
respective collections.
