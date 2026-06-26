@varbase_seo @admin
Feature: Varbase SEO - administration pages and forms
  As a site administrator
  I want the SEO administration pages and forms to work with Varbase SEO enabled

  Scenario: The SEO administration pages and forms are reachable
    Given I am a logged in user with the "Webmaster" user
    When I open the administration page "/admin/config/search/metatag"
    Then I should see "Metatag"
    And I should not see "Page not found"
    When I open the administration page "/admin/config/search/metatag/global"
    Then I should see "Edit default meta tags for Global"
    And I should not see "The website encountered an unexpected error"
    When I open the administration page "/admin/config/search/path/patterns"
    Then I should not see "Page not found"
    When I open the administration page "/admin/config/search/path/patterns/add"
    Then I should see "Add Pathauto pattern"
    When I open the administration page "/admin/config/search/redirect"
    Then I should not see "Page not found"
    When I open the administration page "/admin/config/search/redirect/add"
    Then I should see "Add URL redirect"
    When I open the administration page "/admin/config/search/simplesitemap"
    Then I should see "Simple XML Sitemap"
