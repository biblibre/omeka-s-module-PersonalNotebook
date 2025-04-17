MODULE := $(notdir $(CURDIR))
VERSION := $(shell php -r 'echo parse_ini_file("config/module.ini")["version"];')
ZIP := ${MODULE}-${VERSION}.zip

dist: ${ZIP}

.PHONY: ${ZIP}
${ZIP}:
	git archive -o $@ --prefix=${MODULE}/ v${VERSION}

.PHONY: test
test:
	../../vendor/bin/phpunit
	PHP_CS_FIXER_IGNORE_ENV=1 ../../node_modules/.bin/gulp test:module:cs
