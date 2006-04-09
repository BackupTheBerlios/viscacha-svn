<?php
// Bases on Verification Word v2 by Huda M Elmatsani

class VeriWord {

    /* path to font directory*/
    var $dir_font     = './classes/fonts/';
    /* path to background image directory*/
    var $dir_noise     = "./classes/graphic/noises/";
    var $word         = "";
    var $wordarray     = array();
    var $im_width     = 0;
    var $im_height     = 0;
    var $im_type    = ""; //image type: jpeg, png
    var $sess_file     = './data/captcha.php';
    var $type         = 0;
    var $noises        = array();
    var $fonts         = array();
    var $filter        = 0;
    var $colortext  = false;
    
    function VeriWord() {
    
        // Get Font-Files
        $handle = opendir($this->dir_font);
        $pre = 'captcha_';
        while ($file = readdir($handle)) {
            if ($file != "." && $file != ".." && !is_dir($this->dir_font.$file)) {                      
                $nfo = pathinfo($this->dir_font.$file);
                $prefix = substr($nfo['basename'], 0, strlen($pre));
                if ($nfo['extension'] == 'ttf' && $prefix == $pre) {
                    $this->fonts[] = $nfo['basename'];
                }
            }
        }

        // Get Noise-Files
        $handle = opendir($this->dir_noise);
        while ($file = readdir($handle)) {
            if ($file != "." && $file != ".." && !is_dir($this->dir_noise.$file)) {                      
                $nfo = pathinfo($this->dir_noise.$file);
                if ($nfo['extension'] == 'jpg' || $nfo['extension'] == 'jpeg') {
                    $this->noises[] = $nfo['basename'];
                }
            }
        }
        
        srand((float)microtime()*time());
        mt_srand((double)microtime()*1000000); 
        
    }

    function set_filter ($filter) {
        $this->filter = (int) $filter;
    }
    
    function set_color ($ct) {
        if ($ct) {
            $this->colortext = true;
        }
        else {
            $this->colortext = false;
        }
    }

    function set_size ($w=200, $h=80) {
        if ($w > 1000 || $h > 1000) {
            $w=200;
            $h=80;
        }
        $this->im_width = $w;
        $this->im_height = $h;
    }

    function set_veriword($type=0) {
        if ($type == 0) {
            $this->word = $this->pick_number();
        }
        else {
            $this->word = $this->pick_word();
        }
        return $this->set_session();
    }

    function check_session($fid, $word) {
        $floods = file($this->sess_file);
        foreach ($floods as $row) {
            if (strlen($row) < 47) {
                continue;
            }
            $data = explode("\t",$row);
            if ($data[0] == $fid && strcasecmp($data[2], $word) == 0){
                return TRUE;
            }
        }
        return FALSE;
    }
    
    function set_session() {
    
        $fid = md5(microtime());
        $floods = array();
        $word = &$this->word;
        $floods = file($this->sess_file);
        $save = array();
        $limit = time()-60*60;
        
//        if (count($floods) > 1000) {
//            die('We can not accept registrations at the moment, because spam bots are attacking the script. Please try again in an hour!');
//        }
        
        foreach ($floods as $row) {
            if (strlen($row) < 47) {
                continue;
            }
            $row = trim($row);
            $data = explode("\t",$row);
            if ($data[1] > $limit){
                $save[] = $row;
            }
        }
        $save[] = $fid."\t".time()."\t".$word;
        
        file_put_contents($this->sess_file, implode("\n",$save));
        
        return $fid;
    }

