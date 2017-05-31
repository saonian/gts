<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module_model extends MY_Model {

	public function __construct() {
		parent::__construct();
	}

	public function get_product_modules($product_id = 0){
		$product_id = empty($product_id) ? $this->current_product_id : (int)$product_id;
		return $this->get_modules_tree($product_id, 0);
	}

	public function get_module_by_id($module_id){
		return $this->db->get_where(TBL_MODULE, array('id' => (int)$module_id))->row();
	}

	public function get_all_modules($product_id = 0){
		$product_id = empty($product_id) ? $this->current_product_id : (int)$product_id;
		$modules = $this->db->get_where(TBL_MODULE, array('product_id' => $product_id))->result();
		foreach ($modules as $key => $val) {
			$val->name = $this->get_module_path($val->id);
		}
		return $modules;
	}

	private function get_modules_tree($product_id = 0, $parent_id = 0){
		$product_id = empty($product_id) ? $this->current_product_id : (int)$product_id;
		$this->db->order_by('order', 'asc');
		$modules = $this->db->get_where(TBL_MODULE, array('product_id' => $product_id, 'parent' => $parent_id))->result();
		foreach ($modules as $key => $val) {
			$sub_modules = $this->get_modules_tree($product_id, $val->id);
			if(!empty($sub_modules)){
				$val->childs = $sub_modules;
			}
		}
		return $modules;
	}

	public function get_modules_tree_html($product_id = 0, $parent_id = 0, $edit = TRUE){
		$product_id = empty($product_id) ? $this->current_product_id : (int)$product_id;
		$modules = $this->db->get_where(TBL_MODULE, array('product_id' => $product_id, 'parent' => $parent_id))->result();
		$html = '';
		if(!empty($modules)){
			$html = $parent_id == 0 ? '<ul class="tree treeview">' : '<ul>';
			foreach ($modules as $key => $val) {
				if($edit){
					$html .= '<li>'.
						$val->name.
						'<a class="iframe" data-url="/module/edit/'.$val->id.'" href="javascript:">编辑</a>
						 <a href="/module/'.$val->id.'">子模块</a>
						 <a href="/module/delete/'.$val->id.'" onclick="return confirm(\'确认删除吗?\')">删除</a>
	 					 <input type="text" class="text-1" style="width:30px;text-align:center" value="'.$val->order.'" id="orders['.$val->id.']" name="orders['.$val->id.']">';
				}else{
					$html .= "<li><a href=\"/story/?mid={$val->id}&body=1&is_product=1&allproject=1\" target=\"story_list\">{$val->name}</a>";
				}
				$sub_html = $this->get_modules_tree_html($product_id, $val->id, $edit);
				if(!empty($sub_html)){
					$html .= $sub_html;
				}
				$html .= '</li>';
			}
			$html .= '</ul>';
		}
		return $html;
	}

	public function get_modules_select_html($product_id = 0, $parent_id = 0, $selected_id = 0){
		$product_id = empty($product_id) ? $this->current_product_id : (int)$product_id;
		$modules = $this->db->get_where(TBL_MODULE, array('product_id' => $product_id, 'parent' => $parent_id))->result();
		$html = '';
		if(!empty($modules)){
			foreach ($modules as $key => $val) {
				$name = $this->get_module_path($val->id);
				$selected = $selected_id == $val->id ? 'selected' : '';
				$html .= "<option value=\"{$val->id}\" {$selected}>{$name}</option>";
				$sub_html = $this->get_modules_select_html($product_id, $val->id, $selected_id);
				if(!empty($sub_html)){
					$html .= $sub_html;
				}
			}
		}
		return $html;
	}

	private function get_module_path($module_id){
		$module = $this->get_module_by_id($module_id);
		$path = array();
		if($module){
			$path_ids = array_filter(explode(',', $module->path));
			$this->db->where_in('id', $path_ids);
			$modules = $this->db->get(TBL_MODULE)->result();
			foreach ($modules as $val) {
				$path[] = $val->name;
			}
		}
		return '/'.join('/', $path);
	}

	public function get_sub_modules($parent_id){
		return $this->db->get_where(TBL_MODULE, array('product_id' => $this->current_product_id, 'parent' => $parent_id))->result();
	}

	public function save_module(){
		$module_id = $this->input->post('module_id', TRUE);
		$parent_id = $this->input->post('parent_id', TRUE);
		$orders = $this->input->post('orders', TRUE);
		$modules = $this->input->post('modules', TRUE);
		if($orders){
			$orders = array_map('intval', $orders);
		}

		$this->db->trans_start();

		$redirect = TRUE;
		$module_id = intval($module_id);
		if($module_id && $parent_id !== FALSE){
			$redirect = FALSE;
			$name = $this->input->post('name', TRUE);
			$this->db->where('id', $module_id);
			$this->db->update(TBL_MODULE, array('parent' => $parent_id, 'name' => $name));
		}

		if($orders){
			foreach ($orders as $mid => $ord) {
				$this->db->where('id', $mid);
				$this->db->update(TBL_MODULE, array('order' => $ord));
			}
		}

		if($modules){
			foreach ($modules as $key => $val) {
				$module_id = 0;
				if(strpos($key, 'id') !== FALSE && strpos($key, 'id') == 0){
					$module_id = intval(str_replace('id', '', $key));
				}
				// 存在则更新，否则插入
				if($module_id){
					if(empty($val)){
						$this->db->delete(TBL_MODULE, array('id' => $module_id));
					}else{
						$this->db->where('id', $module_id);
						$this->db->update(TBL_MODULE, array('name' => trim($val)));
						$this->update_module_data($module_id);
					}
				}else if(!empty($val)){
					$new_module = array(
						'name' => trim($val),
						'parent' => $parent_id,
						'product_id' => $this->current_product_id,
						'path' => '',
						'grade' => 1,
						'order' => 0,
						'type' => 'story',
						'owner' => ''
					);
					$this->db->insert(TBL_MODULE, $new_module);
					$module_id = $this->db->insert_id();
					$this->update_module_data($module_id);
				}
			}
		}

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE){
			show_msg(ERROR ,'保存失败');
		}

		if($redirect){
			$redirect_url = '/module'.($parent_id ? "/{$parent_id}" : '');
			header("Location: {$redirect_url}");
		}else{
			echo '<script>window.close();window.parent.location.reload();</script>';
		}
		exit;
	}

	/**
	 * 更新模板数据(path,grade,order)
	 * @param  int $module_id 模板ID
	 * @return void
	 */
	private function update_module_data($module_id){
		$module = $this->get_module_by_id($module_id);
		if($module->parent == 0){
			$path = ",{$module_id},";
			$grade = 1;
			$max_order = $this->db->query("SELECT MAX(`order`) AS ord FROM ".TBL_MODULE." WHERE parent=0")->row();
			$max_order = $max_order->ord;
			$order = $max_order + 1;
		}else{
			$parent_id = $module->parent;
			$path = '';
			$grade = 1;
			while ($parent_id) {
				$parent = $this->db->get_where(TBL_MODULE, array('id' => $parent_id))->row();
				$parent_id = $parent->parent;
				$path = ','.$parent->id.','.$path;
				$grade++;
			}
			$path .= $module_id.',';
			$max_order = $this->db->query("SELECT MAX(`order`) AS ord FROM ".TBL_MODULE." WHERE parent={$module->parent}")->row();
			$max_order = $max_order->ord;
			$order = $max_order + 1;
		}
		$this->db->where('id', $module_id);
		$this->db->update(TBL_MODULE, array('path' => $path, 'grade' => $grade, 'order' => $order));
	}

	public function delete($module_id){
		$this->db->delete(TBL_MODULE, array('id' => $module_id));
		header("Location: /module");exit;
	}

	public function get_sub_mids($module_id){
		$ids = array($module_id);
		$modules = $this->db->get_where(TBL_MODULE, array('parent' => (int)$module_id))->result();
		foreach ($modules as $val) {
			$ids[] = $val->id;
			$subids = $this->get_sub_mids($val->id);
			$ids = array_merge($ids,$subids);
		}
		$ids = array_unique($ids);
		return $ids;
	}
}