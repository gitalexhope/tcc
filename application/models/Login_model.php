<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login_model extends CI_Model
	{
	      public function  __construct()
		  {
		   		parent::__construct();

	   	  }

	 public function userLoginProcess($username,$password)
         {
     	      $this -> db -> select('scr_u_id,scr_firstname,scr_lastname,scr_uname,scr_password,scr_is_admin,login_count,scr_is_verified');
              $this -> db -> from('scr_user');
		      $this -> db -> where('scr_uname', $username);
		      $this -> db -> where('scr_password',$password);
		      $this -> db -> where('scr_active',1);
			  //$this -> db -> where('scr_is_verified',1);

		      $this -> db -> limit(1);
		      $query = $this -> db -> get();
	          if($query -> num_rows() == 1)
	          {
	            $result = $query->result();
        	    if($result)
	            {
		             $sess_array = array();
		             foreach($result as $row)
		             {
		                 $sess_array = array('uname' => $row->scr_uname,
		                 	                 'fname' => $row->scr_firstname,
		                 	                 'lname' => $row->scr_lastname,
		                 	                 'id' => $row->scr_u_id,
		                 	                 'isadmin'=>$row->scr_is_admin,
		                 	                 'isverified'=>$row->scr_is_verified,
		                 	                 );

			         }
		             $this->session->set_userdata('user_logged_in', $sess_array);
		              return TRUE;
	            }
	          }
	         else
	         {
	           return false;
	         }
	     }

	     public function staffLoginProcess($username,$password)
         {
     	      $this -> db -> select('scr_u_id,scr_firstname,scr_lastname,scr_uname,scr_password,scr_is_admin,scr_is_verified');
              $this -> db -> from('scr_worker');
		      $this -> db -> where('scr_uname', $username);
		      $this -> db -> where('scr_password',$password);
		      $this -> db -> where('scr_active',1);
			  //$this -> db -> where('scr_is_verified',1);

		      $this -> db -> limit(1);
		      $query = $this -> db -> get();
	          if($query -> num_rows() == 1)
	          {
	            $result = $query->result();
        	    if($result)
	            {
		             $sess_array = array();
		             foreach($result as $row)
		             {
		                 $sess_array = array('uname' => $row->scr_uname,
		                 	                 'fname' => $row->scr_firstname,
		                 	                 'lname' => $row->scr_lastname,
		                 	                 'id' => $row->scr_u_id,
		                 	                 'isadmin'=>$row->scr_is_admin,
		                 	                 'isverified'=>$row->scr_is_verified,
		                 	                 );
			         }
		             $this->session->set_userdata('staff_logged_in', $sess_array);
				     return TRUE;
	            }
	          }
	         else
	         {
	           return false;
	         }
	     }



	public function userLogoutProcess()
	{
		   $this->session->unset_userdata('user_logged_in');
		   $this->session->unset_userdata('staff_logged_in');
		   $this->session->unset_userdata('existing_user_renew_notify');
		   $this->session->unset_userdata('trial_user_renew_notify');
		   $this->session->unset_userdata('low_balance_notify');
		   //$this->session->sess_destroy();
	}
	public function userLoginCheck()
	{
	     if($this->session->userdata('user_logged_in'))
	     {
		     $user=$this->session->userdata('user_logged_in');
		     if($user['isverified']==1)
		     {
			    return true;
		     }
		     else
		     {
		 	    return false;
		     }
	     }
	     else
	     {
	     	return false;
	     }

	}
	public function staffLoginCheck()
	{
	     if($this->session->userdata('staff_logged_in'))
	     {
		     $user=$this->session->userdata('staff_logged_in');
		     if($user['isverified']==1)
		     {
			    return true;
		     }
		     else
		     {
		 	    return false;
		     }
	     }
	     else
	     {
	     	return false;
	     }

	}
	public function is_profile_updated()
	{
		 $user=$this->session->userdata('user_logged_in');
		 $qry=$this->db->query("SELECT * from amazon_profile where profile_id=".$user['id']);
		 $res=$qry->result_array();
		 $qry1=$this->db->query("SELECT * from usr_comp_info where usr_id=".$user['id']);
		 $usr=$qry1->result_array();

		 if(count($res) > 0 && count($usr) > 0)
	     {
		        return true;
		 }
	     else
	     {
	     	return false;
	     }

	}

	public function adminLoginCheck()
	{
		if($this->session->userdata('user_logged_in'))
	     {
		     $user=$this->session->userdata('user_logged_in');
		     // if($user['isadmin']==1 && $user['isverified']==1)
		     if($user['id']==1)
		     {
			    return true;
		     }
		     else
		     {
		 	    return false;
		     }
	     }
	     else
	     {
	     	return false;
	     }
	}
	public function notadminLoginCheck()
	{
		if($this->session->userdata('user_logged_in'))
	     {
		     $user=$this->session->userdata('user_logged_in');
		     // if($user['isadmin']==1 && $user['isverified']==1)
		     if($user['id']<>'1')
		     {
			    return true;
		     }
		     else
		     {
		 	    return false;
		     }
	     }
	     else
	     {
	     	return false;
	     }
	}
	public function get_admin_prefix()
	{
		$query=$this->db->query("SELECT scr_tbl_pointer FROM scr_user WHERE scr_is_admin=1 AND scr_active=1 AND scr_user_type='Admin'");
		$res=$query->result_array();
		$admin_prefix=trim($res[0]['scr_tbl_pointer']);
		if(!empty($admin_prefix))
		return $admin_prefix;
	    else
	    {
	    	return '';
	    }
	}
	public function customerLoginCheck()
	{
		if($this->session->userdata('user_logged_in'))
	     {
		     $user=$this->session->userdata('user_logged_in');
		     if($user['isadmin']==2 && $user['isverified']==1)
		     {
			    return true;
		     }
		     else
		     {
		 	    return false;
		     }
	     }
	     else
	     {
	     	return false;
	     }
	}
	public function is_verified_user()
	{
		if($this->session->userdata('user_logged_in'))
	     {
		     $user=$this->session->userdata('user_logged_in');
		     if($user['isverified']==1)
		     {
			    return true;
		     }
		     else
		     {
		 	    return false;
		     }
	     }
	     else
	     {
	     	return false;
	     }
	}


	   public function user_exit($mail)

	   {

	      $query=$this->db->query("SELECT scr_u_id,scr_firstname,scr_uname,scr_lastname FROM Scr_user  where scr_uname=".$this->db->escape($mail).

		                          " AND scr_active=0");

		  if($query->num_rows()==0)

		  {

    		  return FALSE;

		  }

		  else

		  {

		     return $query->result_array();

		  }

	   }


		public function get_usage_left_count()
		{
			$user=$this->session->userdata('user_logged_in');
		    $query=$this->db->query("SELECT trial_count FROM scr_user where scr_u_id=".$user['id']);
		    $free=$query->result_array();
		    return $free[0]['trial_count'];
     	}

     	public function send_otp($phone_number,$custom_otp)
     	{
     		  $url="https://2factor.in/API/V1/53695b47-97bf-11e7-94da-0200cd936042/SMS/+91{$phone_number}/{$custom_otp}";
     		  $ch = curl_init();
		      curl_setopt($ch, CURLOPT_URL, $url);
		      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		      curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		      $response = curl_exec($ch);
		      if(curl_errno($ch))
		      {
		          return FALSE;
		      }
		      else
			  {
				  $res=json_decode($response);
				  if($res->Status=='Success')
				  {
				  	return TRUE;
				  }
				  else
				  {
				  	return FALSE;
				  }
			  }

     	}


  }
?>
