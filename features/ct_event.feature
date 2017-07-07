@api
Feature: Event Page Content type
  As a MassGov alpha content editor,
  I want to be able to add event pages,
  so that I can inform people about event information and enable them to take action for it.

  Scenario: Verify that the event content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then the content type "event" has the fields:
      | field                       | tag        | type    | multivalue |
      | field-event-ref-contact     | input      | text    | false      |
      | field-event-capacity        | input      | text    | false      |
      | field-event-date            | input      | date    | false      |
      | field-event-description     | textarea   | text    | false      |
      | field-event-image           | input      | submit  | false      |
      | field-event-logo            | input      | submit  | false      |
      | field-event-fees            | input      | text    | false      |
      | field-event-contact-general | input      | text    | true       |
      | field-event-links           | input      | text    | true       |
      | field-event-lede            | input      | text    | false      |
      | field-event-link-sign-up    | input      | text    | false      |
      | field-event-ref-parents     | input      | text    | true       |
      | field-event-rain-date       | input      | text    | false      |
      | field-event-ref-event-2     | input      | text    | false      |
      | field-event-time            | input      | text    | false      |
      | field-event-you-will-need   | textarea   | text    | false      |

