<?php

namespace Drupal\image_gen\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Defines ImageGen class.
 */
class ImageGen extends ControllerBase {

  /**
   * Generate image.
   *
   * Ex : /image-gen/600/300/random/random/example/jpg,
   * Ex : /image-gen/600/300/FF2200/000000/example/jpg.
   *
   * @param int $width
   *   Image width.
   * @param int $height
   *   Image width.
   * @param string $bg
   *   Background color.
   * @param string $color
   *   Text color.
   * @param string $text
   *   Text content.
   * @param string $type
   *   Type of extension.
   */
  public function generateImage($width, $height, $bg, $color, $text, $type) {
    $config = $this->config('image_gen.config');

    /*
     * Handle the “width” parameter.
     */
    if (empty($width)) {
      $width = $config->get('width');
    }

    /*
     * Handle the “height” parameter.
     */
    if (empty($height)) {
      $height = $config->get('height');
    }

    /*
     * Handle the “type” parameter.
     */
    if (empty($type)) {
      $type = $config->get('type');
    }
    if (in_array(strtolower($type), ['png', 'gif', 'jpg', 'jpeg'])) {
      $type = strtolower($type);
    }

    /*
     * Handle the “text” parameter.
     */
    if (empty($text)) {
      $text = $width . "x" . $height;
    }

    if (strlen($text)) {
      $text = filter_var(trim($text), FILTER_SANITIZE_STRING);
    }
    $encoding = mb_detect_encoding($text, 'UTF-8, ISO-8859-1');
    if ($encoding !== 'UTF-8') {
      $text = mb_convert_encoding($text, 'UTF-8', $encoding);
    }
    $text = mb_encode_numericentity($text,
      [0x0, 0xffff, 0, 0xffff],
      'UTF-8');

    /*
     * Handle the “bg” parameter.
     */
    if (empty($bg)) {
      $bg = substr($config->get('bg'), 1);
    }
    if ($bg == 'random') {
      $bg = sprintf('%06X', mt_rand(0, 0xFFFFFF));
    }
    $bg = strtoupper($bg);
    if ((strlen($bg) === 6 || strlen($bg) === 3)) {
      if (strlen($bg) === 3) {
        $bg =
          strtoupper($bg[0] .
            $bg[0] .
            $bg[1] .
            $bg[1] .
            $bg[2] .
            $bg[2]);
      }
    }
    list($bgRed, $bgGreen, $bgBlue) = sscanf($bg, "%02x%02x%02x");

    /*
     * Handle the “color” parameter.
     */
    if (empty($color)) {
      $color = substr($config->get('color'), 1);
    }
    if ($color == 'random') {
      $color = sprintf('%06X', mt_rand(0, 0xFFFFFF));
    }
    $color = strtoupper($color);
    if ((strlen($color) === 6 || strlen($color) === 3)) {
      if (strlen($color) === 3) {
        $color =
          strtoupper($color[0] .
            $color[0] .
            $color[1] .
            $color[1] .
            $color[2] .
            $color[2]);
      }
    }
    list($colorRed, $colorGreen, $colorBlue) = sscanf($color, "%02x%02x%02x");

    $fontFile = realpath(drupal_get_path('module', 'image_gen')) . '/font/' . $config->get('font');

    if (!is_readable($fontFile)) {
      $fontFile = 'arial';
    }

    $fontSize = (int) round(($width - 50) / 8);
    if ($fontSize <= 9) {
      $fontSize = 9;
    }

    /*
     * Generate the image.
     */
    $image     = imagecreatetruecolor($width, $height);
    $colorFill = imagecolorallocate($image, $colorRed, $colorGreen, $colorBlue);
    $bgFill    = imagecolorallocate($image, $bgRed, $bgGreen, $bgBlue);
    imagefill($image, 0, 0, $bgFill);
    $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);

    while ($textBox[4] >= $width) {
      $fontSize -= round($fontSize / 2);
      $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
      if ($fontSize <= 9) {
        $fontSize = 9;
        break;
      }
    }
    $textWidth  = abs($textBox[4] - $textBox[0]);
    $textHeight = abs($textBox[5] - $textBox[1]);
    $textX      = ($width - $textWidth) / 2;
    $textY      = ($height + $textHeight) / 2;
    imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);

    $headers = [
      "Cache-Control" => "no-store, no-cache, must-revalidate, max-age=0",
      "Cache-Control" => "post-check=0, pre-check=0",
      "Pragma" => "no-cache",
    ];

    /*
     * Return the image and destroy it afterwards.
     */
    switch ($type) {
      case 'png':
        $headers["Content-type"] = 'image/png';
        $response = new StreamedResponse(
          function () use ($image) {
            imagepng($image);
          }, 200, $headers);
        break;

      case 'gif':
        $headers["Content-type"] = 'image/gif';
        $response = new StreamedResponse(
          function () use ($image) {
            imagegif($image);
          }, 200, $headers);
        break;

      case 'jpg':
      case 'jpeg':
        $response = new StreamedResponse(
          function () use ($image) {
            imagejpeg($image);
          }, 200, $headers);
        header('Content-Type: image/jpeg');
        break;
    }

    $response->send();

    return $response;

  }

}
