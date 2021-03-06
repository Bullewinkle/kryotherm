<?
session_start ();
                
                $im = @imagecreate (80, 20) or die ("Cannot initialize new GD image stream!");
                $bg = imagecolorallocate ($im, 232, 238, 247);
                $char = $_SESSION['code'];

                //создаём шум на фоне
                for ($i=0; $i<=128; $i++) {
                        $color = imagecolorallocate ($im, rand(0,255), rand(0,255), rand(0,255)); //задаём цвет
                        imagesetpixel($im, rand(2,80), rand(2,20), $color); //рисуем пиксель
                }

                //выводим символы кода
                for ($i = 0; $i < strlen($char); $i++) {
                        $color = imagecolorallocate ($im, rand(0,255), rand(0,128), rand(0,255)); //задаём цвет
                        $x = 5 + $i * 20;
                        $y = rand(1, 6);
                        imagechar ($im, 5, $x, $y, $char[$i], $color);
                }

                /*/упрощённый вариант
                $color = imagecolorallocate($img, 0, 0, 0);
                imagestring($im, 3, 5, 3, $char, $color);*/

                //антикеширование
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
                header("Cache-Control: no-store, no-cache, must-revalidate");
                header("Cache-Control: post-check=0, pre-check=0", false);
                header("Pragma: no-cache");

                //создание рисунка в зависимости от доступного формата
                if (function_exists("imagepng")) {
                   header("Content-type: image/png");
                   imagepng($im);
                } elseif (function_exists("imagegif")) {
                   header("Content-type: image/gif");
                   imagegif($im);
                } elseif (function_exists("imagejpeg")) {
                   header("Content-type: image/jpeg");
                   imagejpeg($im);
                } else {
                   die("No image support in this PHP server!");
                }
                imagedestroy ($im);
?>