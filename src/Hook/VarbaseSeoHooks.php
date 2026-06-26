<?php

namespace Drupal\varbase_seo\Hook;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\metatag\MetatagManagerInterface;
use Vardot\Entity\EntityDefinitionUpdateManager;
use Vardot\Installer\ModuleInstallerFactory;

/**
 * Object-oriented hook implementations for Varbase SEO.
 *
 * Drupal 11 replaces procedural hooks with methods that carry the #[Hook]
 * attribute (https://www.drupal.org/node/3442349). The real logic lives here
 * and uses dependency injection; the procedural functions in varbase_seo.module
 * are kept only as #[LegacyHook] shims delegating to this service.
 */
class VarbaseSeoHooks {

  use StringTranslationTrait;

  /**
   * Constructs a VarbaseSeoHooks object.
   *
   * @param \Drupal\metatag\MetatagManagerInterface $metatagManager
   *   The metatag manager.
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $classResolver
   *   The class resolver.
   */
  public function __construct(
    protected ?MetatagManagerInterface $metatagManager,
    protected ClassResolverInterface $classResolver,
  ) {}

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for node forms.
   */
  #[Hook('form_node_form_alter')]
  public function formNodeFormAlter(array &$form, FormStateInterface $form_state, $form_id): void {
    if (isset($form['field_yoast_seo']['widget']['0']['yoast_seo'])) {
      $form['field_yoast_seo']['widget']['0']['yoast_seo']['#title'] = $this->t('Real-time SEO analyzer');
    }
  }

  /**
   * Implements hook_form_BASE_FORM_ID_alter() for metatag_defaults_edit_form.
   */
  #[Hook('form_metatag_defaults_edit_form_alter')]
  public function formMetatagDefaultsEditFormAlter(array &$form, FormStateInterface $form_state, $form_id): void {
    if ($this->metatagManager === NULL) {
      return;
    }

    // Override Entity type / Group Mapping will be reflected on the Metatags
    // General form.
    $metatag_defaults = $form_state->getFormObject()->getEntity();
    $values = $metatag_defaults->get('tags');
    $form = $this->metatagManager->form($values, $form);

    // Move the Active checkbox to the bottom of the form.
    $status = $form['status'];
    unset($form['status']);
    $form['status'] = $status;
  }

  /**
   * Implements hook_modules_installed().
   */
  #[Hook('modules_installed')]
  public function modulesInstalled(array $modules): void {
    // When we enable the Google Analytics module we load the custom GA settings.
    if (in_array('google_analytics', $modules)) {
      $google_analytics_managed_optional_configs = [
        'google_analytics.settings',
      ];
      ModuleInstallerFactory::importConfigsFromList('varbase_seo', $google_analytics_managed_optional_configs, 'config/managed/google_analytics');

      // Entity updates to clear up any mismatched entity and/or field
      // definitions detected in the entity type and field definitions.
      $this->classResolver
        ->getInstanceFromDefinition(EntityDefinitionUpdateManager::class)
        ->applyUpdates();
    }
  }

  /**
   * Implements hook_pathauto_punctuation_chars_alter().
   */
  #[Hook('pathauto_punctuation_chars_alter')]
  public function pathautoPunctuationCharsAlter(array &$punctuation): void {
    $punctuation['copyright'] = ['value' => '©', 'name' => $this->t('Copyright symbol')];
    $punctuation['trademark'] = ['value' => '™', 'name' => $this->t('Trademark')];
    $punctuation['registered_trademark'] = ['value' => '®', 'name' => $this->t('Registered trademark')];
    $punctuation['left_double_quotation'] = ['value' => '”', 'name' => $this->t('Left double quotation')];
    $punctuation['right_double_quotation'] = ['value' => '“', 'name' => $this->t('Right double quotation')];
    $punctuation['en_dash'] = ['value' => '–', 'name' => $this->t('En dash')];
    $punctuation['em_dash'] = ['value' => '—', 'name' => $this->t('Em dash')];

    // Add the Arabic diacritics and special symbols.
    $punctuation['fatha'] = ['value' => 'َ', 'name' => $this->t('Fatha symbol')];
    $punctuation['damma'] = ['value' => 'ُ', 'name' => $this->t('Damma symbol')];
    $punctuation['ksrah'] = ['value' => 'ِ', 'name' => $this->t('Ksrah symbol')];
    $punctuation['tanween_fateh'] = ['value' => 'ًِ', 'name' => $this->t('Tanween fateh symbol')];
    $punctuation['tanween_dumm'] = ['value' => 'ٌِ', 'name' => $this->t('Tanween dumm symbol')];
    $punctuation['tanween_kser'] = ['value' => 'ٍِ', 'name' => $this->t('Tanween kser symbol')];
    $punctuation['shaddah'] = ['value' => 'ّ', 'name' => $this->t('Shaddah symbol')];
    $punctuation['sokon'] = ['value' => 'ْ', 'name' => $this->t('Sokoon symbol')];
    $punctuation['maddah'] = ['value' => 'ِ~', 'name' => $this->t('Maddah symbol')];
    $punctuation['tamdeed'] = ['value' => 'ِـ', 'name' => $this->t('Tamdeed symbol')];
    $punctuation['right_guillemet'] = ['value' => '»', 'name' => $this->t('Right Guillemet symbol')];
    $punctuation['left_guillemet'] = ['value' => '«', 'name' => $this->t('Left Guillemet symbol')];
    $punctuation['question_mark_rtl'] = ['value' => '؟', 'name' => $this->t('Question mark rtl')];
  }

}
