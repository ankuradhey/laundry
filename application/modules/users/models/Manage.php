<?php
class Users_Model_Manage extends Zend_Db_Table
{
   protected $_name = 'user_events';
 
     public function getuserallImage($user_id)      
     {     
         $db    = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);
        //$sql    = $db->select()->from('photo')->where('userid = ? ', $user_id)->where->('img_type=?','');   
        $sql    = "SELECT * from photo where userid = $user_id AND img_type ='' "; 
        $result = $db->fetchAll($sql);          
        return $result;
    
     }
     
 public function deleteImageByID($id) 
  {     
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "DELETE FROM photo WHERE id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;
    }
    
  public function deleteslicephID($id) 
  {     
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "DELETE FROM slice_picture WHERE id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;
    }  
  public function deleteimgwall($id)
  {
    $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "DELETE FROM latest_slice  WHERE photo_id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;  
  }
    
  public function getusermainImage($id)
  {
     $db    = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = $db->select()->from('photo')->where('id = ? ', $id);                  
        $result = $db->fetchRow($sql);          
        return $result;  
  }
  
  public function updateanywhere($mytable, array $data, $where)
     {
		 //echo $where;die;
      $db = Zend_Db_Table::getDefaultAdapter();
      $db->update($mytable,$data,$where);
      return true;
     }
     
   public function getimageinfo($id,$user_id,$table)
   {
        $db     = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "SELECT ph.*,us.user_id,us.fname,us.lname,us.display_name,us.image_name,us.extension FROM $table as ph, user as us WHERE ph.userid=us.user_id AND ph.id='$id'  AND ph.img_type='' ";                  
        $result = $db->fetchRow($sql);          
        return $result;        
   }
   
   public function getmultimginfo($id,$current_img_id,$user_id,$table)
   {
       $parent_id  = ($table == 'slice_picture' ) ? 'photo_id' : 'gifs_id';
        $db     = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "SELECT ph.*,us.user_id,us.fname,us.lname,us.display_name,us.image_name,us.extension FROM $table as ph, user as us WHERE ph.user_id=us.user_id AND ph.id='$current_img_id' AND ph.$parent_id='$id' ";                  
        $result = $db->fetchRow($sql);          
        return $result;        
   }
   
   
  public function getletestslice($id,$user_id,$sliceparam)
   {
        $db     = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);
         $sql    = "SELECT ls.*,us.user_id,us.fname,us.lname,us.display_name,us.image_name,us.extension FROM latest_slice as ls, user as us WHERE ls.userid=us.user_id AND ls.$sliceparam='$id'  ";                  
        $result = $db->fetchRow($sql);          
        return $result;        
   } 

 public function getimagecomments($photo_id,$userid,$sliceparam)
 {
   
    $firstDayOfMonth = date('Y-m-d H:i:s', mktime(0,0,0,date('n'),1,date('Y')));  
    $db     = Zend_Db_Table::getDefaultAdapter();
    $sql    = "SELECT cps.*,u.user_id,u.display_name,u.image_name,u.extension FROM comment_profile_share cps,user u WHERE cps.userid = u.user_id AND cps.$sliceparam='$photo_id' ORDER BY cps.postdate DESC LIMIT 10 ";                  
    $result = $db->fetchAll($sql);          
    return $result;   
    
 }  
 public function getMultimgcomments($photo_id,$current_img_id,$userid,$sliceparam)
 {
    $parent_id  = ($sliceparam == 'multigif_id' ) ? 'gifs_id' : 'photo_id';     
    $firstDayOfMonth = date('Y-m-d H:i:s', mktime(0,0,0,date('n'),1,date('Y')));
    $db     = Zend_Db_Table::getDefaultAdapter();
    $sql    = "SELECT cps.*,u.user_id,u.display_name,u.image_name,u.extension FROM comment_profile_share cps,user u WHERE cps.userid = u.user_id AND cps.$sliceparam='$current_img_id' ORDER BY cps.postdate DESC LIMIT 10 ";                  
    $result = $db->fetchAll($sql);          
    return $result;   
    
 }  
 
 
 public function getnextdata($id,$userid,$table)
 {   
    $index=0; 
    $db    = Zend_Db_Table::getDefaultAdapter();                
    $sql    = "SELECT `id` FROM `$table` WHERE `id` > '{$id}' AND userid='$userid' AND img_type='' ORDER BY `id` ASC LIMIT 1";                  
    $result = $db->fetchAll($sql);      
    if(count($result)>0)
    return $result[$index]->id;   
     else 
         return false;
 }
 public function getpreviousdata($id,$userid,$table)
 {   
    $index  = 0;  
    $db     = Zend_Db_Table::getDefaultAdapter();            
    $sql    = "SELECT `id` FROM `$table` WHERE `id` < '{$id}' AND userid='$userid' AND img_type='' ORDER BY `id` DESC LIMIT 1";                  
    $result = $db->fetchAll($sql);
    if(count($result)>0)
    return $result[$index]->id;   
     else
         return false;
 }
 
 public function getnextmultidata($id,$main_image_id,$userid,$table)
 {   
     $filed_Na = ($table=='slice_gifs')?'gifs_id':'photo_id';
    $index=0; 
    $db    = Zend_Db_Table::getDefaultAdapter();                
    $sql    = "SELECT `id` FROM `$table` WHERE `id` > '{$main_image_id}' AND user_id='$userid' AND $filed_Na='$id' ORDER BY `id` ASC LIMIT 1";                  
    $result = $db->fetchAll($sql);      
    if(count($result)>0)
    return $result[$index]->id;   
     else 
         return false;
 }
 public function getpreviousmultidata($id,$main_image_id,$userid,$table)
 {   
    $filed_Na = ($table=='slice_gifs')?'gifs_id':'photo_id'; 
    $index  = 0;  
    $db     = Zend_Db_Table::getDefaultAdapter();            
    $sql    = "SELECT `id` FROM `$table` WHERE `id` < '{$main_image_id}' AND user_id='$userid' AND $filed_Na='$id' ORDER BY `id` DESC LIMIT 1";                  
    $result = $db->fetchAll($sql);
    if(count($result)>0)
    return $result[$index]->id;   
     else
         return false;
 }
 
 public function getupdated($photo_id,$sliceparam)
 {     
   $db     = Zend_Db_Table::getDefaultAdapter();
   $sql    = "SELECT cps.*,u.user_id,u.display_name,u.image_name,u.extension FROM comment_profile_share cps,user u WHERE cps.userid = u.user_id AND cps.$sliceparam='$photo_id' ORDER BY cps.postdate DESC LIMIT 10 ";                  
   $result = $db->fetchAll($sql);          
   return $result;    
 
 } 
 
 public function getmultiupdated($photo_id,$sliceparam)
 {     
   $db     = Zend_Db_Table::getDefaultAdapter();
   $sql    = "SELECT cps.*,u.user_id,u.display_name,u.image_name,u.extension FROM comment_profile_share cps,user u WHERE cps.userid = u.user_id AND cps.$sliceparam='$photo_id' ORDER BY cps.postdate DESC LIMIT 10 ";                  
   $result = $db->fetchAll($sql);          
   return $result;    
 
 } 
 
