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

    git submodule add git://github.com/docteurklein/TranslatorBundle.git vendor/bundles/Knp/Bundle/TranslatorBundle

    ```
        
    By cloning repository:
    
    ``` bash 

    mkdir -p vendor/bundles/Knp/Bundle
    cd !$
    git clone git://github.com/docteurklein/TranslatoBundle.git

    ```
    
    By including into deps file:
    
    ``` ./deps-file 

    [TranslatorBundle]
		git=git://github.com/docteurklein/TranslatorBundle.git
		target=/bundles/Knp/Bundle/TranslatorBundle

    ```

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

3.  Add the Knp namespace to your autoloader

    ```php

    // app/autoload.php
    $loader->registerNamespaces(array(
        'Knp' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    );

    ```

4.  Add routing

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
        enabled: false # defaults to true
        roles: [ ROLE_TRANSLATE ] # roles that are needed to see translation form, defaults to null

    ```

Role based conditions
---------------------

Additionaly to `enabled` option, you can decide to display the translation form per user's roles.
Users need to be authenticated and need all the roles you configured above to see the form.


Services
--------

This bundle introduces those services:

    translator.dumper.csv                    container Knp\Bundle\TranslatorBundle\Dumper\CsvDumper
    translator.dumper.xliff                  container Knp\Bundle\TranslatorBundle\Dumper\XliffDumper
    translator.dumper.yaml                   container Knp\Bundle\TranslatorBundle\Dumper\YamlDumper
    translator.writer                        container Knp\Bundle\TranslatorBundle\Translation\Translator

    controllers are services too:

    knplabs_translator.controller.edition    request   Knp\Bundle\TranslatorBundle\Controller\EditionController
    knplabs_translator.controller.translator request   Knp\Bundle\TranslatorBundle\Controller\TranslatorController


API
---

    ``` php

    class Knp\Bundle\TranslatorBundle\Translation\Translator extends Symfony\Bundle\FrameworkBundle\Translation\Translator
    {

        public function isTranslated($id, $domain, $locale);

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
