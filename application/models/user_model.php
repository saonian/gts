<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends MY_Model {

	public function __construct() {
		parent::__construct();
	}

	public function get_user_by_id($user_id){
		return empty($user_id) ? NULL : $this->db->get_where(TBL_USER, array('id' => (int)$user_id))->row();
	}

	public function get_user_by_account($account){
		return empty($account) ? NULL : $this->db->get_where(TBL_USER, array('account' => $account))->row();
	}

    public function attempt($account, $password){
        return empty($account) || empty($password) ? NULL : $this->db->get_where(TBL_USER, array('account' => $account, 'password' => $password))->row();
    }

	public function get_all_users(){
		return $this->db->get(TBL_USER)->result();
	}

    public function get_part_users(){
        return $this->db->get_where(TBL_USER,array('department_id' => 5))->result();
    }

	/**
	 * 获取有某个权限的用户
	 */
	public function get_user_by_item($item){
		$this->load->model('rbac_model');
		$all_users = $this->get_all_users();
		foreach ($all_users as $key => $val) {
			$result = $this->rbac_model->check_user_access($val->id, (string)$item);
			if(!$result){
				unset($all_users[$key]);
			}
		}
        return $all_users;
    }

    /**
     * 获取有某个权限的用户----针对系统组的用户
     */
    public function get_user_by_item_part($item){
        $this->load->model('rbac_model');
        $all_users = $this->get_part_users();
        foreach ($all_users as $key => $val) {
            $result = $this->rbac_model->check_user_access($val->id, (string)$item);
            if(!$result){
                unset($all_users[$key]);
            }
        }
        return $all_users;
    }

    public function save_user($data){
    	$this->db->where('id',$_SESSION['userinfo']['id']);
    	$this->db->update('gg_user',$data);
    }

//     public function thumb($src_file, $new_width, $new_height, $mid_width = 0, $mid_height = 0, $start_x = 0, $start_y = 0,$filename) {
//     	$pathinfo = pathinfo($src_file);
//         if ($new_width < 1 || $new_height < 1) {
//             echo "params width or height error !";
//             exit();
//         }
//         // 图像类型
//         $img_type = exif_imagetype($src_file);
//         $support_type = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
//         if (!in_array($img_type, $support_type, true)) {
//             echo "只支持jpg、png、gif格式图片裁剪";
//             exit();
//         }
//         /* 载入图像 */
//         switch ($img_type) {
//             case IMAGETYPE_JPEG :
//                 $src_img = imagecreatefromjpeg($src_file);
//                 break;
//             case IMAGETYPE_PNG :
//                 $src_img = imagecreatefrompng($src_file);
//                 break;
//             case IMAGETYPE_GIF :
//                 $src_img = imagecreatefromgif($src_file);
//                 break;
//             default:
//             echo "载入图像错误!";
//             exit();
//         }
//         /* 获取源图片的宽度和高度 */
//         $src_width = imagesx($src_img);
//         $src_height = imagesy($src_img);

//         // 为剪切图像创建背景画板
//         $mid_img = imagecreatetruecolor($mid_width, $mid_height);
//         //拷贝剪切的图像数据到画板，生成剪切图像
//         imagecopy($mid_img, $src_img, 0, 0, $start_x, $start_y, $mid_width, $mid_height);
//         // 为裁剪图像创建背景画板
//         $new_img = imagecreatetruecolor($new_width, $new_height);
//         //拷贝剪切图像到背景画板，并按比例裁剪
//         imagecopyresampled($new_img, $mid_img, 0, 0, 0, 0, $new_width, $new_height, $mid_width, $mid_height);

//         /* 按格式保存为图片 */
//         switch ($img_type) {
//             case IMAGETYPE_JPEG :
//                 imagejpeg($new_img, $filename, 100);
//                 break;
//             case IMAGETYPE_PNG :
//                 imagepng($new_img, $filename, 9);
//                 break;
//             case IMAGETYPE_GIF :
//                 imagegif($new_img, $filename, 100);
//                 break;
//             default:
//                 break;
//         }
//     return $filename;
// }
    public function thumb($src_file, $new_width, $new_height, $mid_width = 0, $mid_height = 0, $start_x = 0, $start_y = 0,$filename) {
        $pathinfo = pathinfo($src_file);
        $img_type = strtolower($pathinfo['extension']);
        if ($new_width < 1 || $new_height < 1) {
            echo "params width or height error !";
            exit();
        }
        // 图像类型
        $support_type = array('gif', 'jpg', 'png');
        if (!in_array($img_type, $support_type, true)) {
            echo "只支持jpg、png、gif格式图片裁剪";
            exit();
        }
        /* 载入图像 */
        switch ($img_type) {
            case 'jpg' :
                $src_img = imagecreatefromjpeg($src_file);
                break;
            case 'png' :
                $src_img = imagecreatefrompng($src_file);
                break;
            case 'gif' :
                $src_img = imagecreatefromgif($src_file);
                break;
            default:
            echo "载入图像错误!";
            exit();
        }
        /* 获取源图片的宽度和高度 */
        $src_width = imagesx($src_img);
        $src_height = imagesy($src_img);

        // 为剪切图像创建背景画板
        $mid_img = imagecreatetruecolor($mid_width, $mid_height);
        //拷贝剪切的图像数据到画板，生成剪切图像
        imagecopy($mid_img, $src_img, 0, 0, $start_x, $start_y, $mid_width, $mid_height);
        // 为裁剪图像创建背景画板
        $new_img = imagecreatetruecolor($new_width, $new_height);
        //拷贝剪切图像到背景画板，并按比例裁剪
        imagecopyresampled($new_img, $mid_img, 0, 0, 0, 0, $new_width, $new_height, $mid_width, $mid_height);

        /* 按格式保存为图片 */
        switch ($img_type) {
            case 'jpg' :
                imagejpeg($new_img, $filename, 100);
                break;
            case 'png' :
                imagepng($new_img, $filename, 9);
                break;
            case 'gif' :
                imagegif($new_img, $filename, 100);
                break;
            default:
                break;
        }
        return $filename;
    }
	
}