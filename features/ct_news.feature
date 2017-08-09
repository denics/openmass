@api
Feature: News Content type
  As a MassGov alpha content editor,
  I want to be able to add news pages,
  so that I can inform people about news and press releases.

  Scenario: Verify that the category metatag exists and has the correct value.
    Given I am viewing a "news" content with the title "test service page"
    Then I should see a "category" meta tag of "news"
