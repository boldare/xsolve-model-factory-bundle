#!/usr/bin/env bash

vendor/bin/phpstan analyse -l 7 -c phpstan.neon DependencyInjection/ XsolveModelFactoryBundle.php
