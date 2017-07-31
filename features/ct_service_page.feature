@api
Feature: Contact Info Content type
  As a MassGov alpha content editor,
  I want to be able to add guide pages,
  so that I can inform people about organizations and services.

  Scenario: Verify that the contact info content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "service_page" content has the correct fields