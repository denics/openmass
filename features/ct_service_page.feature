@api
Feature: Service Page Content type
  As a MassGov alpha content editor,
  I want to be able to add guide pages,
  so that I can inform people about organizations and services.

  Scenario: Verify that the contact info content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "service_page" content has the correct fields

  Scenario: Verify that the category metatag exists and has the correct value.
    Given I am viewing a "service_page" with the title "test service page"
    Then I should see a "category" meta tag of "services"
