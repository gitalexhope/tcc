<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_auth extends CI_Controller {
 
    public function  __construct()
	{
	   parent::__construct();
       $this->load->library('form_validation');
	}
	

	public function index()
	{
         if(!$this->login_model->userLoginCheck())
          {
    		  $this->load->view('user_login');
	      }
	      else
	      {   
	        $user=$this->session->userdata('user_logged_in');
			
			if($user['scr_u_id']==1)
			{
   		      redirect('manage_users');
			}
			elseif($user['isadmin']==1 || $user['isadmin']==2 || $user['scr_u_id']!=1)
			{
   		      redirect('orders_report_orderby_date_list');
			}
	      }
    }
    public function login()
    {
         $this->form_validation->set_rules('username', 'Username ', 'required');
	     $this->form_validation->set_rules('password', 'Password ', 'required|callback_user_verify');
	     if($this->form_validation->run() == FALSE)
	     {
	          $this->load->view('user_login');
	     }
	     else
	     {
	         
	        $user=$this->session->userdata('user_logged_in');
			if($user['scr_u_id']==1)
			{
   		      redirect('manage_users');
			}
			elseif($user['isadmin']==1 || $user['isadmin']==2 || $user['scr_u_id']!=3)
			{
   		      redirect('manage_users');
			}
			

	     }
 
    }
    public function staff_login()
    {
         $this->form_validation->set_rules('username', 'Username ', 'required');
	     $this->form_validation->set_rules('password', 'Password ', 'required|callback_staff_verify');
	     if($this->form_validation->run() == FALSE)
	     {
	          $this->load->view('staff_login');
	     }
	     else
	     {
   		      redirect('customer_relation');
	     }
 
    }
    function staff_verify($password)
	{
	  $usr = $this->input->post('username');
	  $result=$this->login_model->staffLoginProcess($usr,$password);
	  if($result)
	  {
	     	 return true;
	  }
	  else
	  {
	    $this->form_validation->set_message('user_verify', 'Invalid username or password');
	    return false;
	  }
	}     
    function user_verify($password)
	{
	  $usr = $this->input->post('username');
	  $result=$this->login_model->userLoginProcess($usr,$password);
	  if($result)
	  {
	     if($this->login_model->is_verified_user())
		 {
			 return true;
	     }
         else
		 {
		 	 
            $this->session->set_userdata('resent_email', $this->input->post('username'));		 	
		 	$err_msg="Please verify your mail from the email we have sent or <a href='".base_url()."user_auth/resend_mail/'>To Resend the Activation mail please click </a>" ;

			$this->form_validation->set_message('user_verify', $err_msg);
	        return false;
		 } 		 
	  }
	  else
	  {
	    $this->form_validation->set_message('user_verify', 'Invalid username or password');
	    return false;
	  }
	}
        
    public function logout()
  	{
	  $this->login_model->userLogoutProcess();
	  redirect("user_auth","refresh");
	}
     

    public function signup($plan_name='Micro')
	{
		$plan_name=strtolower($plan_name) ;
		$plan_array=array('micro','small','medium','large','extra_large');
		if(in_array($plan_name,$plan_array))
		{
			$key=md5(uniqid(rand(), true));	
			$_SESSION['session_key'] = $key;
			$this->session->mark_as_temp('session_key', 200);
			$data['plan_name']=$plan_name;
			$data['plan_list']=$plan_array;
			$this->load->view('user_signup',$data);
		}
		else
		{
			redirect('home');
		}
	}

	public function add_user()
	{
		 $this->form_validation->set_rules('fname', 'First Name ', 'required');
	     $this->form_validation->set_rules('lname', 'Last Name ', 'required');
	      $this->form_validation->set_message('is_unique', 'The Email already exist');
	     $this->form_validation->set_rules('email', 'email', 'required|valid_email|is_unique[scr_user.scr_uname]');
	     $this->form_validation->set_message('matches', 'Password mismatch');
	     $this->form_validation->set_rules('pwd', 'Password', 'required|min_length[8]|matches[rpwd]');
         $this->form_validation->set_rules('rpwd', 'confirm password', 'required');
        
  // $this->form_validation->set_rules( 'g-recaptcha-response', 'captcha', 'trim|callback_check_google_captcha|required' );
	     if($this->form_validation->run() == FALSE)
	     {
	     	
			if(empty($this->session->userdata('session_key')))
			{
				$key=md5(uniqid(rand(), true));	
				$this->session->set_userdata('session_key', $key);	
			}
			$data['session_key']=$this->session->userdata('session_key');
		    $this->load->view('user_signup',$data);
	     }
	     else
	     {
	       $mail_verify_key=md5(uniqid(rand(), true));	
  	       // $mail_verify_key='verified';
	       $ref_key=md5(uniqid(rand(), true));	
	      

	       $insert_user = array('scr_firstname' =>$this->input->post('fname'),
                                'scr_lastname' =>$this->input->post('lname'),
                                'scr_is_verified' => 1,
                                'scr_is_admin' => 2,
								'scr_active' => 1,
                                'scr_uname' =>$this->input->post('email'),
								'scr_password' =>$this->input->post('pwd'),
								'mail_verify_key'=>$mail_verify_key,
								
								
								//'cntry_id'=>$this->input->post('country')
                               );
	       $this->db->trans_start();
	   
           $this->db->insert('scr_user', $insert_user);
           $uid=$this->db->insert_id();

	       $this->db->trans_complete();
	       if(!empty($uid))
		   {
		    
	       $data['msg']="Account created please sign in ";
		    $this->load->view("signup_success",$data);
		   }
		   else
		   {
		    $this->load->view('user_signup');
		   }
	  
           
	     }
	}
	public function add_referer()
	{
		 $this->form_validation->set_rules('fname', 'First Name ', 'required');
	     $this->form_validation->set_rules('lname', 'Last Name ', 'required');
	     $this->form_validation->set_message('integer', 'Invalid mobile number');
	     $this->form_validation->set_message('exact_length', 'Invalid mobile number');
	     $this->form_validation->set_rules('mobile', 'Mobile number ', 'required|integer|exact_length[10]');
	     $this->form_validation->set_message('is_unique', 'The Email already exist');
	     $this->form_validation->set_rules('email', 'email', 'required|valid_email|is_unique[scr_user.scr_uname]');
	     $this->form_validation->set_message('matches', 'Password mismatch');
	     $this->form_validation->set_rules('pwd', 'Password', 'required|min_length[8]|matches[rpwd]');
         $this->form_validation->set_rules('rpwd', 'confirm password', 'required');
         $this->form_validation->set_rules( 'v_code', 'OTP', 'trim|callback_check_OTP|required' );
         $this->form_validation->set_rules( 'plan', 'Plan', 'trim|callback_check_plan|required' );
         $this->form_validation->set_rules('hash_key', 'hash_key ', 'required');
	     $this->form_validation->set_rules('referer', 'referer', 'required|callback_check_referer');
	     
         // $this->form_validation->set_rules( 'g-recaptcha-response', 'captcha', 'trim|callback_check_google_captcha|required' );
	     if($this->form_validation->run() == FALSE)
	     {
	     	
			if(empty($this->session->userdata('session_key')))
			{
				$key=md5(uniqid(rand(), true));	
				$this->session->set_userdata('session_key', $key);	
			}
			$data['session_key']=$this->session->userdata('session_key');
		    $rid=$this->input->post('referer');
	     	$ref_hash=$this->input->post('hash_key');
	       if($this->input->post('sign_up_type')=='SELF')
	       {
	       	$query=$this->db->query("SELECT scr_u_id as ref_key,referal_key as hash_key,'SELF' as sign_up_type FROM scr_user WHERE scr_u_id=".$this->db->escape($rid)." AND referal_key=".$this->db->escape($ref_hash));	
		   $data['ref_data']=$query->result_array();
		   
	       }	
	       elseif($this->input->post('sign_up_type')=='MAIL')
	       {
	       		 $query=$this->db->query("SELECT ref_id as ref_key,refered_hash_id as hash_key,refered_fname as fname, refered_lname as lname,refered_mail as mail,'MAIL' as sign_up_type
		   							FROM referal_hub WHERE ref_id=".$this->db->escape($rid)." AND refered_hash_id=".$this->db->escape($ref_hash))	;
		   $data['ref_data']=$query->result_array();
		  
	       }
	       $this->load->view('referer_signup',$data);
	     }
	     else
	     {
	       
	       $mail_verify_key=md5(uniqid(rand(), true));	
	       $mail_verify_key=$mail_verify_key;
	       $scr_is_verified=0;
  	       if($this->input->post('sign_up_type')=='MAIL')
	       {
	       $mail_verify_key='verified';
	       $scr_is_verified=1;	
	       }
	       $ref_key=md5(uniqid(rand(), true));	
	       $plan_nme=$this->input->post('plan');
	       $pl_sql="SELECT * from plan_manager where plan_code=".$this->db->escape(strtolower($plan_nme));
	       $pl_qry=$this->db->query($pl_sql);
	       $pln=$pl_qry->result_array();
	       $pln_id=1;
	       if(!empty($pln))
	       {
	       	 $pln_id=$pln[0]['plan_id'];
	       }

	       $insert_user = array('scr_firstname' =>$this->input->post('fname'),
                                'scr_lastname' =>$this->input->post('lname'),
                                'scr_is_verified' => $scr_is_verified,
                                'scr_is_admin' => 2,
								'scr_active' => 1,
                                'scr_uname' =>$this->input->post('email'),
								'scr_password' =>$this->input->post('pwd'),
								'mail_verify_key'=>$mail_verify_key,
								'referal_key'=>strtoupper($ref_key),
								'mobile_no'=>$this->input->post('mobile'),
								'first_plan_id'=>$pln_id
								//'cntry_id'=>$this->input->post('country')
                               );
	       $this->db->trans_start();
	   
           $this->db->insert('scr_user', $insert_user);
           $uid=$this->db->insert_id();
           if($this->input->post('sign_up_type')=='MAIL')
	       {
	       if($this->create_free_plan($uid,$pln_id))
	 	   	$data['msg']="Your mail has been verified successfully. And you can start using our feature.";
	 	   else
		  	$data['msg']="Your mail has been verified successfully. but not able to subscription please contact support team.";

           $this->db->query("UPDATE referal_hub SET is_signup=1,ref_type='MAIL' where ref_id=".$this->db->escape($_POST['referer'])." AND refered_hash_id=".$this->db->escape($_POST['hash_key'])." AND refered_mail=".$this->db->escape($_POST['email']));
           }
           elseif($this->input->post('sign_up_type')=='SELF')
	       {
	        	 $insert_ref_user = array('ref_by_user_id' =>$this->input->post('referer'),
                                'refered_mail' =>$this->input->post('email'),
                                'refered_hash_id' =>$this->input->post('hash_key'),
                                'refered_fname' =>$this->input->post("fname"),
                                'refered_lname' =>$this->input->post("lname"),
                                'is_signup'=>1,
                                'ref_type'=>'LINK'
                               );
	        	 $this->db->insert('referal_hub', $insert_ref_user);
      
	       }

	       $this->db->trans_complete();
	       if($this->input->post('sign_up_type')=='MAIL')
	       {
	   	       $data['msg']="Account created successfully please login .";
			   $this->load->view("action_success",$data);
		   }
		   elseif($this->input->post('sign_up_type')=='SELF')
		   {
		       $data['name']=$this->input->post("fname");
		  	   $data['activate_link']=base_url()."user_auth/mail_verify/".$uid."/".$mail_verify_key;
			   $msg=$this->load->view('mail/activation_mail',$data,TRUE);
			   if($this->sent_activation_link($msg,$this->input->post("email")))
			   {
			    $data['msg']="A verification link has been sent to your mail. Please verify by clicking the link.";
			    $this->load->view("signup_success",$data);
			   }
			   else
			   {
			   $data['msg']="Not able to create account .";
			   $this->load->view("signup_success",$data);
			   }
	     	
		   }
	     }
	}
  public function check_referer($ref_key)
  {
  	   if(!isset($_POST['sign_up_type']) || empty($_POST['sign_up_type']))
  	   {
  	   	 $this->form_validation->set_message('check_referer', 'referal data signup type missing');
         return FALSE;
  	   }
       if(!empty($this->input->post('email')) && !empty($this->input->post('referer')) && !empty($this->input->post('hash_key')) )
       { 
       	 if($this->input->post('sign_up_type')=='MAIL')
       	 {
       	     $query=$this->db->query("SELECT ref_id,refered_fname from referal_hub WHERE ref_id=".$this->db->escape($_POST['referer'])." AND refered_hash_id=".$this->db->escape($_POST['hash_key'])." AND refered_mail=".$this->db->escape($_POST['email']));
       	     $res=$query->result_array();
       	     if(!empty($res['0']['refered_fname']))
			 {
		    	return TRUE;
			 }
			 else
			 {
				$this->form_validation->set_message('check_referer', 'referal data mismatch');
	     		return FALSE;
			 }
       	 	
       	 }
       	 elseif($this->input->post('sign_up_type')=='SELF')
       	 {
       	     $query=$this->db->query("SELECT scr_u_id from scr_user WHERE scr_u_id=".$this->db->escape($_POST['referer'])." AND referal_key=".$this->db->escape($_POST['hash_key']));
       	     $res=$query->result_array();
       	     if(!empty($res['0']['scr_u_id']))
			 {
		    	return TRUE;
			 }
			 else
			 {
				$this->form_validation->set_message('check_referer', 'referal data mismatch');
	     		return FALSE;
			 }
       	 	
       	 }
       	 else
       	 {
       		$this->form_validation->set_message('check_referer', 'referal data mismatch');
         	return FALSE; 	
       	 }
         
       }
       else
       {
         $this->form_validation->set_message('check_referer', 'referal data mismatch');
         return FALSE;
       }
  }

	public function resend_mail($email='')
	{
       if($this->session->userdata('resent_email'))
	     {
		       $email=$this->session->userdata('resent_email');
		       $mail_verify_key=md5(uniqid(rand(), true));
		       $this->db->query("UPDATE scr_user SET mail_verify_key='".$mail_verify_key."' WHERE scr_uname='{$email}'");
		       $query=$this->db->query("SELECT scr_u_id,scr_uname,scr_firstname,scr_lastname from scr_user WHERE scr_uname='".$email."'");
		       $res=$query->result_array();
               $data['name']=$res[0]['scr_firstname'];
		  	   $data['activate_link']=base_url()."user_auth/mail_verify/".$res[0]['scr_u_id']."/".$mail_verify_key;
		  	   $msg=$this->load->view('mail/activation_mail',$data,TRUE);
			   if($this->sent_activation_link($msg,$email))
			   {
			    $data['msg']="A verification link has been resent to your mail. Please verify by clicking the link.";
			    $this->load->view("action_success",$data);
			   }
			   else
			   {
		         $data['msg']="Something went wrong please try again";	   	
		         $this->load->view("action_success",$data);
			   }
	          
         }
         else
         {
         	  $sdata['msg']="Something went wrong please try again";
         	  $this->load->view("action_success",$sdata);
         }
         
	}
  public function validate_country($country)
  {
    $sql="SELECT * FROM country_master WHERE country_id=".$this->db->escape($country);
    $query=$this->db->query($sql);
  	$res=$query->result_array();

  	if(count($res)>0)
  	{
  		return TRUE;
  	}
  	else
  	{
  		$this->session->set_flashdata('vmsg','Country code error'); 
  		$this->form_validation->set_message('validate_country', 'country code error');
  		return FALSE;
  	}
  }
  public function check_OTP($otp)
  {
       if(!empty($otp))
       {
        $qry=$this->db->query("SELECT count(*) as ttl from mobile_otp WHERE mobile_no=".$this->db->escape($_POST['mobile'])." AND first_otp=".$this->db->escape($otp));
        $res=$qry->result_array();

        if(!empty($res[0]['ttl']))
        {
        	return TRUE;
        }
   	    else
		{
			$this->form_validation->set_message('check_OTP', 'Wrong OTP code');
     		return FALSE;
		}
       }
       else
       {
         $this->form_validation->set_message('check_OTP', 'Wrong OTP code');
         return FALSE;
       }
  }
  public function check_plan($plan)
  {
  	   $plan_array=array('micro','small','medium','large','extra_large');
  	   $plan=strtolower($plan);
       if(!empty($plan) && $plan!='null' && in_array($plan,$plan_array))
       {
        	return TRUE;
       }
       else
       {
         $this->form_validation->set_message('check_plan', 'Please choose plan');
         return FALSE;
       }
  }
  public function check_google_captcha($recaptcha)
  {
       if(!empty($recaptcha))
       {
       	 $google_url="https://www.google.com/recaptcha/api/siteverify";
		 $secret='6Lc-qCkUAAAAAMP96w0aFOIZKHqV01cITYzL6SWY';
		 $ip=$_SERVER['REMOTE_ADDR'];
		 $url=$google_url."?secret=".$secret."&response=".$recaptcha."&remoteip=".$ip;
		 $res=file_get_contents($url);
		 
		 $res= json_decode($res, true);
		 if($res['success'])
		 {
	    	return TRUE;
		 }
		 else
		 {
			$this->form_validation->set_message('check_google_captcha', 'Wrong captcha code');
     		return FALSE;
		 }
         
       }
       else
       {
         $this->form_validation->set_message('check_google_captcha', 'Wrong captcha code');
         return FALSE;
       }
  }
  private function fetch_curl_data($url)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 25);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.16) Gecko/20110319 Firefox/3.6.16");
		$curlData = curl_exec($curl);
		curl_close($curl);
		return $curlData;
	}
 public function mail_verify($uid,$mail_hass)
 {
		if(!empty($uid) && !empty($mail_hass) && is_numeric($uid))
		{
		   $data=array();
		   $query=$this->db->query("SELECT scr_firstname,scr_uname,mail_verify_key,first_plan_id FROM scr_user where scr_u_id=".$this->db->escape($uid));
		   if ($query->num_rows() == 1)
	        {
	          $row = $query->row();
	          if (strcmp($row->mail_verify_key,$mail_hass)== 0) 
		      {
			   $update=array('mail_verify_key' =>"verified",'scr_is_verified'=>1); 

			   $this->db->where('scr_u_id',$uid);
			   $this->db->update('scr_user',$update);
		 	   if($this->create_free_plan($uid,$row->first_plan_id))
		 	   	$data['msg']="Your mail has been verified successfully. And you can start using our feature.";
		 	   else
		 	   	$data['msg']="Your mail has been verified successfully. but not able to free subscription please contact support team.";

	  		  }       
		  	  elseif(strcmp($row->mail_verify_key,'verified')==0)
			  {
			   $data['msg']="Your mail has already been verified and you can start using our feature.";
			  }
			  else
			  {
			   $data['msg']="Your verification mail expired create a new account";
			  }
			}
			else
			{
	          $data['msg']="No Record Found Or Link May Be Borken or Changed";
			}
		   $this->load->view("action_success",$data);
	   }
	   else
		{
		 redirect('user_auth');
		}
 }
 private function sent_activation_link($msg,$recev)
 {
     $this->load->library('email');
     $config['protocol'] = 'smtp';
     $config['smtp_host'] = 'ssl://smtp.zoho.com';
     $config['smtp_port'] = '465';
     $config['smtp_user'] = 'feedback_mail@proseller.in';
     $config['smtp_pass'] = 'Ravi560066';
     $config['charset'] = 'utf-8';
    // $config['crlf'] = "\r\n";
     $config['newline'] = "\r\n"; 
     $config['mailtype'] = "html";
     $this->email->initialize($config);
     $this->email->from("feedback_mail@proseller.in","Proseller.in");
     $this->email->to($recev);
     $this->email->subject("Proseller.in - user account activation mail");
     $this->email->message($msg);
     if ($this->email->send())
	  {
       return true;
      }
     else
	  {
       return FALSE;
      }
 }

 private function create_free_plan($user_id,$plan_id)
 {
 	// die($user_id);
 	$this->load->model("subscription_model");
 	$plan_count=$this->subscription_model->get_total_subscription($user_id);
 	if($plan_count > 0)
 	{
 		return true;
 	}
 	$this->db->trans_start();
 	$timestamp=date('Y-m-d H:i:s');
 	$this->subscription_model->update_user_balance($user_id,0,$timestamp,1);
 	$this->subscription_model->create_trial_plan($user_id,$plan_id,$timestamp,$timestamp);
 	$this->db->trans_complete();
	if ($this->db->trans_status() === FALSE)
	{
	  return FALSE;
	}
	else
	{
	 return true;	
	}
 }

 public function send_OTP()
 {
 	if(empty($this->session->userdata('session_key')))
 	{
 		echo '{"status_code":"0","status_text":"Form Session Expired please refresh"}';	
 		die();
 	}
 	if(isset($_POST['mobile_no'])  && !empty($_POST['mobile_no']) && is_numeric($_POST['mobile_no']) && strlen($_POST['mobile_no'])==10)
 	{
 		$otp=mt_rand(100000,999999);

 		$sql="INSERT INTO mobile_otp(mobile_no,first_otp,sent_count)values(".$this->db->escape($this->input->post('mobile_no')).",".$this->db->escape($otp).",1) ON Duplicate KEY update first_otp=values(first_otp),sent_count=sent_count+1;";
 		if($this->db->query($sql))
 		{
 			if($this->login_model->send_otp($this->input->post('mobile_no'),$otp))
 			{
 				echo '{"status_code":"1","status_text":"OTP successfully sent to your mobile "}';		
 			}
 			else
 			{
 				echo '{"status_code":"0","status_text":"Not able to send OTP to this number"}';	
 			}
 			
 		}
 		
 	} 
 	else
 	{
 		echo '{"status_code":"0","status_text":"Mobile number invalid"}';
 	}
 	
 }
  
}

