## TranslatorBundle

This bundle's purpose is to provide an interface for edition, addition and deletion
of translations messages.

Currently supported formats:

*   YAML
*   XLIFF
*   CSV


Install & setup the bundle
--------------------------

1.  Install via composer

    composer require docteurklein/translator-bundle=~2.0


2.  Add the bundle to your `AppKernel` class

``` php

    // app/AppKernerl.php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Knp\Bundle\TranslatorBundle\KnpTranslatorBundle,
            // ...
        );
        // ...
    }

```


3.  Add routing

``` yaml

    // app/config/routing.yml

    knplabs_translator_admin:
        resource: @KnpTranslatorBundle/Resources/config/routing/edition.yml
            prefix:   /trans/admin

    knplabs_translator:
        resource: @KnpTranslatorBundle/Resources/config/routing/routing.yml
            prefix:   /trans

```

These route files provide the following routes:

    [router] Current routes
    Name                     Method  Pattern
    knplabs_translator_list  GET    /trans/admin/list
    knplabs_translator_get   GET    /trans/{id}/{domain}/{locale}
    knplabs_translator_put   PUT    /trans/




Minimal configuration
---------------------

This bundle requires the activation of the core translator:


``` yaml

    // app/config/config.yml
    framework:
        # ...
        translator:    { fallback: en }
        # ...

```

Additional configuration
------------------------

This bundle relies on the Ext Core library.
You can decide wheter or not it will be included automatically.

``` yaml

    knplabs_translator:
        include_vendor_assets: false # defaults to true

```

Services
--------

This bundle introduces those services:

    knp_translator.dumper.csv                    container Knp\Bundle\TranslatorBundle\Dumper\CsvDumper
    knp_translator.dumper.xliff                  container Knp\Bundle\TranslatorBundle\Dumper\XliffDumper
    knp_translator.dumper.yaml                   container Knp\Bundle\TranslatorBundle\Dumper\YamlDumper
    knp_translator.writer                        container Knp\Bundle\TranslatorBundle\Translation\Writer

    controllers are services too:

    knp_translator.controller.edition    request   Knp\Bundle\TranslatorBundle\Controller\EditionController
    knp_translator.controller.translator request   Knp\Bundle\TranslatorBundle\Controller\TranslatorController



API
---

Updating a given translation key is really simple:


``` php

    $this->get('translator.writer')->write('the key to translate', 'the translated string', 'messages', 'en');

```


Rest API
--------

*   Update `english` translations files for domain `tests` with `translated value` for key `foo.bar.baz`

``` bash

    curl -X PUT http://project-url/trans/  \
        -F 'id=foo.bar.baz' \
        -F 'domain=messages' \
        -F 'locale=en' \
        -F 'value=translate value' 

```

*   Get the translated value of key `foo.bar.baz` for `english` locale for `tests` domain

``` bash

    curl http://project-url/trans/foo.bar.baz/tests/en

```
