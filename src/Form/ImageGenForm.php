<?php

namespace Drupal\image_gen\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\File\FileSystem;

/**
 * Defines ImageGenForm Classe.
 */
class ImageGenForm extends ConfigFormBase {

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

  /**
   * Constructs a ImageGenForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\File\FileSystem $file_system
   *   The file system service.
   */
  public function __construct(ConfigFactoryInterface $config_factory, FileSystem $file_system) {
    parent::__construct($config_factory);
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('file_system')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'image_gen.config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ImageGenForm';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Assigning the config 'image_gen.config' during buildForm.
    $imagegen_config = $this->config('image_gen.config');

    $width = $imagegen_config->get('width');
    $height = $imagegen_config->get('height');
    $bg = $imagegen_config->get('bg');
    $color = $imagegen_config->get('color');
    $image_field_replace = $imagegen_config->get('image_field_replace');
    $processed_text_image_replace = $imagegen_config->get('processed_text_image_replace');

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [       
      '#type' => 'submit',       
      '#value' => $this->t('Filtrer'),       
      '#button_type' => 'primary',       
      '#attributes' => [         
        'class' => ['btn btn-primary'],       
      ],     
    ];


    $form['actions']['preprocess'] = [
      '#type' => 'submit',
      '#value' => $this->t('Preprocess'),
      // No regular submit-handler. This form only works via JavaScript.
      '#submit' => [],
      '#ajax' => [
        'callback' => '::submitForm',
        'event' => 'click',
      ],
    ];


    $form['image_field_replace'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Replace missing rendered images style'),
      '#default_value' => $image_field_replace,
      '#return_value' => 1,
    ];

    $form['processed_text_image_replace'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Replace missing images in rendered text'),
      '#default_value' => $processed_text_image_replace,
      '#return_value' => 1,
    ];

    $form['width'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Width'),
      '#default_value' => $width,
    ];

    $form['height'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Height'),
      '#default_value' => $height,
    ];

    $form['type'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Extension'),
      '#default_value' => $imagegen_config->get('type'),
    ];

    // List all fonts files located in the 'ImageGen' module's directory.
    // Those files are used as "option" of the Select field below.
    $fonts_path = drupal_get_path('module', 'image_gen');
    $fonts_path = $this->fileSystem->realpath($fonts_path) . '/font';
    $files = $this->fileSystem->scanDirectory($fonts_path, '/.*/');

    // Instantiating an array.
    $options = [];

    foreach ($files as $file) {
      $options[$file->filename] = $file->filename;
    }

    $form['font'] = [
      '#type' => 'select',
      '#title' => $this->t('font'),
      // Give the array in select content.
      '#options' => $options,
      '#default_value' => $imagegen_config->get('font'),
    ];

    $form['bg'] = [
      '#type' => 'color',
      '#title' => $this->t('Background'),
      '#default_value' => $bg,
    ];

    $form['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $color,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];

    if ($imagegen_config->isNew()) {
      // If no config => Create an empty route.
      $url = Url::fromRoute('ImageGen.content',
        [],
        [
          'absolute' => TRUE,
          // Target _blank => another page opens when clicked.
          'attributes' => ['target' => '_blank'],
        ]
      );
    }
    else {
      // Create a URL from the route contained in the "ImageGen.content" block
      // (image_gen.routing.yml) with the parameters of the form '/ form / gen'.
      $url = Url::fromRoute('ImageGen.content',
        [
          'width' => $width,
          'height' => $height,
          'bg' => substr($bg, 1),
          'color' => substr($imagegen_config->get('color'), 1),
          'type' => $imagegen_config->get('type'),
          'text' => 'Image Generator',
        ],
        [
          'absolute' => TRUE,
          // Target _blank => another page opens when clicked.
          'attributes' => ['target' => '_blank'],
        ]
      );
    }




    // Link to the content of this URL.
    $link = Link::fromTextAndUrl($url->toString(), $url)->toString();

    $form['url'] = [
      '#type' => 'html_tag',
      '#tag' => 'p',
      '#value' => $link,
    ];

    $form['image'] = [
      '#type' => 'html_tag',
      '#tag' => 'img',
      '#value' => '',
      '#attributes' => [
        "src" => $url->toString(),
      ],
    ];

    return $form;
  }

  /**
   * Form validation handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Saving the form values ​​in the config 'image_gen.config'.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $this->config('image_gen.config')
      ->set('width', $form_state->getValue('width'))
      ->save();

    $this->config('image_gen.config')
      ->set('height', $form_state->getValue('height'))
      ->save();

    $this->config('image_gen.config')
      ->set('type', $form_state->getValue('type'))
      ->save();

    $this->config('image_gen.config')
      ->set('bg', $form_state->getValue('bg'))
      ->save();

    $this->config('image_gen.config')
      ->set('text', $form_state->getValue('text'))
      ->save();

    $this->config('image_gen.config')
      ->set('color', $form_state->getValue('color'))
      ->save();

    $this->config('image_gen.config')
      ->set('font', $form_state->getValue('font'))
      ->save();

    $this->config('image_gen.config')
      ->set('image_field_replace', $form_state->getValue('image_field_replace'))
      ->save();

    $this->config('image_gen.config')
      ->set('processed_text_image_replace', $form_state->getValue('processed_text_image_replace'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