    function output_word($fid) {

        $floods = file($this->sess_file);
        foreach ($floods as $row) {
            if (strlen($row) < 47) {
                continue;
            }
            $data = explode("\t",$row);
            if ($data[0] == $fid){
                $this->word = $data[2];
            }
        }

        $t = array();
        $t[] = array(
            array('    ###    ',' ########  ','  ######  ',' #######   ',' ######## ',' ######## ','  ######   ',' ##     ## ',' #### ','       ## ',' ##    ## ',' ##       ',' ##     ## ',' ##    ## ','   #####    ',' ########  ','  ########  ',' ########  ','  ######  ',' ######## ',' ##     ## ',' ##      ## ',' ##      ## ',' ##     ## ',' ##    ## ',' ######## '),
            array('   ## ##   ',' ##     ## ',' ##    ## ',' ##    ##  ',' ##       ',' ##       ',' ##     ## ',' ##     ## ','  ##  ','       ## ',' ##   ##  ',' ##       ',' ###   ### ',' ###   ## ','  ##    ##  ',' ##     ## ',' ##      ## ',' ##     ## ',' ##    ## ','   ##     ',' ##     ## ',' ##      ## ',' ##  ##  ## ','  ##   ##  ','  ##  ##  ','      ##  '),
            array('  ##   ##  ',' ##     ## ',' ##       ',' ##     ## ',' ##       ',' ##       ',' ##        ',' ##     ## ','  ##  ','       ## ',' ##  ##   ',' ##       ',' #### #### ',' ####  ## ',' ##      ## ',' ##     ## ',' ##      ## ',' ##     ## ',' ##       ','   ##     ',' ##     ## ',' ##      ## ',' ##  ##  ## ','   ## ##   ','   ####   ','     ##   '),
            array(' ##     ## ',' ########  ',' ##       ',' ##     ## ',' ######   ',' ######   ',' ##   #### ',' ######### ','  ##  ','       ## ',' #####    ',' ##       ',' ## ### ## ',' ## ## ## ',' ##      ## ',' ########  ',' ##      ## ',' ########  ','  ######  ','   ##     ',' ##     ## ',' ##      ## ',' ##  ##  ## ','    ###    ','    ##    ','    ##    '),
            array(' ######### ',' ##     ## ',' ##       ',' ##     ## ',' ##       ',' ##       ',' ##     ## ',' ##     ## ','  ##  ',' ##    ## ',' ##  ##   ',' ##       ',' ##     ## ',' ##  #### ',' ##      ## ',' ##        ',' ##  ##  ## ',' ##   ##   ','       ## ','   ##     ',' ##     ## ','  ##    ##  ',' ##  ##  ## ','   ## ##   ','    ##    ','   ##     '),
            array(' ##     ## ',' ##     ## ',' ##    ## ',' ##    ##  ',' ##       ',' ##       ',' ##     ## ',' ##     ## ','  ##  ',' ##    ## ',' ##   ##  ',' ##       ',' ##     ## ',' ##   ### ','  ##    ##  ',' ##        ',' ##    ##   ',' ##    ##  ',' ##    ## ','   ##     ',' ##     ## ','   ##  ##   ',' ##  ##  ## ','  ##   ##  ','    ##    ','  ##      '),
            array(' ##     ## ',' ########  ','  ######  ',' #######   ',' ######## ',' ##       ','  ######   ',' ##     ## ',' #### ','  ######  ',' ##    ## ',' ######## ',' ##     ## ',' ##    ## ','   #####    ',' ##        ','  #####  ## ',' ##     ## ','  ######  ','   ##     ','  #######  ','     ##     ','  ###  ###  ',' ##     ## ','    ##    ',' ######## ')
        );
        $t[] = array(
            array('    #    ',' ######  ','  #####  ',' #####   ',' ###### ',' ###### ','  #####  ',' #     # ',' ### ','       # ',' #    # ',' #      ',' #      # ',' #     # ','   #####   ',' ######  ','  ######  ',' ######  ','  #####  ',' ######### ',' #      # ',' #     # ',' #       # ',' #     # ',' #     # ',' ####### '),
            array('   # #   ',' #     # ',' #     # ',' #    #  ',' #      ',' #      ',' #     # ',' #     # ','  #  ','       # ',' #   #  ',' #      ',' ##    ## ',' ##    # ','  #     #  ',' #     # ',' #      # ',' #     # ',' #     # ','     #     ',' #      # ',' #     # ',' #       # ','  #   #  ','  #   #  ','      #  '),
            array('  #   #  ',' #     # ',' #       ',' #     # ',' #      ',' #      ',' #       ',' #     # ','  #  ','       # ',' #  #   ',' #      ',' # #  # # ',' # #   # ',' #       # ',' #     # ',' #      # ',' #     # ',' #       ','     #     ',' #      # ',' #     # ',' #       # ','   # #   ','   # #   ','     #   '),
            array(' #     # ',' ######  ',' #       ',' #     # ',' #####  ',' #####  ',' #  ###  ',' ####### ','  #  ','       # ',' ###    ',' #      ',' #  ##  # ',' #  #  # ',' #       # ',' ######  ',' #      # ',' ######  ','  #####  ','     #     ',' #      # ',' #     # ',' #   #   # ','    #    ','    #    ','    #    '),
            array(' ####### ',' #     # ',' #       ',' #     # ',' #      ',' #      ',' #     # ',' #     # ','  #  ',' #     # ',' #  #   ',' #      ',' #      # ',' #   # # ',' #       # ',' #       ',' #  #   # ',' #   #   ','       # ','     #     ',' #      # ','  #   #  ',' #   #   # ','   # #   ','    #    ','   #     '),
            array(' #     # ',' #     # ',' #     # ',' #    #  ',' #      ',' #      ',' #     # ',' #     # ','  #  ',' #     # ',' #   #  ',' #      ',' #      # ',' #    ## ','  #     #  ',' #       ',' #    #   ',' #    #  ',' #     # ','     #     ',' #      # ','   # #   ',' #  # #  # ','  #   #  ','    #    ','  #      '),
            array(' #     # ',' ######  ','  #####  ',' #####   ',' ###### ',' #      ','  #####  ',' #     # ',' ### ','  #####  ',' #    # ',' ###### ',' #      # ',' #     # ','   #####   ',' #       ','  ####  # ',' #     # ','  #####  ','     #     ','  ######  ','    #    ','  ##   ##  ',' #     # ','    #    ',' ####### ')
        );
        
        $set = array(
        'A' => 0,
        'B' => 1,
        'C' => 2,
        'D' => 3,
        'E' => 4,
        'F' => 5,
        'G' => 6,
        'H' => 7,
        'I' => 8,
        'J' => 9,
        'K' => 10,
        'L' => 11,
        'M' => 12,
        'N' => 13,
        'O' => 14,
        'P' => 15,
        'Q' => 16,
        'R' => 17,
        'S' => 18,
        'T' => 19,
        'U' => 20,
        'V' => 21,
        'W' => 22,
        'X' => 23,
        'Y' => 24,
        'Z' => 25
        );
    
        $r = array();
        foreach ($this->wordarray as $key => $v) {
            $r[$key] = rand(0,1);    
        }
        $text = '';
        for ($i = 0; $i < 7; $i++) {
            foreach ($this->wordarray as $key => $v) {
                $v = strtoupper($v);
                $text .= $t[$r[$key]][$i][$set[$v]];    
            }
            $text .= "<br>";
        }
    
        return str_replace(' ', '&nbsp;', $text);
    }

