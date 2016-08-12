#!/bin/sh
./vendor/phpunit/phpunit/phpunit -c phpunit.xml --coverage-html coverage Tests/
./vendor/phpmd/phpmd/src/bin/phpmd . text ./standards/phpmd.xml --exclude Tests,vendor,coverage,standards,Resources
./vendor/sebastian/phpcpd/phpcpd . --exclude Tests --exclude vendor --exclude coverage --exclude standards --exclude Resources
