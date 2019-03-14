<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Amazon_order_list extends CI_Controller {
  private $user_id;
  public function  __construct()
  {
       parent::__construct();
      if(!$this->login_model->userLoginCheck())
      {
        redirect('user_auth');
      }
      else
      {
        $this->load->model("amazon_transaction_model");   
        $user=$this->session->userdata('user_logged_in');  
        $this->user_id=$user['id'];
       
      }
       
  }

  public function index()
  {
    $this->load->view('UI/header');
    $this->load->view('UI/amazon_transaction');
    $this->load->view('UI/footer');
  }
  public function get_order_list($orderby='purchase_date',$direction='DESC',$offet,$limit,$searchterm='')
  {
      $result_set=$this->amazon_transaction_model->get_order_list($orderby,$direction,$offet,$limit,$searchterm);
      // $result_set['outstanding']=$this->amazon_transaction_model->get_outstanding_bill();
      echo json_encode($result_set);
  }
  public function order_info($ssg_tn='-1')
    {
      if($ssg_tn != -1)
      {
          $data['status_code'] = '1';
          $data['status_text'] = 'status completed';
          $data['info']  = $this->amazon_transaction_model->get_order_info_by_id($ssg_tn);
          if(count($data['info']) > 0 )
          $data['items']  = $this->amazon_transaction_model->get_order_items($ssg_tn);
          if($data['info'] > 0)
          echo json_encode($data);
          else
          {
            echo '{"status_code":"0","status_text":"No Details Found "}';   
          } 
      }
      else
      {
        echo '{"status_code":"0","status_text":"Input Error please try again "}';   
      }
    }

 
 
}

