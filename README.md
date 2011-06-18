
## TranslatorBundle

This bundle's purpose is to provide an interface for edition, addition and deletion
of translations messages.

Currently supported formats:

*   YAML
*   XLIFF
*   CSV


Install & setup the bundle
--------------------------

1.  Fetch the source code

    Using Git to control your project from project root directory:
    
    ``` bash 

    git submodule add git://github.com/docteurklein/TranslatorBundle.git vendor/bundles/Knplabs/Bundle/TranslatorBundle

    ```
        
    By cloning repository:
    
    ``` bash 

    mkdir -p vendor/bundles/Knplabs/Bundle
    cd !$
    git clone git://github.com/docteurklein/TranslatoBundle.git

    ```

2.  Add the bundle to your `AppKernel` class

    ``` php

    // app/AppKernerl.php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Knplabs\Bundle\TranslatorBundle\KnplabsTranslatorBundle,
            // ...
        );
        // ...
    }
    
    ```

3.  Add the Knplabs namespace to your autoloader

    ```php

    // app/autoload.php
    $loader->registerNamespaces(array(
        'Knplabs' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    );

    ```

4.  Add routing

    ``` yaml

    // app/config/routing.yml

    knplabs_translator_admin:
        resource: @KnplabsTranslatorBundle/Resources/config/routing/edition.yml
            prefix:   /trans/admin

    knplabs_translator:
        resource: @KnplabsTranslatorBundle/Resources/config/routing/routing.yml
            prefix:   /trans

    ```

These route files provide the following routes:

    [router] Current routes
    Name                     Method  Pattern
    knplabs_translator_list  GET     /trans/admin/list
    knplabs_translator_get   GET     /trans/get/{id}/{domain}/{locale}
    knplabs_translator_put   PUT|GET /trans/put/{id}/{domain}/{locale}



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


Services
--------

This bundle introduces those services:

    translator.dumper.csv                    container Knplabs\Bundle\TranslatorBundle\Dumper\CsvDumper
    translator.dumper.xliff                  container Knplabs\Bundle\TranslatorBundle\Dumper\XliffDumper
    translator.dumper.yaml                   container Knplabs\Bundle\TranslatorBundle\Dumper\YamlDumper
    translator.writer                        container Knplabs\Bundle\TranslatorBundle\Translation\Translator

    controllers are services too:

    knplabs_translator.controller.edition    request   Knplabs\Bundle\TranslatorBundle\Controller\EditionController
    knplabs_translator.controller.translator request   Knplabs\Bundle\TranslatorBundle\Controller\TranslatorController


API
---

    ``` php

    class Knplabs\Bundle\TranslatorBundle\Translation\Translator extends Symfony\Bundle\FrameworkBundle\Translation\Translator
    {
        public function update($id, $value, $domain, $locale);

        public function getResources($locale, $domain);

        public function getFallbackLocale();

        public function getCatalog($locale);

        public function getLocales();

        public function all();


    ```

Updating a given translation key is really simple:


    ``` php

    $this->get('translator.writer')->update('the key to translate', 'the translated string', 'messages', 'en');

    ```


Rest API
--------

*   Update `english` translations files for domain `tests` with `translated value` for key `foo.bar.baz`

    ``` bash

    curl -X PUT http://project-url/trans/put/foo.bar.baz/tests/en?value=translated+value

    ```

*   Get the translated value of key `foo.bar.baz` for `english` locale for `tests` domain

    ``` bash

    curl -X GET http://project-url/trans/get/foo.bar.baz/tests/en

    ```