public function getuserallvideo($user_id)
  {
     $db    = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = $db->select()->from('video')->where('	userid = ? ', $user_id);                  
        $result = $db->fetchAll($sql);          
        return $result;  
  }   
 public function getwall_id($id,$sliceparam)
  {
     $db = Zend_Db_Table::getDefaultAdapter();
     $sql = "SELECT id FROM latest_slice where $sliceparam='$id' ";            
     $result = $db->fetchROW($sql);
    return $result['id'];  
  } 
  
public function deletecomment($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $sql = "DELETE  FROM comment_profile_share WHERE id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;
    }  
    
public function getmemberUser($mem_id)
	{
            $_name  = 'user';
            $db     = Zend_Db_Table::getDefaultAdapter();
			$db->setFetchMode(Zend_Db::FETCH_OBJ);
			
            $sql    = $db->select()->from($_name)
                    ->where('status = ? ', 1)
                    ->where('user_id = ? ', $mem_id);
					
            $result = $db->fetchRow($sql);          
            return $result;
	   }    
  
 public function getalbuminfo($id,$user_id)
   {
        $db    = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "SELECT ap.*,us.user_id,us.fname,us.lname,us.display_name,us.image_name,us.extension FROM album_photo as ap,user as us WHERE us.user_id=ap.userid AND ap.id='$id'  ";                  
        $result = $db->fetchRow($sql);          
        return $result;        
   }
   
 public function getalbumslice($id,$user_id)
 {
       $db     = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "SELECT ls.*,us.user_id,us.fname,us.lname,us.display_name,us.image_name,us.extension FROM latest_slice as ls, user as us WHERE ls.userid=us.user_id AND ls.album_id='$id' AND ls.userid='$user_id' ";                  
        $result = $db->fetchRow($sql);          
        return $result; 
 }

 public function getalbumcomments($album_id,$userid)
 {
   $firstDayOfMonth = date('Y-m-d H:i:s', mktime(0,0,0,date('n'),1,date('Y')));  
    $db     = Zend_Db_Table::getDefaultAdapter();
     $sql   = "SELECT cps.*,u.user_id,u.display_name,u.image_name,u.extension FROM comment_profile_share cps,user u WHERE cps.userid = u.user_id AND cps.albumphoto_id='$album_id' ORDER BY cps.postdate DESC LIMIT 10 ";                  
    $result = $db->fetchAll($sql);          
    return $result;   
    
 } 
 
 public function getnextAlbumdata($id,$userid)
 {   
    $index=0; 
    $db    = Zend_Db_Table::getDefaultAdapter();                
    $sql    = "SELECT `id` FROM `album_photo` WHERE `id` > '{$id}' AND userid='$userid' ORDER BY `id` ASC LIMIT 1";                  
    $result = $db->fetchAll($sql);      
    if(count($result)>0)
    return $result[$index]->id;   
     else 
         return false;
 }
 public function getpreviousAlbumdata($id,$userid)
 {   
    $index  = 0;  
    $db     = Zend_Db_Table::getDefaultAdapter();            
    $sql    = "SELECT `id` FROM `album_photo` WHERE `id` < '{$id}' AND userid='$userid' ORDER BY `id` DESC LIMIT 1";                  
    $result = $db->fetchAll($sql);
    if(count($result)>0)
    return $result[$index]->id;   
     else
         return false;
 }
 
  public function getalbumwallByid($id)
  {
     $db = Zend_Db_Table::getDefaultAdapter();
    echo  $sql = "SELECT id FROM latest_slice where albumphoto_id ='$id' ";             
     $result = $db->fetchROW($sql);
    return $result['id'];  
  } 
  
  public function getAlbumupdated($album_id)
 {     
   $db     = Zend_Db_Table::getDefaultAdapter();
   $sql    = "SELECT cps.*,u.user_id,u.display_name,u.image_name,u.extension FROM comment_profile_share cps,user u WHERE cps.userid = u.user_id AND cps.albumphoto_id='$album_id' ORDER BY cps.postdate DESC LIMIT 10 ";                  
   $result = $db->fetchAll($sql);          
   return $result;    
 
 } 
 
 public function deletealbumcomment($id) 
  {     
    $db      = Zend_Db_Table::getDefaultAdapter();
    $sql     = "DELETE  FROM comment_profile_share WHERE id='".$id."'";
    $result  = $this->_db->query($sql);
    return $result;    
  }  
 
  
  public function getuserprofileF($uid) 
   {
        $db      = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql     = "SELECT userid FROM `user_profile_field` WHERE userid='$uid'";
        $result = $db->fetchRow($sql);        
        $ret = (!$result->userid) ? 'notok':'ok';
        return $ret;   
   }
   
   public function findAllLateshslice($user_id,$email) 
   {    
    $db     = Zend_Db_Table::getDefaultAdapter();
    $sql    = "Select usr . * , fs . * , cps . * from user as us, friends as fs,comment_profile_share as cps,user as usr WHERE us.email= fs.myemail AND usr.user_id=cps.userid AND us.user_id='$user_id' AND usr.email = fs.useremail AND us.email = '$email' ORDER BY cps.postdate DESC LIMIT 0 , 5 ";
   $result  = $db->fetchAll($sql); 
    
    return $result;   
  } 
  
  public function finduserSlices($user_id,$email) 
   {    
      $db     = Zend_Db_Table::getDefaultAdapter();    
      $sql1   = "SELECT usr.*,fs .* FROM  user as us, friends as fs,user as usr WHERE us.email= fs.myemail AND us.user_id='$user_id' AND usr.email = fs.useremail AND us.email = '$email' ";
      $result = $db->fetchAll($sql1); //echo "<pre />"; print_r($result);
     foreach($result as $users)
     {         
       $user_ides[]       = $users->user_id;
       $email_pr[]        = $users->myemail;
       $friend_emaiil[]   = $users->useremail;
     } 
     $user_ide     = implode(",",$user_ides);
     $frends_email = implode("','",$friend_emaiil);
      
      $comntSql    = "SELECT ur.user_id,ur.email,ur.display_name ,cps.* FROM comment_profile_share as cps, user as ur WHERE cps.userid IN ('$user_ide') AND cps.userid=ur.user_id order by cps.postdate ";      
      $cummentARR  = $db->fetchAll($comntSql);
      
      $activity    = $this->Activitydate($user_id,$email);
      $getpostdate = date('Y-m-d',strtotime($activity->postdate));
      
      $fri_sql     = "SELECT * FROM friends  WHERE myemail IN('$frends_email') AND created_at='$getpostdate' ORDER BY created_at DESC";      
      $frndSql     = $db->fetchAll($fri_sql);
      
     $detail =  array_merge($cummentARR,$frndSql);//echo "<pre />";print_r($detail);
      return $detail;   
  } 
  
   public function Activitydate($user_id,$email) 
   {    
    $db      = Zend_Db_Table::getDefaultAdapter();
    $sql     = "Select  cps . * from user as us, friends as fs,comment_profile_share as cps WHERE us.email= fs.myemail AND us.user_id=cps.userid AND us.email = '$email' ORDER BY cps.postdate DESC LIMIT 0 , 1 ";
    $result = $db->fetchRow($sql);          
    return $result;   
  }
  
  public function beforeActivitydate($userid,$email)
  {
   $db      = Zend_Db_Table::getDefaultAdapter();
   $sql     = "Select  cps . * from user as us, friends as fs,comment_profile_share as cps WHERE us.email= fs.myemail AND us.user_id=cps.userid AND us.email = '$email' AND cps.postdate>= DATE(NOW()) - INTERVAL 2 DAY LIMIT 0 , 1 ";
   $result = $db->fetchRow($sql);          
   return $result;   
      
  }   
  public function findbeforeslice($user_id,$email,$dateBefore) 
   {    
    $db     = Zend_Db_Table::getDefaultAdapter();
    $sql    = "Select usr . * , fs . * , cps . * from user as us, friends as fs,comment_profile_share as cps,user as usr WHERE us.email= fs.myemail AND us.user_id=cps.userid  AND us.user_id='$user_id' AND usr.email = fs.useremail AND us.email = '$email' AND cps.postdate <= '$dateBefore' ORDER BY cps.postdate DESC LIMIT 0 , 5 ";
   $result  = $db->fetchAll($sql); 
    
    return $result;   
  } 
  
  public function geImageordrs($id,$user_id)
  {
     $db    = Zend_Db_Table::getDefaultAdapter();
              $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT `id`,`order` from photo WHERE id !='$id' AND userid='$user_id'";                 
    $result = $db->fetchAll($sql);       
    return $result;  
  }
 
