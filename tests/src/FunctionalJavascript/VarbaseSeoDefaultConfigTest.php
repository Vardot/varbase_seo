<?php

namespace Drupal\Tests\varbase_seo\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Tests Varbase SEO default cofnig test.
 *
 * @group varbase_seo
 */
class VarbaseSeoDefaultConfigTest extends WebDriverTestBase {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'olivero';

  /**
   * {@inheritdoc}
   */
  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'rdf',
    'metatag',
    'metatag_facebook',
    'metatag_google_plus',
    'metatag_hreflang',
    'metatag_mobile',
    'metatag_open_graph',
    'metatag_twitter_cards',
    'metatag_verification',
    'pathauto',
    'redirect',
    'redirect_404',
    'redirect_domain',
    'schema_metatag',
    'schema_article',
    'schema_item_list',
    'schema_web_page',
    'schema_web_site',
    'simple_sitemap',
    'yoast_seo',
    'script_manager',
    'varbase_seo',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Insall the Claro admin theme.
    $this->container->get('theme_installer')->install(['claro']);

    // Set the Claro theme as the default admin theme.
    $this->config('system.theme')->set('admin', 'claro')->save();

    // Create the SEO Admin user with all seo_admin user role permissions.
    $seo_admin_permissions = $this->config('user.role.seo_admin')->get('permissions');
    $seo_admin_available_permissions = [];
    $available_permissions = array_keys(\Drupal::service('user.permissions')->getPermissions());
    foreach ($seo_admin_permissions as $seo_admin_permission) {
      if (in_array($seo_admin_permission, $available_permissions)) {
        $seo_admin_available_permissions[] = $seo_admin_permission;
      }
    }
    $this->seoAdminUser = $this->drupalCreateUser($seo_admin_available_permissions, 'SEO Admin');

  }

  /**
   * Check Varbase SEO user role SEO Admin and permissions.
   */
  public function testCheckVarbaseSeoUserRoleSeoAdminAndPermissions() {

    // Given that the root super user was logged in to the site.
    $this->drupalLogin($this->rootUser);

    $this->drupalGet('admin/people/roles');
    $this->assertSession()->pageTextContains('Roles');
    $this->assertSession()->pageTextContains('SEO Admin');

    $this->drupalGet('admin/people/permissions');
    $this->assertSession()->pageTextContains('Roles');
    $this->assertSession()->pageTextContains('SEO Admin');

    $page = $this->getSession()->getPage();

    $seo_admin_permissions = $this->config('user.role.seo_admin')->get('permissions');
    $available_permissions = array_keys(\Drupal::service('user.permissions')->getPermissions());
    foreach ($seo_admin_permissions as $seo_admin_permission) {
      if (in_array($seo_admin_permission, $available_permissions)) {
        $seo_admin_permission = $page->findField('seo_admin[' . $seo_admin_permission . ']');
        $this->assertNotEmpty($seo_admin_permission);
        $this->assertTrue($seo_admin_permission->isChecked());
      }
    }
  }

  /**
   * Check Varbase SEO Fix 404 pages View and page.
   */
  public function testCheckVarbaseSeoFix404PagesViewAndPage() {

    // Given that SEO admin user was logged in to the site.
    $this->drupalLogin($this->seoAdminUser);

    // Check that No Last in the Fix 404 pages.
    $this->drupalGet('admin/config/search/redirect/404');
    $this->assertSession()->pageTextContains('Fix 404 pages');
    $this->assertSession()->pageTextNotContains('Last');
    $this->assertSession()->pageTextNotContains('First');

    // Hit 50 page not found.
    for ($no_webpage_number = 1; $no_webpage_number < 50; $no_webpage_number++) {
      $this->drupalGet('no-webpage' . (string) $no_webpage_number);
      $this->assertSession()->pageTextContains('Page not found');
    }

    // Check that still NO Last or First in the Fix 404 pages.
    $this->drupalGet('admin/config/search/redirect/404');
    $this->assertSession()->pageTextContains('Fix 404 pages');
    $this->assertSession()->pageTextContains('Last');
    $this->assertSession()->pageTextNotContains('First');

    // Hit more page not found.
    for ($no_webpage_number = 51; $no_webpage_number < 76; $no_webpage_number++) {
      $this->drupalGet('no-webpage' . (string) $no_webpage_number);
    }

    // Check that Last is in the Fix 404 pages.
    $this->drupalGet('admin/config/search/redirect/404');
    $this->assertSession()->pageTextContains('Fix 404 pages');
    $this->assertSession()->pageTextContains('Last');
    $this->assertSession()->pageTextNotContains('First');

  }

  /**
   * Check Varbase SEO XML Sitemap.
   */
  public function testCheckVarbaseSeoXmlSitemap() {

    // Given that SEO admin user was logged in to the site.
    $this->drupalLogin($this->seoAdminUser);

    $this->drupalGet('admin/config/search/simplesitemap');
    $this->assertSession()->pageTextContains('Simple XML Sitemap');
    $this->assertSession()->pageTextContains('Default');

    $this->drupalGet('admin/config/search/simplesitemap/settings');
    $this->assertSession()->pageTextContains('Simple XML Sitemap');
    $this->assertSession()->pageTextContains('Settings');

    $page = $this->getSession()->getPage();

    $cron_generate = $page->findField('cron_generate');
    $this->assertNotEmpty($cron_generate);
    $this->assertTrue($cron_generate->isChecked());

    $remove_duplicates = $page->findField('remove_duplicates');
    $this->assertNotEmpty($remove_duplicates);
    $this->assertTrue($remove_duplicates->isChecked());

    $skip_untranslated = $page->findField('skip_untranslated');
    $this->assertNotEmpty($skip_untranslated);
    $this->assertTrue($skip_untranslated->isChecked());

    $xsl = $page->findField('xsl');
    $this->assertNotEmpty($xsl);
    $this->assertTrue($xsl->isChecked());

  }

  /**
   * Check Varbase SEO Metatags.
   */
  public function testCheckVarbaseSeoMetatags() {

    // Given that SEO admin user was logged in to the site.
    $this->drupalLogin($this->seoAdminUser);

    $this->drupalGet('admin/config/search/metatag');
    $this->assertSession()->pageTextContains('Metatag');

    $this->assertSession()->pageTextContains('Global');
    $this->assertSession()->pageTextContains('Front page');
    $this->assertSession()->pageTextContains('403 access denied');
    $this->assertSession()->pageTextContains('404 page not found');
    $this->assertSession()->pageTextContains('Content');
    $this->assertSession()->pageTextContains('Content: Basic page');
    $this->assertSession()->pageTextContains('Taxonomy term');
    $this->assertSession()->pageTextContains('User');

  }

}
