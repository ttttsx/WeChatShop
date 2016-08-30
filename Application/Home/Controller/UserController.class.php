<?php 
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {
	// 用户首页
	public function index() {
		if(!isset($_SESSION['userInfo'])) {
			$this->redirect('login');
		} else {
			$order = M('order'); // 订单表
			$user = session('userInfo');
			if(!isset($_GET['status'])) {
				$status = 1;
			} else {
				$status = $_GET['status'];
			}
			$userId = $user['id'];
			// 根据订单状态查询订单
			$orders = $order->order('id desc')->where('status='.
					$status.' and userId='.$userId)->select();
			$orderDetail = M('orderdetail'); // 订单详情表
			// 遍历循环用户订单列表
			foreach ($orders as $key=>$val) {
				$orderDetails = $orderDetail->where("orderId='".$val['orderid']."'")->select();
				// 对该订单的详细信息遍历
				foreach ($orderDetails as $value) {
					$items = array('productName'=>$value['productname'],'img'=>$value['img'],'price'=>$value['price'],'quantity'=>$value['quantity'],'productId'=>$value['productid']);
					$orders[$key]['items'][] = $items;
					//var_dump($items);
				}
			}
			$this->assign('orders', $orders);
			$this->assign('status', $status);
			$this->assign('user', $user);
			//var_dump($orders);
			$this->display();
		}
	}
	
	public function login() {
		$this->display();
	}
	public function ajaxLogin() {                // 登录判断
		$userName = I('post.userName');
		$password = md5(I('post.password'));
		
		$User = M('user');
		$users = $User->where("userName='{$userName}' and password='{$password}'")->find();
		if(is_array($users)) {
			$data = array('status'=>1);
			$_SESSION['userInfo'] = $users;
		} else {
			$data = array('status'=>0);
		}
		echo json_encode($data);
		exit;
	}
	public function doRegister() { // 注册
		if(IS_POST) {
			// 获取post参数
			$data['userName'] = I('post.userName','','trim');
			$data['password'] = md5(I('post.password','','trim'));
			$data['securityQuestion'] = I('post.securityQuestion','','trim');
			$data['answer'] = I('post.answer','','trim');
			$user = M('user');
			$insert = $user->add($data);
			if($insert) {
				$this->success("注册成功",'login');
			} else {
				$this->error("注册失败");
			}
		}
	}
	public function ajaxRegister() { // 用户名查重
		$userName = I('post.userName');
		$User = M('user');
		$count = $User->where("userName='".$userName."'")->find();
		if(is_array($count)) {
			echo "false";
		} else {
			echo "true";
		}
	}
	public function ajaxFind() { // 找回密码-查找用户
		$userName = I('post.userName');
	
		$User = M('user');
		$users = $User->where("userName='{$userName}'")->find();
		if(is_array($users)) {
			$data = array('status'=>1);
		} else {
			$data = array('status'=>0);
		}
		echo json_encode($data);
		exit;
	}
	public function checkAnswer() { // 找回密码-回答问题
		$userName = I('param.userName');
		$User = M('user');
		$users = $User->where("userName='{$userName}'")->find();
		$data = array('question'=>$users['securityquestion'], 'userName'=>$userName);
		$this->assign('question',json_encode($data));
		$this->display();
	}
	public function resetPassword() { // 重置密码-页面
		$userName = I('param.userName');
		$data = array('userName'=>$userName);
		$this->assign('name',json_encode($data));
		$this->display();
	}
	public function answerCheck() { // 找回密码-检验答案
		$answer = I('post.answer');
		$userName = I('post.userName');
		$User = M('user');
		$users = $User->where("userName='".$userName."'")->find();
		if($users['answer']==$answer) {
			$data = array('status'=>1);
		} else {
			$data = array('status'=>0);
		}
		echo json_encode($data);
		exit;
	}
	public function ajaxReset() { // 重置密码
		$userName = I('post.userName');
		$password = I('post.password');
		$User = M('user');
		$updateData['password'] = md5($password);
		$result = $User->where("userName='{$userName}'")->save($updateData);
		if($result !== false) {
			$data = array('status'=>1);
		} else {
			$data = array('status'=>0);
		}
		echo json_encode($data);
		exit;
	}
	
	public function address() { // 收货地址
		$address = M('address');
		$id = I('get.id','','intval');
		$type = I('get.type','edit','trim');
		
		if($id) {
			if($type=='del') { // 删除收货地址
				$address->where(array('id'=>$id,'userid'=>$_SESSION['userInfo']['id']))->delete();
				$msg = array('status'=>1,'info'=>L('delete_success'));
				$this->assign('msg',$msg);
			} else if($type=='mod') { // 修改收货地址
				$data['recName'] = I('param.recName');
				$data['mobile'] = I('param.mobile');
				$data['address'] = I('param.address');
				$data['province'] = I('param.province');
				$data['city'] = I('param.city');
				$data['area'] = I('param.area');
				$result = $address->where("id='{$id}'")->save($data);
				$msg = array('status'=>1,'info'=>L('modify_success'));
				$this->assign('msg',$msg);
			}
		}
		$addressList = $address->where(array('userId'=>intval($_SESSION['userInfo']['id'])))->select();
		$this->assign('addressList',$addressList);
		$this->display();
	}
	public function addAddress() {
		if(IS_POST) {
			$address = M('address');
			
			$recName = I('param.recName','trim');
			$province = I('param.province','trim');
			$city = I('param.city','trim');
			$area = I('param.area','trim');
			$mobile = I('param.mobile','trim');
			$addr = I('param.address','trim');
			
			$user = session('userInfo');
			$data['userId'] = $user['id'];
			$data['recName'] = $recName;
			$data['province'] = $province;
			$data['city'] = $city;
			$data['area'] = $area;
			$data['mobile'] = $mobile;
			$data['address'] = $addr;
			
			$result = $address->data($data)->add();
			if($result!==false) {
				$this->redirect('User/address');
			}
		}
		$this->display();
	}
	public function editAddress() {
		$address = M('address');
		$id = I('param.id');
		$info = $address->find($id);
		
		$this->assign('info',$info);
		//var_dump($info);
		$this->display();
	}
	public function logOut() {
		session(null);
		$data = array('status'=>1);
		echo json_encode($data);
	}
}
?>