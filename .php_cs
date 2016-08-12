<?php

use Symfony\CS\Fixer\Contrib\HeaderCommentFixer;

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->exclude('vendor')
;

$header = <<<EOF
This file is part of the XsolveModelFactoryBundle package.

(c) XSolve <http://github.com/xsolve-pl/>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
EOF;

HeaderCommentFixer::setHeader($header);

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->fixers(['-psr0'])
    ->finder($finder)
;
