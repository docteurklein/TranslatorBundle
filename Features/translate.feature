Feature: Do edit-in-place translation of an application
  In order to translate an application independantly and easily
  As a user I am able to
  translate every translatable string of my application

  @javascript
  Scenario: Navigate through application searching for translations
    Given I am on "/trans/admin/list"
    When I double-click "//h1"
    Then I should see "#knplabs-translator-form" element

  @javascript
  Scenario: Update a translation
    Given I am on "/trans/admin/list"
    And I double-click "//h1"
    When I fill in "knplabs-translator-value" with "It's alive, ALIVE!"
    And I press "Submit"
    And I reload the page
    Then The node "//h1" should contain "It's alive, ALIVE!"
