<?php

declare(strict_types=1);

namespace Drupal\varbase_seo\Hook;

use Drupal\Core\DependencyInjection\ClassResolverInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Vardot\Entity\EntityDefinitionUpdateManager;
use Vardot\Installer\ModuleInstallerFactory;

/**
 * Hook implementations for the Varbase SEO module.
 */
class VarbaseSeoHooks {

  use StringTranslationTrait;

  /**
   * Constructs a VarbaseSeoHooks object.
   *
   * @param \Drupal\Core\DependencyInjection\ClassResolverInterface $classResolver
   *   The class resolver.
   * @param object $metatagManager
   *   The Metatag manager.
   */
  public function __construct(
    protected ClassResolverInterface $classResolver,
    #[Autowire(service: 'metatag.manager')]
    protected $metatagManager,
  ) {}

  /**
   * Implements hook_form_BASE_FORM_ID_alter().
   *
   * Alter node form.
   */
  #[Hook('form_node_form_alter')]
  public function formNodeFormAlter(&$form, FormStateInterface $form_state, $form_id): void {
    if (isset($form['field_yoast_seo']) &&
        isset($form['field_yoast_seo']['widget']) &&
        isset($form['field_yoast_seo']['widget']['0']) &&
        isset($form['field_yoast_seo']['widget']['0']['yoast_seo'])) {
      $form['field_yoast_seo']['widget']['0']['yoast_seo']['#title'] = $this->t('Real-time SEO analyzer');
    }
  }

  /**
   * Implements hook_form_BASE_FORM_ID_alter().
   *
   * Alter Metatag defaults edit form.
   */
  #[Hook('form_metatag_defaults_edit_form_alter')]
  public function formMetatagDefaultsEditFormAlter(&$form, FormStateInterface $form_state, $form_id): void {

    // Override Entity type / Group Mapping will be reflected on
    // Metatags General form.
    $metatag_defaults = $form_state->getFormObject()->getEntity();
    $values = $metatag_defaults->get('tags');
    $form = $this->metatagManager->form($values, $form);

    // Move Active checkbox to the bottom of the form.
    $status = $form['status'];
    unset($form['status']);
    $form['status'] = $status;
  }

  /**
   * Implements hook_modules_installed().
   */
  #[Hook('modules_installed')]
  public function modulesInstalled($modules): void {
    // When we enable the Google Analytics module we load the custom GA settings.
    if (in_array('google_analytics', $modules)) {

      // Varbase SEO custom google analytics config settings.
      $google_analytics_managed_optional_configs = [
        'google_analytics.settings',
      ];
      ModuleInstallerFactory::importConfigsFromList('varbase_seo', $google_analytics_managed_optional_configs, 'config/managed/google_analytics');

      // Entity updates to clear up any mismatched entity and/or field
      // definitions and fix changes were detected in the entity type and field
      // definitions.
      $this->classResolver
        ->getInstanceFromDefinition(EntityDefinitionUpdateManager::class)
        ->applyUpdates();

    }
  }

  /**
   * Implements hook_pathauto_punctuation_chars_alter().
   *
   * Alter the list of punctuation characters for Pathauto control.
   */
  #[Hook('pathauto_punctuation_chars_alter')]
  public function pathautoPunctuationCharsAlter(array &$punctuation): void {
    $punctuation['copyright'] = [
      'value' => '©',
      'name' => $this->t('Copyright symbol'),
    ];
    $punctuation['trademark'] = [
      'value' => '™',
      'name' => $this->t('Trademark'),
    ];
    $punctuation['registered_trademark'] = [
      'value' => '®',
      'name' => $this->t('Registered trademark'),
    ];
    $punctuation['left_double_quotation'] = [
      'value' => '”',
      'name' => $this->t('Left double quotation'),
    ];
    $punctuation['right_double_quotation'] = [
      'value' => '“',
      'name' => $this->t('Right double quotation'),
    ];
    $punctuation['en_dash'] = [
      'value' => '–',
      'name' => $this->t('En dash'),
    ];
    $punctuation['em_dash'] = [
      'value' => '—',
      'name' => $this->t('Em dash'),
    ];

    // Add the Arabic diacritics and special symbols.
    $punctuation['fatha'] = [
      'value' => 'َ',
      'name' => $this->t('Fatha symbol'),
    ];
    $punctuation['damma'] = [
      'value' => 'ُ',
      'name' => $this->t('Damma symbol'),
    ];
    $punctuation['ksrah'] = [
      'value' => 'ِ',
      'name' => $this->t('Ksrah symbol'),
    ];
    $punctuation['tanween_fateh'] = [
      'value' => 'ًِ',
      'name' => $this->t('Tanween fateh symbol'),
    ];
    $punctuation['tanween_dumm'] = [
      'value' => 'ٌِ',
      'name' => $this->t('Tanween dumm symbol'),
    ];
    $punctuation['tanween_kser'] = [
      'value' => 'ٍِ',
      'name' => $this->t('Tanween kser symbol'),
    ];
    $punctuation['shaddah'] = [
      'value' => 'ّ',
      'name' => $this->t('Shaddah symbol'),
    ];
    $punctuation['sokon'] = [
      'value' => 'ْ',
      'name' => $this->t('Sokoon symbol'),
    ];
    $punctuation['maddah'] = [
      'value' => 'ِ~',
      'name' => $this->t('Maddah symbol'),
    ];
    $punctuation['tamdeed'] = [
      'value' => 'ِـ',
      'name' => $this->t('Tamdeed symbol'),
    ];
    $punctuation['right_guillemet'] = [
      'value' => '»',
      'name' => $this->t('Right Guillemet symbol'),
    ];
    $punctuation['left_guillemet'] = [
      'value' => '«',
      'name' => $this->t('Left Guillemet symbol'),
    ];
    $punctuation['question_mark_rtl'] = [
      'value' => '؟',
      'name' => $this->t('Question mark rtl'),
    ];
  }

}
