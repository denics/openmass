@api
Feature: Topic Page Content type
  As a MassGov alpha content editor,
  I want to be able to add topic pages,
  so that I can collect information about topics.

  Scenario: Verify that the topic page content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then "topic_page" content has the correct fields

  Scenario: Verify that pathauto patterns are applied to Topic Page nodes.
    Given I am viewing an "topic_page" content with the title "Test Topic Page"
    Then I am on "topics/test-topic-page"