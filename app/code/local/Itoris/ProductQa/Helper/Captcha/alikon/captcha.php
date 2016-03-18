<?php

class alikoncaptcha {

   var $codelength = 7;
   var $iwidth = 149;
   var $iheight = 34;
   var $chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ23456789';

    function captchacode() {

      $chars = $this->chars;

      $fullchar='';
      for ($i = 0; $i < $this->codelength; $i++) {
         $fullchar .= $chars[rand(0, strlen($chars) - 1)];
      }

       return $fullchar;

   }


   function image($rndstring) {

      //alikoncaptcha::captchacode();
      //$rndstring = $_SESSION['sfg']['sec_code'];
		//echo $GLOBALS['mosConfig_absolute_path'];die;
      $font= dirname(__FILE__) . '/Arial.ttf';
	  $_SESSION['captcha_code'] = $rndstring;
      /* output type */
      # $output_type='jpeg';
      $output_type='png';

      /* font size range, angle range, character padding */
      $min_font_size = 14;
      $max_font_size = 20;
      $min_angle = -20;
      $max_angle = 20;
      $char_padding = 1;

      /* initialize variables  */
      $turing_string='';
      $data = array();
      $image_width = $image_height = 0;

      /* build the data array of the characters, size, placement, etc. */
      for($i=0; $i<$this->codelength; $i++) {

         $char = substr($rndstring, $i, 1);

         $size = mt_rand($min_font_size, $max_font_size);
         $angle = mt_rand($min_angle, $max_angle);

         $bbox = imagettfbbox( $size, $angle, $font, $char );

         $char_width = max($bbox[2],$bbox[4]) - min($bbox[0],$bbox[6]);
         $char_height = max($bbox[1],$bbox[3]) - min($bbox[7],$bbox[5]);

         $image_width += $char_width + $char_padding;
         $image_height = max($image_height, $char_height);

         $data[] = array(
            'char'        => $char,
            'size'        => $size,
            'angle'       => $angle,
            'height'      => $char_height,
            'width'       => $char_width,
         );
      }

      /* calculate the final image size, adding some padding */
      $x_padding = 12;

/*      $image_width += ($x_padding * 1);
      $image_height = ($image_height * 1.5) + 2;*/
      $image_width = $this->iwidth;
      $image_height = $this->iheight;

      /* build the image, and allocte the colors  */
      $im = ImageCreate($image_width, $image_height);

      $r = 51 * mt_rand(4,5);
      $g = 51 * mt_rand(4,5);
      $b = 51 * mt_rand(4,5);
      $color_bg = ImageColorAllocate($im,  $r,  $g,  $b );

      $r = 51 * mt_rand(3,4);
      $g = 51 * mt_rand(3,4);
      $b = 51 * mt_rand(3,4);
      $color_line0 = ImageColorAllocate($im,  $r,  $g,  $b );

      $r = 51 * mt_rand(3,4);
      $g = 51 * mt_rand(3,4);
      $b = 51 * mt_rand(3,4);
      $color_line1    = ImageColorAllocate($im,  $r,  $g,  $b );

      $r = 51 * mt_rand(1,2);
      $g = 51 * mt_rand(1,2);
      $b = 51 * mt_rand(1,2);
      $color_text = ImageColorAllocate($im,  $r,  $g,  $b );

      $color_border = ImageColorAllocate($im,   0,   0,   0 );

      /* make the random background lines */
      for($l=0; $l<10; $l++) {

         $c = 'color_line' . ($l%2);
         $lx = mt_rand(0,$image_width+$image_height);
         $lw = mt_rand(0,3);
         if ($lx > $image_width) {
            $lx -= $image_width;
            ImageFilledRectangle($im, 0, $lx, $image_width-1, $lx+$lw, $$c );
         } else {
            ImageFilledRectangle($im, $lx, 0, $lx+$lw, $image_height-1, $$c );
         }

      }

      /* output each character */
      $pos_x = $x_padding + ($char_padding / 2);
      foreach($data as $d) {

         $pos_y = ( ( $image_height + $d['height'] ) / 2 );
         ImageTTFText($im, $d['size'], $d['angle'], $pos_x, $pos_y, $color_text, $font, $d['char'] );
         $pos_x += $d['width'] + $char_padding;
         $generatecode=$d['char'];

      }

      /* a nice border */
      ImageRectangle($im, 0, 0, $image_width-1, $image_height-1, $color_border);

      /* write it */
      @header('Content-type: image/png',true);
      ImagePNG($im); //,$GLOBALS['mosConfig_absolute_path'].'/components/com_sfg/plugins/captcha/alikon/tmp.png');

      /* free memory */
      ImageDEstroy($im);
   }



}

?>