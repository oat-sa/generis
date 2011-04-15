<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* static functions to generate buttons
* @author patrick
* @package usergui
*/
function getPane($v)
{$height = 15+strlen($v[1])*6;
$center = imagecreate(17,$height);
$black = ImageColorAllocate ($center, 0, 0, 0);
$white = ImageColorAllocate ($center, 255, 255, 255);
imagefill($center, 0, 0, $white);
imagecolortransparent($center,$white);
error_reporting(0);
ImageTTFText($center,10,90,14, $height, $black, "verdana.ttf", $v[1]);
imagepng($center,"./icons/genfiles/".$v[1].".png"); 
}
 function getButtonimage($text,$seq=false)
		{
			#E6EBF2
		//echo "SEQ VAUT pour ".$text." :";
		//echo $seq;
		//echo "<br>";
		
		error_reporting(E_ALL);
		$size=strlen($text)*5.6+25;
		
		
		
		
		
		$where = dirname(__FILE__)."/icons/genfiles/".md5($text).".jpg";
		//echo $where;
		//$where = "./icons/genfiles/".md5($text).".jpg";

		if (!file_exists($where)) { 
			$im = imagecreatefromjpeg(dirname(__FILE__)."/icons/ButtonEmpty.jpg");
		$separator= imagecreatefrompng(dirname(__FILE__)."/icons/Separateur.png");
		
		if ($seq==false)
			{
		imagecopyresized($im,$separator, 0, 0, 0, 0, 1, 17, 1, 17);
		$start=8;
			}
		else {
			$start=0;$size=$size-8;
			}
			error_reporting(0);
		$black = ImageColorAllocate ($im, 0, 0, 0);
		ImageTTFText($im, 8, 0, $start, 9, $black, "verdana.ttf", $text);
		
		imagecopyresized($im,$separator, $size, 0, 0, 0, 1, 17, 1, 17); 
		$return = imagecreate ( $size+2 , 17 ) ;
		imagecopy ($return, $im , 0, 0 , 0 , 0 , $size+2 , 17) ;

		imagepng($return,$where); 
                // }  moi je la verrai bien l�
		}
		return $where;
		}
?>