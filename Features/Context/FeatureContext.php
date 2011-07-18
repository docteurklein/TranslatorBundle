<?php

namespace Knp\Bundle\TranslatorBundle\Features\Context;

use Behat\BehatBundle\Context\MinkContext;
use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Exception\Pending;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Feature context.
 */
class FeatureContext extends MinkContext
{
    /**
     * @When /^I double-click "([^"]*)"$/
     */
    public function iDoubleclick($xpath)
    {
        $this->getSession()->getPage()->find('xpath', $xpath)->doubleClick();
    }

    /**
     * @Then /^The node "([^"]*)" should contain "([^"]*)"$/
     */
    public function theNodeShouldContain($xpath, $value)
    {
        $node = $this->getSession()->getPage()->find('xpath', $xpath);
        $text = $node->getText();

        assertTrue(false !== strpos($text, $value));
    }

    /**
     * @Given /^display last response$/
     */
    public function displayLastResponse()
    {
        var_dump($this->getSession()->getDriver()->getClient()->getResponse()->getContent());
    }
}
