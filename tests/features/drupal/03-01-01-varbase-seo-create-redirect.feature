@varbase_seo @content
Feature: Varbase SEO - create a URL redirect
  As a site administrator
  I want to create a URL redirect with Varbase SEO enabled

  Scenario: Create a URL redirect
    Given I am a logged in user with the "Webmaster" user
    When I am on "/admin/config/search/redirect/add"
    Then I should see "Add URL redirect"
    When I fill the field "#edit-redirect-source-0-path" with "old-seo-page"
    And I fill the field "#edit-redirect-redirect-0-uri" with "<front>"
    And I press the "Save" button
    Then I should not see "Access denied"
    And I should not see "The website encountered an unexpected error"
    When I open the administration page "/admin/config/search/redirect"
    Then I should see "old-seo-page"
