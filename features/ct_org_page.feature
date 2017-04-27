@api
Feature: Organization Landing Page Content type
  As an anonymous user,
  I want to visit an org page in order to learn more information about what
  the agency or organization does, and how I might contact them.

  Scenario: Verify that the org-page content type has the correct fields
    Given I am logged in as a user with the "administrator" role
    Then the content type "org_page" has the fields:
      | field                        | tag        | type                       | multivalue |
      | field-title-sub-text         | input      | text                       | false      |
      | field-ref-actions-6          | input      | text                       | false      |
      | field-action-set-bg-wide     | input      | submit                     | false      |
      | field-action-set-bg-narrow   | input      | submit                     | false      |
      | field-ref-actions-3          | input      | text                       | false      |
      | field-ref-card-view-6        | input      | text                       | false      |
      | field-bg-wide                | input      | submit                     | false      |
      | field-bg-narrow              | input      | submit                     | false      |
      | field-sub-title              | textarea   |                            | false      |
      | field-sub-brand              | input      | submit                     | false      |
      | field-ref-orgs               | input      | text                       | true       |
      | field-link                   | input      | text                       | false      |
      | field-social-links           | input      | text                       | true       |
      | body                         | textarea   |                            | false      |

  Scenario: Verify that pathauto patterns are applied to org_page nodes.
    Given I am viewing an "org_page" content with the title "Run the Test Suite"
    Then I am on "run-test-suite"

  Scenario: Verify validation for background image.
    Given I am viewing an "action" content:
      | title  | Some Featured Action |
      | status | 1                    |
    And I am logged in as a user with the "administrator" role
    When I am viewing an "org_page" content:
      | title                    | Some Nice Org Page 2   |
      | field-action-set-bg-wide | A header image         |
      | field-sub-title          | Some lede text.        |
      | field-ref-actions-3      | Some Featured Action   |
    And I follow "Edit draft"
    And I fill in "edit-field-ref-actions-3-0-target-id" with "Some Featured Action"
    And I press "Save and Create New Draft"
    Then I should see the text "field must not be empty"

  Scenario: Verify validation for social links.
    Given I am logged in as a user with the "administrator" role
    When I am viewing an "org_page" content:
      | title                    | Some Nice Org Page 3   |
      | field-action-set-bg-wide | A header image         |
      | field-sub-title          | Some lede text.        |
    And I follow "Edit draft"
    And I fill in "edit-field-social-links-0-uri" with "http://www.some-incorrect-value.com"
    And I fill in "edit-field-social-links-0-title" with "Incorrect link text"
    And I press "Save and Create New Draft"
    Then I should see the text "is an invalid link value"

  Scenario: Verify that the org page content type displays it's content correctly
    Given I am logged in as a user with the "administrator" role
    And I am viewing an "action" content:
      | title                  | Action Title 1 |
      | status                 | 1              |
    And I am viewing an "action" content:
      | title                  | Action Title 2 |
      | status                 | 1              |
    And I am viewing an "stacked_layout" content:
      | title                  | Guide Title 1  |
      | status                 | 1              |
    And I am viewing an "org_page" content:
      | title                  | Related organization |
      | status                 | 1                    |
    And "icons" terms:
      | name                 | field_sprite_name |
      | Behat Test: Building | building          |
    And I am viewing a "topic" content:
      | title           | Run the Test Suite   |
      | field_icon_term | Behat Test: Building |
    When I am viewing an "org_page" content:
      | title                 | Executive Office of Health and Human Services                                            |
      | field_title_sub_text  | (EOHHS)                                                                                  |
      | field_sub_title       | EOHHS oversees health and general support services to help people                        |
      | field_ref_actions_3   | Action title 1                                                                           |
      | field_ref_actions_6   | Action title 2, Guide Title 1                                                            |
      | field_ref_card_view_6 | Run the Test Suite                                                                       |
      | field_link            | See all EOHHS’s programs and services on classic Mass.gov - http://www.google.com        |
      | field_ref_orgs        | Related organization                                                                     |
      | body                  | The Executive Office of Health and Human Services is responsible through                 |
      | field_social_links    | Facebook - http://www.facebook.com/masshhs, Twitter - http://www.twitter.com/masshhs     |
    Then I should see the text "Executive Office of Health and Human Services" in the "page_banner" region
    And I should see the text "(EOHHS)" in the "page_banner" region
    And I should see the text "OHHS oversees health and general support services to help people" in the "page_header" region
    And I should see the text "Action title 1"
    And I should see the text "Action title 2"
    And I should see the text "Guide Title 1"
    And I should see the link "Related organization"
    And I should see the text "The Executive Office of Health and Human Services is responsible through" in the "stacked_sections" region
    And I should see the link "Facebook" in the "stacked_sections" region
    And I should see the link "Twitter" in the "stacked_sections" region
    And I should see the link "Run the Test Suite" in the "sections_3up"
    And I should see the link "Related organization" in the "link_list"
