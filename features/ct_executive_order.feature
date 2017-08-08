@api
Feature: Executive Order Page Content type
  As a MassGov alpha content editor,
  I want to be able to add executive order pages,
  so that I can inform people about event information and enable them to take action for it.

  Scenario: Verify that the event content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "executive_order" content has the correct fields

  Scenario: Verify that pathauto patterns are applied to Executive order+nodes.
    Given I am viewing an "executive_order" content with the title "Test Executive Order"
    Then I am on "executive-orders/test-executive-order"
