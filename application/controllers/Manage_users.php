<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manage_users extends CI_Controller {
  public function  __construct()
  {
     parent::__construct();
     $this->load->model('login_model');
     if(!$this->login_model->adminLoginCheck())
     {
      redirect('user_auth');
     }
     $user=$this->session->userdata('user_logged_in');  
     $this->user_id=$user['id'];
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      $this->load->view('UI/manage_users');
      $this->load->view('UI/footer');
  }
  public function get_user_list()
  {
    $res=$this->get_users();
    $data['status_code']='1';
    $data['status_text']='Success';
    $data['payload']=$res;
    echo json_encode($data);

  }
  public function get_users()
  {
    $sql="SELECT scr_u_id as uid,scr_firstname as fname,scr_lastname as lname,scr_uname as email,scr_password as password,scr_is_verified as is_verified,scr_active as is_active,
                 DATE_FORMAT(joined_on,'%Y-%m-%d') as joined,scr_is_admin,IF(scr_is_admin='1','Admin','User') as position
          FROM `scr_user`
          
          GROUP by scr_u_id ORDER BY position
          ";
    $query=$this->db->query($sql);
    return $query->result_array();
    
  }
  public function update_amazon_api()
   {

      if(isset($_POST['api_detail']))
      {
         $api=json_decode($_POST['api_detail']);
          if(isset($api->seller_id) &&  isset($api->access_key) && isset($api->secret_key) && !empty($api->seller_id) &&  !empty($api->access_key) && !empty($api->secret_key))
         {
         	$key_id=empty($api->key_id)?'NULL':$api->key_id;
            $sql="INSERT INTO scr_user(scr_u_id,scr_firstname,scr_uname,scr_password,scr_is_verified,scr_active,scr_is_admin) VALUES($key_id,'{$api->seller_id}','{$api->access_key}','{$api->secret_key}','1','1','2')
             ON DUPLICATE KEY UPDATE scr_firstname = VALUES(scr_firstname),scr_uname=VALUES(scr_uname),scr_password=VALUES(scr_password);";
            if($this->db->query($sql))      
            {
               echo '{"status_code":"1","status_text":"User Name and Password details updated"}';         
            }
            else
            {
                echo '{"status_code":"0","status_text":"Server Error please try again"}';     
            }
        }
         else
         {
           echo '{"status_code":"0","status_text":"Input Error"}';     
         }
      }
      else
      {
        echo '{"status_code":"0","status_text":"Input Error"}';  
      }
   }

  public function add_credits()
  {
    if(!empty($_POST['user_id']))
    {
        
        $this->db->trans_start();
        $this->db->query("UPDATE scr_user SET scr_active='0' WHERE scr_u_id=".$this->db->escape($_POST['user_id']));
        $this->db->trans_complete();

         if($this->db->trans_status() === FALSE)
         {
              $data['status_code']='0';
              $data['status_text']='Something went wrong pls try again';
         }
         else
         {
            $data['status_code']='1';
            $data['status_text']='Success';
         }
        
        $data['payload']=$this->get_users();
    
    }
    else
    {
        $data['status_code']='0';
        $data['status_text']='Input Error';
    }
    echo json_encode($data);
  }
  public function delete_amazon_api()
   {
   	  if(isset($_POST['user_id']))
   	  {
        if($this->db->query("UPDATE scr_user SET scr_active=0,scr_is_verified=0 WHERE scr_u_id=".$this->db->escape($_POST['user_id'])))
        {
        		echo '{"status_code":"1","status_text":"User  Deactivated Successfully"}';     
        }
        else
        {
        	 echo '{"status_code":"0","status_text":"Server Error please try again"}';     
        }
   	  }
   	  else
   	  {
   	  	echo '{"status_code":"0","status_text":"Input Error"}';  
   	  }
   }
   public function activate_user()
   {
   	  if(isset($_POST['user_id']))
   	  {
        if($this->db->query("UPDATE scr_user SET scr_active=1,scr_is_verified=1 WHERE scr_u_id=".$this->db->escape($_POST['user_id'])))
        {
        		echo '{"status_code":"1","status_text":"User Activated  Successfully"}';     
        }
        else
        {
        	 echo '{"status_code":"0","status_text":"Server Error please try again"}';     
        }
   	  }
   	  else
   	  {
   	  	echo '{"status_code":"0","status_text":"Input Error"}';  
   	  }
   }
   
   public function make_user_admin()
   {
   	  if(isset($_POST['user_id']))
   	  {
        if($this->db->query("UPDATE scr_user SET scr_is_admin=1 WHERE scr_u_id=".$this->db->escape($_POST['user_id'])))
        {
        		echo '{"status_code":"1","status_text":"User Has been added as Admin User"}';     
        }
        else
        {
        	 echo '{"status_code":"0","status_text":"Server Error please try again"}';     
        }
   	  }
   	  else
   	  {
   	  	echo '{"status_code":"0","status_text":"Input Error"}';  
   	  }
   }
   
   public function make_user_normal()
   {
   	  if(isset($_POST['user_id']))
   	  {
        if($this->db->query("UPDATE scr_user SET scr_is_admin=2 WHERE scr_u_id=".$this->db->escape($_POST['user_id'])))
        {
        		echo '{"status_code":"1","status_text":"User Has been added as Normal User"}';    
        }
        else
        {
        	 echo '{"status_code":"0","status_text":"Server Error please try again"}';     
        }
   	  }
   	  else
   	  {
   	  	echo '{"status_code":"0","status_text":"Input Error"}';  
   	  }
   }
  
  
  public function delete_user()
   {
   	  if(isset($_POST['user_id']))
   	  {
        if($this->db->query("DELETE FROM  scr_user  WHERE scr_u_id=".$this->db->escape($_POST['user_id'])))
        {
        		echo '{"status_code":"1","status_text":"User Has been Deleted Suceesfully"}';    
        }
        else
        {
        	 echo '{"status_code":"0","status_text":"Server Error please try again"}';     
        }
   	  }
   	  else
   	  {
   	  	echo '{"status_code":"0","status_text":"Input Error"}';  
   	  }
   }
  
}