public function getusercoverImage($user_id)
{
   
   $db    = Zend_Db_Table::getDefaultAdapter();
              $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * from photo WHERE userid ='$user_id' AND img_type =''  order by `order` ASC";                 
    $result = $db->fetchAll($sql);       
    return $result;   
    
}  

public function getusergifs($user_id)
{   
   $db     = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * from gifs WHERE userid ='$user_id' AND  img_type ='' ";                 
    $result = $db->fetchAll($sql);       
    return $result;       
}

public function getallmultigif($user_id)
{
     $db     = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT sg.*,sg.picture as gifs_image from slice_gifs as sg WHERE sg.user_id ='$user_id' ";                 
    $result = $db->fetchAll($sql);       
    return $result;    
}

public function getgifsByid($id)
{   
   $db    = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * from gifs WHERE `id` ='$id'";                 
    $result = $db->fetchRow($sql);       
    return $result;     
}

public function  getmultigisfsByid($id)
{
    $db    = Zend_Db_Table::getDefaultAdapter();
            $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT sg.*, sg.picture as gifs_image from slice_gifs as sg WHERE sg.`id` ='$id'";                 
    $result = $db->fetchRow($sql);       
    return $result; 
}        

public function deletegifByID($id) 
  {     
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "DELETE FROM gifs WHERE id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;
    }
    
   public function deletemultigif($id) 
  {     
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "DELETE FROM slice_gifs WHERE id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;
    } 
    
  public function deleteGifwall($id)
  {
    $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "DELETE FROM latest_slice  WHERE gifs_id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;  
  }
  
  public function getgifImage($id)
  {
     $db    = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = $db->select()->from('gifs')->where('id = ? ', $id);                  
        $result = $db->fetchRow($sql);          
        return $result; 
  }
  
  public function getmultigifImage($id)
  {
     $db    = Zend_Db_Table::getDefaultAdapter();
                 $db->setFetchMode(Zend_Db::FETCH_OBJ);         
        $sql  = "SELECT sg .*,sg.picture as gifs_image  FROM slice_gifs as sg WHERE sg.id= '$id' "; 
        $result = $db->fetchRow($sql);          
        return $result; 
  }
  
 public function getvideosbyId($id)
 {
    $db    = Zend_Db_Table::getDefaultAdapter();
                $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = $db->select()->from('video')->where('id = ? ', $id);                  
    $result = $db->fetchRow($sql);          
    return $result; 
     
 }
 
 public function deletevideoByID($id) 
  {     
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "DELETE FROM video WHERE id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;
    }
    
  public function deletevideowall($id)
  {
    $db = Zend_Db_Table::getDefaultAdapter();
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        $sql    = "DELETE FROM latest_slice  WHERE video_id='".$id."'";
        $result = $this->_db->query($sql);
        return $result;  
  }
  
  public function getuserArticle($userid)
  {
    $db = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * FROM user_article WHERE user_id ='$userid'";
    $result = $db->fetchAll($sql); 
    return $result;  
  }
  
  public function getarticlesbyid($art)
  {
    $db = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);
    $sql    = "SELECT * FROM user_article WHERE article_id ='$art'";
    $result = $db->fetchRow($sql); 
    return $result;  
  }
  
