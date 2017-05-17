@api
Feature: Topic Content type
  As a MassGov content editor,
  I want to be able to add content for topics,
  so that I can help Bay Staters get the best information they need to fulfill basic tasks.

  Scenario: Verify that the topic content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then the content type "topic" has the fields:
    | field                     | tag      | type   | multivalue |  required |
    | field-featured-image      | input    | submit | false      |  false    |
    | field-image-credit        | input    | text   | true       |  false    |
    # Required. Hero image; use file browser modal widget. Due to widget, cannot check required status.
    | field-lede                | textarea |        | false      |  false    |
    | field-icon-term           | select   |        | false      |  true     |
    # Required. Provide machine names for each of the SVG icons from the styleguide.
    | field-section             | select   |        | false      |  true     |
    # Required. Entity ref to section content. Use a select widget, single value.
    | field-common-content      | input    | text   | false      |  false    |
    # Optional. Allows 4 items. Autocomplete entity ref to actions. Autocomplete should be limited to actions that reference subtopics that reference this topic.

  Scenario: Verify that pathauto patterns are applied to topic nodes.
    Given "icons" terms:
      | name                 | field_sprite_name |
      | Behat Test: Building | building          |
    And I am viewing a "topic" content:
      | title           | Run the Test Suite   |
      | field_icon_term | Behat Test: Building |
    Then I am at "topics/run-the-test-suite"

  Scenario: Verify top actions markup is correct
    Given default test content exists
    When I visit the test "topic" "Behat Test: State Parks & Recreation"
    Then I should see the correct markup for the top actions

  Scenario: Verify custom ordered subtopics.
    Given I am logged in as a user with the "administrator" role
    And "icons" terms:
      | name                 | field_sprite_name |
      | Behat Test: Building | building          |
    And I am viewing a "topic" content:
      | title           | Test topic           |
      | field_icon_term | Behat Test: Building |
    Then I should see the link "Reorder Callout Cards"
