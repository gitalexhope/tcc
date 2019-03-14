<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard extends CI_Controller {
  public function  __construct()
  {
     parent::__construct();
     $this->load->model('login_model');
     if(!$this->login_model->userLoginCheck() && !$this->input->is_ajax_request())
     {
      redirect('user_auth');
     }
     else
     {
       $user=$this->session->userdata('user_logged_in');  
       $this->user_id=$user['id'];
       //$this->load->model('dash_model');
       //$this->load->model('campaign_model');
     }  
     
  }
  public function index()
  {
      $this->load->view('UI/header');
      //$this->load->view('UI/dashboard');
      $this->load->view('UI/footer');
  }

  public function get_pre_data()
  {
    $to_date=date('Y-m-d');
    $frm_date = date('Y-m-d',strtotime("-31 days"));
    $data['status_text']='Success';
    $data['status_code']='1';
    $data['confirmed']=$this->dash_model->get_confirmed_order_count($frm_date,$to_date);
    $data['canceled']=$this->dash_model->get_canceled_order_count($frm_date,$to_date);
    $data['returned']=$this->dash_model->get_returned_order_count($frm_date,$to_date);
    $data['revenue']=$this->dash_model->get_revenue($frm_date,$to_date);
    $data['graph_data']=$this->dash_model->get_graph_data($frm_date,$to_date);
    $data['fbk_graph']=$this->dash_model->get_feedback_graph();
    $data['donut_data']=$this->dash_model->get_donut_data($frm_date,$to_date);
    $data['top_10']=$this->dash_model->get_top_10_product($frm_date,$to_date);
    $data['top_10_cities']=$this->dash_model->get_top_10_cities($frm_date,$to_date);
    $data['cmp_info']=$this->dash_model->get_consolidated_campaign_details($frm_date,$to_date);
    $data['feedback_data']=$this->dash_model->get_feedback_data($frm_date,$to_date);
    $data['metrics']=$this->campaign_model->get_campaign_metrics($frm_date,$to_date);
    $data['fbk_data']=$this->campaign_model->get_feedback_data($frm_date,$to_date);
    echo json_encode($data);
  }
  public function filter_data()
  {
    if(!empty($_POST['from_date']) && !empty($_POST['to_date']))
    {
      $this->process_data($this->input->post('from_date'),$this->input->post('to_date'));  
    }
    else
    {
      $data['status_text']='Date range error';
    $data['status_code']='0';
    echo json_encode($data);
    }
    
  }
  public function get_filter_data()
  {
    // echo date('Y-m-d', strtotime('first day of last three month'));die();
    if(!empty($_POST['cntxt']))
    {
      if($_POST['cntxt']=='today')
      {
        $from_date=date('Y-m-d');
        $to_date=date('Y-m-d');
      }
      // echo date('d/m/Y', strtotime('-7 days'));
      if($_POST['cntxt']=='7 days')
      {
        $to_date=date('Y-m-d');
        $from_date = date('Y-m-d',strtotime("-7 days"));
      }
      if($_POST['cntxt']=='30 days')
      {
        $to_date=date('Y-m-d');
        $from_date = date('Y-m-d',strtotime("-30 days"));
      }
      if($_POST['cntxt']=='this month')
      {
        $to_date=date('Y-m-d');
        $from_date = date('Y-m-01');
      }
      if($_POST['cntxt']=='last month')
      {
        $to_date= date('Y-m-d', strtotime('last day of last month'));
        $from_date =  date('Y-m-d', strtotime('first day of last month'));
      }
      
      $this->process_data($from_date,$to_date);  
    }
    else
    {
      $data['status_text']='Input  error';
      $data['status_code']='0';
      echo json_encode($data);
    }
    
  }

  public function process_data($frm_date,$to_date)
  {
        $data['status_text']='Success';
        $data['status_code']='1';
        $data['confirmed']=$this->dash_model->get_confirmed_order_count($frm_date,$to_date);
        $data['canceled']=$this->dash_model->get_canceled_order_count($frm_date,$to_date);
        $data['returned']=$this->dash_model->get_returned_order_count($frm_date,$to_date);
        $data['revenue']=$this->dash_model->get_revenue($frm_date,$to_date);
        $data['graph_data']=$this->dash_model->get_graph_data($frm_date,$to_date);
        $data['fbk_graph']=$this->dash_model->get_feedback_graph();
        $data['donut_data']=$this->dash_model->get_donut_data($frm_date,$to_date);
        $data['top_10']=$this->dash_model->get_top_10_product($frm_date,$to_date);
        $data['top_10_cities']=$this->dash_model->get_top_10_cities($frm_date,$to_date);
        $data['cmp_info']=$this->dash_model->get_consolidated_campaign_details($frm_date,$to_date);
        $data['feedback_data']=$this->dash_model->get_feedback_data($frm_date,$to_date);
        echo json_encode($data);  
  }
  
  
}
