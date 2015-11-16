<?php

class F5_Utility_Codegenerator
{           
    public function codeGenerator($length=32)
      {
        $chars='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';        
        $string = '';
        
        for($i = 0; $i <= $length-1; $i++)
          { $string .= $chars[rand(0,strlen($chars)-1)];  }
                    
        return $string;
      }

}
