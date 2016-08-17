<?php

namespace Xsolve\ModelFactoryBundle\Tests\Unit\Mock\Model;

use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryAwareModelInterface;
use Xsolve\ModelFactoryBundle\ModelFactory\ModelFactoryAwareModelTrait;

class BarModel implements ModelFactoryAwareModelInterface
{
    use ModelFactoryAwareModelTrait;
}
