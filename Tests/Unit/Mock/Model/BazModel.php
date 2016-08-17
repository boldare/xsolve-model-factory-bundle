<?php

namespace Xsolve\ModelFactoryBundle\Tests\Unit\Mock\Model;

use Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollectionAwareModelInterface;
use Xsolve\ModelFactoryBundle\ModelFactoryCollection\ModelFactoryCollectionAwareModelTrait;

class BazModel implements ModelFactoryCollectionAwareModelInterface
{
    use ModelFactoryCollectionAwareModelTrait;
}
