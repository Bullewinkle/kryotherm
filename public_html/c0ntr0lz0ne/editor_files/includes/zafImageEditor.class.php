<?php
if (!defined('IN_ZAF')) exit;
/*
GD 1 and 2 image manipulation class
Back-ported from WysiwygPro 3a 0.1
*/
class zafImageEditor {

        /* uses the GD library to re-size an image, maintaining aspect ratio
        returns false on failure, or the new dimensions on success.
        */
        function proportionalResize ($file, $outputfile='', $maxwidth=0, $maxheight=0, $jpgQuality=60) {

                if ( !file_exists( $file )) return false;

                $extension = strrchr(strtolower($file),'.');

                list ($origwidth, $origheight) = @getimagesize($file);

                if ((($origwidth > $maxwidth) || ($origheight > $maxheight)) || empty($outputfile) ) {

                        list ($new_w, $new_h) = $this->getProportionalSize($origwidth, $origheight, $maxwidth, $maxheight);

                        switch($extension) {
                                case '.jpg':
                                case '.jpeg':
                                        return $this->_imageResizeJpeg($file, $outputfile, $origwidth, $origheight, $new_w, $new_h, 0, 0, $jpgQuality);
                                        break;
                                case '.png':
                                        return $this->_imageResizePng($file, $outputfile, $origwidth, $origheight, $new_w, $new_h, 0, 0);
                                        break;
                                case '.gif':
                                        return $this->_imageResizeGif($file, $outputfile, $origwidth, $origheight, $new_w, $new_h, 0, 0);
                                        break;
                                default :
                                        return false;
                        }

                } else {
                        @copy($file, $outputfile);
                        return true;
                }
        }

        function getProportionalSize($origwidth, $origheight, $maxwidth=0, $maxheight=0) {
                if (empty($maxwidth)) {
                        $maxwidth = $origwidth;
                }
                if (empty($maxheight)) {
                        $maxheight = $origheight;
                }
                if (($origwidth > $maxwidth) || ($origheight > $maxheight)) {
                        if (($origwidth > $maxwidth) && ($origheight > $maxheight)) {
                                if ( ($origwidth/$maxwidth) > ($origheight/$maxwidth) ) {
                                        $newscale = $maxwidth / $origwidth;
                                } else {
                                        $newscale = $maxheight / $origheight;
                                }
                        } else if ( $origwidth > $maxwidth ) {
                                $newscale = $maxwidth / $origwidth;
                        } else {
                                $newscale = $maxheight / $origheight;
                        }

                        //calculate the new aspect ratio
                        $new_w = abs($origwidth * $newscale);
                        $new_h = abs($origheight * $newscale);
                        return array($new_w, $new_h);
                } else {
                        return array($origwidth, $origheight);
                }
        }

        ////////////////////////
        // create base image
        ///////////////////////

        function _imageCreateBase($width, $height) {
                if (function_exists('imagecreatetruecolor') && function_exists('imagecreate')) {
                        if ($base_image = @imagecreatetruecolor($width, $height)) {
                                return $base_image;
                        } else if ($base_image = @imagecreate($width, $height)) {
                                return $base_image;
                        }
                } else if (function_exists('imagecreate')) {
                        if ($base_image = @imagecreate($width, $height)) {
                                return $base_image;
                        }
                }
                return false;
        }

        ////////////////////////
        // Resize a jpeg image
        ///////////////////////

        function _imageResizeJpeg( $file, $output, $origwidth, $origheight, $width, $height, $cropX=0, $cropY=0, $quality=60 ) {

                if (!function_exists('imagejpeg')) return false;

                if (!(imagetypes() & IMG_JPG)) return false;

                //create the blank limited-palette image
                if (!$base_image = $this->_imageCreateBase($width, $height)) {
                        return false;
                }

                // get the image pointer to the original image
                $imageToResize = @imagecreatefromjpeg($file);

                if (function_exists('imagecopyresampled')) {
                        if (!@imagecopyresampled($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight)) {
                                @imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
                        }
                } else {
                        @imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
                }
                if (empty($output)) {
                        header("Content-type: image/jpeg", true);
                } else {
                        $fh=@fopen($output,'w');
                        @fclose($fh);
                }
                $return = false;
                //create the resized image
                if (@imagejpeg($base_image, $output, $quality)) {
                        $return = array($width, $height, $output);
                }
                @imagedestroy($base_image);
                @imagedestroy($imageToResize);

                return $return;

        }


        ////////////////////////
        // Resize a PNG image
        ///////////////////////

        function _imageResizePng( $file, $output, $origwidth, $origheight, $width, $height, $cropX=0, $cropY=0 ) {

                if (!function_exists('imagepng')) return false;

                if (!(imagetypes() & IMG_PNG)) return false;

                //create the blank limited-palette image
                if (!$base_image = $this->_imageCreateBase($width, $height)) {
                        return false;
                }

                // get the image pointer to the original image
                $imageToResize = @imagecreatefrompng($file);

                if (function_exists('imagecopyresampled')) {
                        if (!@imagecopyresampled($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight)) {
                                @imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
                        }
                } else {
                        @imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
                }
                $return = false;
                if (empty($output)) {
                        header("Content-type: image/x-png", true);
                        if (@imagepng($base_image)) {
                                $return = array($width, $height, $output);
                        }
                } else {
                        $fh=@fopen($output,'w');
                        @fclose($fh);
                        if (@imagepng($base_image, $output)) { //image destination
                                $return = array($width, $height, $output);
                        }
                }
                @imagedestroy($base_image);
                @imagedestroy($imageToResize);

                return $return;
        }

                ////////////////////////
        // Resize a GIF image
        ///////////////////////

        function _imageResizeGif( $file, $output, $origwidth, $origheight, $width, $height, $cropX=0, $cropY=0) {

                if (!function_exists('imagegif') && (!function_exists('imagecreatefromgif') || !function_exists('imagepng') ) ) return false;

                $extension = strrchr(strtolower($file),'.');

                //create the blank limited-palette image
                if (!$base_image = $this->_imageCreateBase($width, $height)) {
                        return false;
                }

                // get the image pointer to the original image
                $imageToResize = @imagecreatefromgif($file);

                if (function_exists('imagecopyresampled')) {
                        if (!@imagecopyresampled($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight)) {
                                @imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
                        }
                } else {
                        @imagecopyresized($base_image, $imageToResize, 0, 0, $cropX, $cropY, $width, $height, $origwidth, $origheight);
                }
                if (!function_exists('imagegif')) {
                        $outputFunction = 'imagepng';
                        $header = 'Content-type: image/x-png';
                        $output = str_replace($extension, '.png', $output);
                } else {
                        $outputFunction = 'imagegif';
                        $header = 'Content-type: image/gif';
                }

                $return = false;
                if (empty($output)) {
                        header($header, true);
                        if (@$outputFunction($base_image)) {
                                $return = array($width, $height, $output);
                        }
                } else {
                        $fh=@fopen($output,'w');
                        @fclose($fh);
                        if (@$outputFunction($base_image, $output)) { //image destination
                                $return = array($width, $height, $output);
                        }
                }
                @imagedestroy($base_image);
                @imagedestroy($imageToResize);

                return $return;
        }
}

?>