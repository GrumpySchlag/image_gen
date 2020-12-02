# Image Generator
 * Introduction
 * Requirements
 * Installation
 * Configuration
 
## INTRODUCTION

This module generates an image on the fly from parameters.

### How to create an image
This feature is available by two ways:

#### The URL
An image could be get from an url like:
```
site_path/image-gen/{width}/{height}/{bg}/{color}/{text}/{type}
```
Examples:
 * `site_path/image-gen/600/300/random/random/example/jpg`
 * `site_path/image-gen/600/300/FF2200/000000/default/jpg`
 * All parameters are optionnal.

#### CKEditor
On CKEditor toolbar, click on the ImageGen button,
fill the form to create an image.

### Behavior
 * If the text is equal to default, the image text displays its dimensions.
 * You can set the text & background colors to random.

### Links
 * For a full description of the module, visit the project page:
   https://www.drupal.org/project/image_gen

 * To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/image_gen
   
This feature is based on GD library.

## REQUIREMENTS
 * CKEditor commonly includes in your Drupal 8 or 9 installation.

## INSTALLATION
 * Classic installation as other modules. See the documentation:  
   https://www.drupal.org/docs/8/extending-drupal/installing-contributed-modules

## CONFIGURATION

### Image Generator
Go to Configuration » Development » ImageGen to configure the module.

There are default configuration options available:
 * Width
 * Height
 * File extension
 * Font
 * Background color
 * Text color

### CKEditor
To have the feature available in CKEditor,
the administrator needs to configure the text formats.
 * Go to Configuration » Content authoring » Text formats and editors
 * Add the ImageGen button to your toolbar.
