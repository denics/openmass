@api
Feature: Service Details Page Content type
  As a MassGov alpha content editor,
  I want to be able to add service detail pages,
  so that I can inform people about additional information regarding services.

  Scenario: Verify that the service details content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "service_details" content has the correct fields
    And "section_with_heading" paragraph has the correct fields

  Scenario: Verify that the category metatag exists and has the correct value.
    Given I am viewing a "service_details" content with the title "test service details"
    Then I should see a "category" meta tag of "services"
