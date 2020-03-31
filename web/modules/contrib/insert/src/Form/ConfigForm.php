<?php

namespace Drupal\insert\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\filter\Entity\FilterFormat;

class ConfigForm extends ConfigFormBase {

  /**
   * @inheritdoc
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    parent::__construct($config_factory);
  }

  /**
   * @inheritdoc
   */
  public function getFormId() {
    return 'insert_config_form';
  }

  /**
   * @inheritdoc
   */
  protected function getEditableConfigNames() {
    return ['insert.config'];
  }

  /**
   * @inheritdoc
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('insert.config');

    $text_formats = array_map(function($format) {
      /** @var FilterFormat $format */
      return $format->label();
    }, filter_formats());

    $form['text_formats'] = [
      '#type' => 'checkboxes',
      '#options' => $text_formats,
      '#title' => $this->t('Automatic text format support'),
      '#description' => $this->t('Drupal core\'s HTML filter removes tags and attributes not explicitly white-listed from the output. This might strip tags and attributes generated by the Insert module. Enabling automatic text format support alters allowed HTML tags of the HTML filter when saving a text format configuration. Since managing the tags and attributes is prone to errors, enabling text format support here will ensure that required tags and attributes are always set. <strong>Note: After enabling text format support, you need to save the corresponding text format(s) at least once for the necessary tags to be added. When disabling support of a text format tags and attributes added to the format are not removed, they just do not get added automatically anymore when saving the text format(s).</strong>'),
      '#default_value' => $config->get('text_formats')
        ? $config->get('text_formats') : [],
    ];

    $form['css_classes'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Additional CSS classes'),
      '#description' => $this->t('CSS classes to be added to items inserted using the Insert module.'),
    ];

    $form['css_classes']['file'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Classes to be added to file links (File Insert widget)'),
      '#default_value' => $config->get('css_classes.file')
        ? $config->get('css_classes.file') : '',
      '#element_validate' => [[get_called_class(), 'validate_css_classes']],
    ];

    $form['css_classes']['image'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Classes to be added to images and image links (Image Insert widget)'),
      '#default_value' => $config->get('css_classes.image')
        ? $config->get('css_classes.image') : '',
      '#element_validate' => [[get_called_class(), 'validate_css_classes']],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * @inheritdoc
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('insert.config');

    $config->set('text_formats', array_keys(
      array_filter($form_state->getValue('text_formats'), function($value) {
        return !!$value;
      })
    ));
    $config->set('css_classes.file', $form_state->getValue('file'));
    $config->set('css_classes.image', $form_state->getValue('image'));

    $config->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * @param array $element
   * @param FormStateInterface $form_state
   * @param array $form
   */
  public static function validate_css_classes($element, &$form_state) {
    // Sanitize white-space.
    $segments = preg_split('/\s+/', trim($element['#value']));
    $form_state->setValueForElement($element, join(' ', $segments));
  }
}