    function output_image($fid, $type='jpeg', $quality = 90) {
        $floods = file($this->sess_file);
        foreach ($floods as $row) {
            if (strlen($row) < 47) {
                continue;
            }
            $data = explode("\t",$row);
            if ($data[0] == $fid){
                $this->word = $data[2];
            }
        }

        /* make it not case sensitive*/
        $this->im_type = strtolower($type);

        /* check image type availability */
        $this->validate_type();

        /* draw the image  */    
        $this->draw_image();
        
        /* show the image  */            
        switch($this->im_type){
            case 'png' :
                header("Content-type: image/png");
                imagepng($this->im);
                imagedestroy($this->im);
              break;
            default:
                header("Content-type: image/jpeg");
                imagejpeg($this->im, '', $quality);
                imagedestroy($this->im);
              break;
        }
        exit;
    }

    function pick_number() {
        $newpass = ""; 
        $string="1324657890";

        for ($i=1; $i <= rand(5,6); $i++) { 
            $newpass .= substr($string, mt_rand(0,strlen($string)-1), 1); 
        } 
        return $newpass;
    }
    
    function pick_word() {
        $newpass = ""; 
        $string="ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        for ($i=1; $i <= 5; $i++) { 
            $a = substr($string, mt_rand(0,strlen($string)-1), 1); 
            $newpass .= $a;
            $this->wordarray[$i] = $a;
        } 
        return $newpass;
    }

    function get_font() {
        shuffle($this->fonts);
        $f = array_shift($this->fonts);
        if(file_exists($this->dir_font.$f)) {
            return $this->dir_font.$f;
        }
        else {
            return rand(1,5);
        }
    }

    function imagettftext($im, $size, $angle, $x, $y, $color, $font, $text, $spacing = 0) {
        $numchar = strlen($text);
        if (is_array($color) && count($color) < $numchar) {
            trigger_error('Not enough colors specified. '.count($color).' specified, but '.$numchar.' needed!', E_USER_ERROR);
        }
        $w = 0;
        for($i = 0; $i < $numchar; $i++) {
               $char = substr($text, $i, 1);
    
            if (is_array($color)) {
                $c = array_pop($color);
            }
            else {
                $c = $color;
            }
            imagettftext($im, $size, $angle, ($x + $w + ($i * $spacing)), $y, $c, $font, $char);
          
            $width = $this->imagettfbbox($size, $angle, $font, $char);
            $w = $w + $width[2];
        }
    }
    
    function imagettfbbox($size, $angle, $font, $text, $spacing = 0) {
        // Get the boundingbox from imagettfbbox(), which is correct when angle is 0
        $bbox = imagettfbbox($size, 0, $font, $text);
        if ($angle == 0) {
            return $bbox;
        }
    
        // Rotate the boundingbox
        $angle = pi() * 2 - $angle * pi() * 2 / 360;
        for ($i=0; $i<4; $i++) {
            $x = $bbox[$i * 2];
            $y = $bbox[$i * 2 + 1];
            $bbox[$i * 2] = cos($angle) * $x - sin($angle) * $y;
            $bbox[$i * 2 + 1] = sin($angle) * $x + cos($angle) * $y;
        }
        
        $bbox[2] += strlen($text)*$spacing;
        $bbox[4] += strlen($text)*$spacing;
    
        return $bbox;
    }
    
    function math_diff($x1, $x2) {
        $max = max($x1, $x2);
        $min = min($x1, $x2);
        $diff = $max-$min;
        if ($diff < 0) {
            $diff = $diff * (-1);
        }
        return $diff;
    }

    function draw_text() {

        /* pick one font type randomly from font directory */
        $text_font     = $this->get_font();
        /* angle for text inclination */
        $text_angle = rand(-15,15);

        /* numeric means built-in font */
        if(is_numeric($text_font)) {
            $text_width        = imagefontwidth($text_font) * strlen($this->word);
            $text_height     = imagefontheight($text_font);
            $margin            = $text_width * 0.25; 
            
            $im_string         = @imagecreatetruecolor( ceil($text_width + $margin), ceil($text_height + $margin) ); 
            if (!$im_string) { 
                $im_string     = imagecreate( ceil($text_width + $margin), ceil($text_height + $margin) ); // For older Versions 
            }
            
            $bg_color        = imagecolorallocate ($im_string, 255, 255, 255);
            $text_color        = imagecolorallocate ($im_string, 0, 0, 0);

            imagefill($im_string, 0, 0, $bg_color); 

            $text_x            = $margin/2;
            $text_y            = $margin/2;
    
            imagestring ( $im_string, $text_font, $text_x, $text_y, $this->word, $text_color );
        }
        else {
            $text_size  = $this->im_height;
            
            $spacing = rand(-3,3);
            
            $box         = $this->imagettfbbox( $text_size, $text_angle, $text_font, $this->word, $spacing);

            $text_width        = $this->math_diff($box[2], $box[0]);
            $text_height    = $this->math_diff($box[5], $box[3]);
            
            $margin        = ($text_width/strlen($this->word)) * 0.25;
            
            $im_string         = @imagecreatetruecolor( ceil($text_width + $margin), ceil($text_height + $margin) ); 
            if (!$im_string) { 
                $im_string     = imagecreate( ceil($text_width + $margin), ceil($text_height + $margin) ); // For older Versions 
            }

            $bg_color        = imagecolorallocate ($im_string, 255, 255, 255); 
            if ($this->colortext) {
                $text_color = array();
                $text_color[]    = imagecolorallocate ($im_string, rand(200,255), rand(0,50), rand(0,50));
                $text_color[]    = imagecolorallocate ($im_string, rand(0,50), rand(200,255), rand(0,50));
                $text_color[]    = imagecolorallocate ($im_string, rand(0,50), rand(0,50), rand(200,255));
                $text_color[]    = imagecolorallocate ($im_string, rand(0,50), rand(0,50), rand(0,50));
                $text_color[]    = imagecolorallocate ($im_string, rand(0,30), rand(100,130), rand(220,255));
                $text_color[]    = imagecolorallocate ($im_string, rand(220,255), rand(100,130), rand(0,30));
                shuffle($text_color);
            }
            else {
                $text_color        = imagecolorallocate ($im_string, 0, 0, 0);
            }

            imagefill($im_string, 0, 0, $bg_color); 
            
            /* draw text into canvas */
            $this->imagettftext    (    
                $im_string,
                ceil($text_size*0.9),
                $text_angle,
                ceil($margin/2), 
                ceil($margin/2)+$text_height,
                $text_color, 
                $text_font, 
                $this->word,
                $spacing
            );
        }

        if ($this->filter == 1) {
            $im_string = $this->wave($im_string, 3);
        }       

        $im_text         = @imagecreatetruecolor($this->im_width, $this->im_height); 
        if (!$im_text) { 
            $im_text     = imagecreate($this->im_width, $this->im_height); // For older Versions 
        }
        
        imagecolortransparent($im_string, $bg_color);

        $done = @imagecopyresampled ($im_text, 
                    $im_string, 
                    0, 0, 0, 0, 
                    $this->im_width, 
                    $this->im_height, 
                    ceil($text_width+$margin), 
                    ceil($text_height+$margin)
                );

           if (!$done) {
               imagecopyresized ($im_text, 
                       $im_string, 
                       0, 0, 0, 0, 
                       $this->im_width, 
                       $this->im_height, 
                       ceil($text_width+$margin), 
                       ceil($text_height+$margin)
               );
           }
           
        imagedestroy($im_string);

        return $im_text;
    }

    function get_noise() {
        /* pick one noise image randomly from image directory */
        shuffle($this->noises);
        $n = array_shift($this->noises);
        if(file_exists($this->dir_noise.$n)) {
            return $this->dir_noise.$n;
        }
        else {
            return FALSE;
        }
    }

    function draw_image() {
        
        /* get the noise image file*/
        $img_file         = $this->get_noise();

        if($img_file) {
            /* create "noise" background image from your image stock*/
            $im_noise     = @imagecreatefromjpeg ($img_file);
        } else {

            /* if fail to load image file, create it on the fly */
            $im_noise     = $this->draw_noise();
        }

         $noise_width     = imagesx($im_noise); 
        $noise_height     = imagesy($im_noise); 
        
        /* resize the background image to fit the size of image output */
        $this->im         = @imagecreatetruecolor($this->im_width,$this->im_height); 
        if (!$this->im) { 
            $this->im     = imageCreate($this->im_width,$this->im_height); // For older Versions 
        }
                            
        $done = @imagecopyresampled (    $this->im, 
                                        $im_noise, 
                                        0, 0, 0, 0, 
                                        $this->im_width, 
                                        $this->im_height, 
                                        $noise_width, 
                                        $noise_height);
        if (!$done) {
            imagecopyresized (    $this->im, 
                                $im_noise, 
                                0, 0, 0, 0, 
                                $this->im_width, 
                                $this->im_height, 
                                $noise_width, 
                                $noise_height);
        }
        /* put text image into background image */
        imagecopymerge (     $this->im, 
                            $this->draw_text(), 
                            0, 0, 0, 0, 
                            $this->im_width, 
                            $this->im_height, 
                            60 );

        return $this->im;
    }

    function draw_noise() {

        /* create "noise" background image*/
        $im_noise     = @imagecreate($this->im_width,$this->im_height); 
        $bg_color     = imagecolorallocate ($im_noise, 255, 255, 255);
        imagefill ( $im_noise, 0, 0, $bg_color );

        for($i=0; $i < $this->im_height; $i++) {
            $c = rand (0,255);
            $line_color        = imagecolorallocate ($im_noise, $c, $c, $c);
        }

        return $im_noise;
    }

    function validate_type() {
        /* check image type availability*/
        $is_available = FALSE;
        
        switch($this->im_type){
            case 'jpeg' :
            case 'jpg'     :
                if(function_exists("imagejpeg"))
                $is_available = TRUE;
                break;
            case 'png' :
                if(function_exists("imagepng"))
                $is_available = TRUE;
                break;
        }
        if(!$is_available && function_exists("imagejpeg")){
            /* if not available, cast image type to jpeg*/
            $this->im_type = "jpeg";
            return TRUE;
        }
        else if(!$is_available && !function_exists("imagejpeg")){
           die("No image support on this PHP server");         
        }
        else
            return TRUE;
    }
    
    /**
    * Apply a wave filter to an image
    *
    * @param    image    image            Image  to convert
    * @param    int        wave            Amount of wave to apply
    * @param    bool    randirection    Randomize direction of wave
    *
    * @return    image
    */
    function wave(&$image, $wave = 10, $randirection = true)
    {
        $image_width = imagesx($image);
        $image_height = imagesy($image);

        $temp = @imagecreatetruecolor($image_width, $image_height); 
        if (!$temp) { 
            $temp = imagecreate($image_width, $image_height); // For older Versions 
        }

        if ($randirection)
        {
            $direction = (mt_rand(0, 1) == 1) ? true : false;
        }

        $middlex = floor($image_width / 2);
        $middley = floor($image_height / 2);

        for ($x = 0; $x < $image_width; $x++)
        {
            for ($y = 0; $y < $image_height; $y++)
            {

                $xo = $wave * sin(2 * 3.1415 * $y / 128);
                $yo = $wave * cos(2 * 3.1415 * $x / 128);

                if ($direction)
                {
                    $newx = $x - $xo;
                    $newy = $y - $yo;
                }
                else
                {
                    $newx = $x + $xo;
                    $newy = $y + $yo;
                }

                if (($newx > 0 AND $newx < $image_width) AND ($newy > 0 AND $newy < $image_height))
                {
                    $index = imagecolorat($image, $newx, $newy);
                    $colors = imagecolorsforindex($image, $index);
                    $color = imagecolorresolve($temp, $colors['red'], $colors['green'], $colors['blue']);
                }
                else
                {
                    $color = imagecolorresolve($temp, 255, 255, 255);
                }

                imagesetpixel($temp, $x, $y, $color);
            }
        }

        return $temp;
    }
    
}
?>
