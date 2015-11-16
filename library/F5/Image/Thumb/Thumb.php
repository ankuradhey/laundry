<?php

require_once 'ThumbLib.inc.php';

class F5_Image_Thumb_Thumb
{           
    public function scale($file, $width, $height, $path, $ext = 'jpg', $delete_source = FALSE)
      {
        if(!is_numeric($width) || !is_numeric($height)) {
        	return FALSE;
        }        
        
        try {
            $thumb = PhpThumbFactory::create($file);
            $thumb->resize($width, $height);
            
            $thumb->save($path . '.' . $ext, $ext);
            
        } catch (Exception $e) {
            return FALSE;
        }

        if($delete_source) {
            @unlink($file);        	
        }
                            
        return TRUE;
      }

    public function resize($file, $width, $height, $path, $ext = 'jpg', $delete_source = FALSE)
      {
        if(!is_numeric($width) || !is_numeric($height)) {
            return FALSE;
        }        
        
        try {
            $thumb = PhpThumbFactory::create($file);
            $thumb->adaptiveResize($width, $height);
            
            $thumb->save($path . '.' . $ext, $ext);
            
        } catch (Exception $e) {
            return FALSE;
        }

        if($delete_source) {
            @unlink($file);         
        }          
          
        return TRUE;
      }
}