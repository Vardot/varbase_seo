@varbase_seo @seo
Feature: Varbase SEO - SEO administration pages
  As a site administrator
  I want the SEO administration pages that Varbase SEO provides (Redirect,
  Metatag, Simple XML Sitemap and URL alias patterns) to be reachable

  Scenario: The SEO administration pages are reachable for the administrator
    Given I am a logged in user with the "Webmaster" user
    When I open the administration page "/admin/config/search/redirect"
    Then I should not see "Page not found"
    When I open the administration page "/admin/config/search/metatag"
    Then I should not see "Page not found"
    When I open the administration page "/admin/config/search/simplesitemap"
    Then I should not see "Page not found"
    When I open the administration page "/admin/config/search/path/patterns"
    Then I should not see "Page not found"