// public function getimageUser($id,$userid)
// {
//   $db = Zend_Db_Table::getDefaultAdapter();
//    $db->setFetchMode(Zend_Db::FETCH_OBJ);
//    $sql    = "SELECT * FROM user_article WHERE article_id ='$art'";
//    $result = $db->fetchRow($sql); 
//    return $result;  
// }

 public function GetalbTotalLikes($id)
  {
    $db      = Zend_Db_Table::getDefaultAdapter();	
    $query_like_num     = "SELECT * FROM wall_post_likes WHERE albumpost_id='".$id."' AND likes=1";	
    $query_like_num_res = $db->fetchAll($query_like_num);	 	   
    $num_likes          = count($query_like_num_res);
    return $num_likes;
 } 
 
 public function GetmultimgLikes($id,$likeParam)
  {
    $db      = Zend_Db_Table::getDefaultAdapter();	
    $query_like_num     = "SELECT * FROM wall_post_likes WHERE $likeParam='".$id."' AND likes=1";	
    $query_like_num_res = $db->fetchAll($query_like_num);	 	   
    $num_likes          = count($query_like_num_res);
    return $num_likes;
 }  
 
  public function fetch_albcomment_result($wallid)
  {
    $db                    = Zend_Db_Table::getDefaultAdapter();	
    $query_share           = "SELECT * FROM comment_profile_share WHERE albumphoto_id='".$wallid."' order by id asc";	
    $query_share_res       = $db->fetchAll($query_share);
    return $query_share_res;
  }
  
  public function fetch_comment_multicount($wallid,$datapram)
  {
    $db              = Zend_Db_Table::getDefaultAdapter();	
   $query_share      = "SELECT * FROM comment_profile_share WHERE $datapram='".$wallid."'"; 
    $query_share_res = $db->fetchAll($query_share);
    return $query_share_res;
  }
  
  public function fetch_like_comment($likeid,$userid)
  {
    $db                    = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);	
    $query_share           = "SELECT * FROM wall_post_likes WHERE albumpost_id='".$likeid."' AND userid='".$userid."' ";	
    $query_share_res       = $db->fetchAll($query_share);
        return $query_share_res;
  } 
  
   public function fetch_like_multi($likeid,$userid,$datapram)
  {
    $db                    = Zend_Db_Table::getDefaultAdapter();
    $db->setFetchMode(Zend_Db::FETCH_OBJ);	
    $query_share           = "SELECT * FROM wall_post_likes WHERE $datapram='".$likeid."' AND userid='".$userid."' ";	
    $query_share_res       = $db->fetchAll($query_share);
        return $query_share_res;
  }
  
  public function fetch_likenum_res($postid)
  {
    $db                    = Zend_Db_Table::getDefaultAdapter();	
    $query_share           = "SELECT * FROM wall_post_likes WHERE albumpost_id='".$postid."' and likes=1";	
    $query_share_res       = $db->fetchAll($query_share);
    return $query_share_res;
  }
  public function fetch_multi_res($postid,$datapram)
  {
    $db                    = Zend_Db_Table::getDefaultAdapter();	
    $query_share           = "SELECT * FROM wall_post_likes WHERE $datapram='".$postid."' and likes=1";	
    $query_share_res       = $db->fetchAll($query_share);
    return $query_share_res;
  }
  
  public function getmultipleImage($ides)
  {
    $db                    = Zend_Db_Table::getDefaultAdapter();	
    $query_share           = "SELECT * FROM slice_picture WHERE photo_id='".$ides."' ";	
    $query_share_res       = $db->fetchAll($query_share);
    return $query_share_res;
  }
  
  public function getmultiplegifs($ides)
  {
      $db                    = Zend_Db_Table::getDefaultAdapter();	
    $query_share           = "SELECT * FROM slice_gifs WHERE gifs_id='".$ides."' ";	
    $query_share_res       = $db->fetchAll($query_share);
    return $query_share_res;
      
  } 
  
  public function getallmultiimage($uid)
  {
     $db                    = Zend_Db_Table::getDefaultAdapter();	
    $query_share           = "SELECT * FROM slice_picture WHERE user_id='".$uid."' ";	
    $query_share_res       = $db->fetchAll($query_share);
    return $query_share_res;  
  }   
  
  public function getmultiByImage($id)
  {
     $db     = Zend_Db_Table::getDefaultAdapter();
     $db->setFetchMode(Zend_Db::FETCH_OBJ);
     $sql    = $db->select()->from('slice_picture')->where('id = ? ', $id);                  
     $result = $db->fetchRow($sql);          
     return $result;    
  }   
  
  public function multiImage($id)
  {
     $db     = Zend_Db_Table::getDefaultAdapter();
     $db->setFetchMode(Zend_Db::FETCH_OBJ);
      $sql    = "SELECT count(id) as count FROM slice_picture where photo_id ='$id' ";                  
     $result = $db->fetchRow($sql);
     return $result->count;    
  } 
  
  public function multigifco($id)
  {
     $db     = Zend_Db_Table::getDefaultAdapter();
     $db->setFetchMode(Zend_Db::FETCH_OBJ);
      $sql    = "SELECT count(id) as count FROM slice_gifs where gifs_id ='$id' ";                  
     $result = $db->fetchRow($sql);
     return $result->count;      
  }       

  /***End Of Class ***/  
 }
?> 