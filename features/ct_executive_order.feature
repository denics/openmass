@api
Feature: Executive Order Page Content type
  As a MassGov alpha content editor,
  I want to be able to add executive order pages,
  so that I can inform people about executive orders.

  Scenario: Verify that the category metatag exists and has the correct value.
    Given I am viewing an "executive_order" with the title "test executive order"
    Then I should see a "category" meta tag of "laws-regulations"
