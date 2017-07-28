@api

Feature: Mass Dashboard
  As a content creator,
  I want a dashboard that makes it easier for me to find the content that I want
  to review or edit,
  so that I can perform my job easily and efficiently.

  Scenario: Verify that authorized users see the tray items
    Given I am logged in as a user with the "author" role
    When I go to "/"
    Then I should see the link "My Content" in the toolbar

  Scenario: Verify author access to the My Work page
    Given I am logged in as a user with the "author" role
    When I go to "/admin/ma-dash/my-work"
    Then the response status code should be 200
    And I should see the link "My Work" in the mass_dashboard_menu

  Scenario: Verify editor access to the Needs Review page
    Given I am logged in as a user with the "editor" role
    When I go to "/admin/ma-dash/needs-review"
    Then the response status code should be 200
    And I should see the link "Needs Review" in the mass_dashboard_menu

  Scenario: Verify authors cannot see the Needs Review page
    Given I am logged in as a user with the "author" role
    When I go to "/admin/ma-dash/needs-review"
    Then the response status code should be 403
    And I should not see the link "Needs Review" in the mass_dashboard_menu

  Scenario: Verify author access to the All Content page
    Given I am logged in as a user with the "author" role
    When I go to "/admin/ma-dash/all-content"
    Then the response status code should be 200
    And I should see the link "All Content" in the mass_dashboard_menu

  Scenario: Verify author access to the Content by Service page
    Given I am logged in as a user with the "author" role
    When I go to "/admin/ma-dash/service-content"
    Then the response status code should be 200
    And I should see the link "Content by Service" in the mass_dashboard_menu

  Scenario: Verify that anonymous users cannot access the dashboard
    Given I am an anonymous user
    When I go to "/admin/ma-dash/my-work"
    Then the response status code should be 403

