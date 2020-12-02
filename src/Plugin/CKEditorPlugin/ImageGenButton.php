<?php

namespace Drupal\image_gen\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;
use Drupal\Core\Url;

/**
 * Defines the "wysiwyg_imagegen" plugin.
 *
 * @CKEditorPlugin(
 *   id = "ImageGen",
 *   label = @Translation("CKEditor Image Gen Button")
 * )
 */
class ImageGenButton extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface {

  /**
   * Get path to library folder.
   */
  public function getLibraryPath() {
    $module_handler = \Drupal::service('module_handler');
    $module_path = $module_handler->getModule('image_gen')->getPath();
    $path = $module_path . '/libraries';
    return $path;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {

    // Récupération de la config grâce à la méthode statique '::config'.
    $imagegen_config = \Drupal::config('image_gen.config');

    $width = $imagegen_config->get('width');
    $height = $imagegen_config->get('height');
    $text = $imagegen_config->get('text');
    $bg = $imagegen_config->get('bg');
    $color = $imagegen_config->get('color');

    $url = Url::fromRoute('ImageGen.content',
      [],
      [
        'absolute' => TRUE,
      ]
    );

    $config = [
      'ImageGenButton_width' => $width,
      'ImageGenButton_height' => $height,
      'ImageGenButton_text' => $text,
      'ImageGenButton_bg' => $bg,
      'ImageGenButton_color' => $color,
      'ImageGenButton_url' => $url->toString(),
    ];

    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $path = $this->getLibraryPath();
    return [
      'ImageGenButton' => [
        'label' => $this->t('ImageGen'),
        'image' => $path . '/icons/ImageGenIcon.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return $this->getLibraryPath() . '/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    return [];
  }

}
