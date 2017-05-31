<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); 
  
class Layout { 
    
    var $obj;
    var $layout;
    
    public function __construct($layout = 'layout_main') { 
        $this->obj =& get_instance(); 
        $this->layout = $layout; 
        $this->obj->load->model('project_model');
        $this->obj->load->model('product_model');
    } 
  
    public function set_layout($layout) { 
      $this->layout = $layout; 
    } 

    public function view($view, $data = NULL, $return = FALSE, $include_headfoot = TRUE) { 
        $data['controller'] = get_controller();
        $data['method'] = get_method();
        $pid = $this->obj->input->get('p');
        if($pid){
            setcookie('current_project_id', (int)$pid, time() + 30*24*60*60, '/');
        }
        $data['current_project_id'] = $pid ? $pid : (empty($_COOKIE['current_project_id']) ? 0 : $_COOKIE['current_project_id']);
        $data['current_product_id'] = empty($_COOKIE['current_product_id']) ? 0 : $_COOKIE['current_product_id'];
        $data['pmsdata'] = $this->obj->config->item('pms');
        $data['menu'] = $this->obj->config->item('menu');
        $data['all_project'] = $this->obj->project_model->get_all_projects();
        $data['all_product'] = $this->obj->product_model->get_all_products();
        $data['include_headfoot'] = $include_headfoot;//必须放在下面这句上边

        $data['content_for_layout'] = $this->obj->load->view($view, $data, TRUE);
        if($return) {
            $output = $this->obj->load->view($this->layout, $data, TRUE);
            return $output; 
        } else { 
            if($include_headfoot){
                $this->obj->load->view($this->layout, $data, FALSE); 
            }else{
                $this->obj->load->view($view, $data, FALSE); 
            }
        } 
    } 
} 
?>