<?php

namespace Drupal\Tests\varbase_seo\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\script_manager\Entity\Script;
use Drupal\script_manager\Entity\ScriptInterface;

/**
 * Tests Varbase SEO test the Javascript Snippets functionality.
 *
 * @group varbase_seo
 */
class VarbaseSeoJavascriptSnippetsTest extends WebDriverTestBase {

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
   * Test script.
   *
   * @var \Drupal\script_manager\Entity\Script
   */
  protected $testScript;


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
   * Check Varbase SEO Javascript Snippets Functionality.
   *
   * After integration with number of modules. Enabled under the same system.
   */
  public function testCheckVarbaseSeoJavascriptSnippetsFunctionality() {

    // Given that SEO admin user was logged in to the site.
    $this->drupalLogin($this->seoAdminUser);

    // Navigate to the Scripts admin page.
    $this->drupalGet('admin/structure/scripts');
    $this->assertSession()->pageTextContains('Scripts');
    $this->assertSession()->pageTextContains('There are no script entities yet.');

    // Add Script.
    $this->drupalGet('admin/structure/scripts/add');
    $this->assertSession()->pageTextContains('Add script');

    $this->testScript = Script::create([
      'id' => 'test_script',
      'label' => $this->t('Test Script'),
      'snippet' => '<script> document.getElementById("main-wrapper").innerHTML = "Hello JavaScript!"; </script>',
      'position' => ScriptInterface::POSITION_BOTTOM,
    ]);
    $this->testScript->save();

    $this->testScript->set('visibility', [
      'request_path' => [
        'id' => 'request_path',
        'pages' => '<front>',
        'negate' => FALSE,
        'context_mapping' => [],
      ],
    ])->save();

    // Navigate to the Scripts admin page.
    $this->drupalGet('admin/structure/scripts');
    $this->assertSession()->pageTextContains('Scripts');
    $this->assertSession()->pageTextContains('Test Script');

    // Got to the homepage.
    $this->drupalGet('<front>');
    $this->assertSession()->pageTextContains('Hello JavaScript!');

    // Got to the use page.
    $this->drupalGet('/user');
    $this->assertSession()->pageTextNotContains('Hello JavaScript!');

  }

}
