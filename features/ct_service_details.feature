@api
Feature: Service Details Page Content type
  As a MassGov alpha content editor,
  I want to be able to add service detail pages,
  so that I can inform people about additional information regarding services.

  Scenario: Verify that the service details content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "service_details" content has the correct fields
    And "section_with_heading" paragraph has the correct fields
