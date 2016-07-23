<?php 
namespace Home\Controller;
use Think\Controller;

class UserController extends Controller {
	public function index() {
		
	}
	
	public function login() {
		$this->display();
	}
	public function ajaxLogin() {                // 登录
		$userName = I('post.userName');
		$password = I('post.password');
		
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
	public function doRegister() {
		if(IS_POST) {
			// 获取post参数
			$data['userName'] = I('post.userName','','trim');
			$data['password'] = I('post.password','','trim');
			$data['securityQuestion'] = I('post.securityQuestion','','trim');
			$data['answer'] = I('post.answer','','trim');
			$user = M('user');
			$insert = $user->add($data);
			if($insert) {
				$this->success("注册成功","{:U('User/login')}");
			} else {
				$this->error("注册失败");
			}
		}
	}
	public function ajaxRegister() {
		$userName = I('post.userName');
		$User = M('user');
		$count = $User->where("userName='".$userName."'")->find();
		if(is_array($count)) {
			echo "false";
		} else {
			echo "true";
		}
	}
	public function ajaxFind() {
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
	public function checkAnswer() {
		$userName = I('param.userName');
		$User = M('user');
		$users = $User->where("userName='{$userName}'")->find();
		$data = array('question'=>$users['securityquestion'], 'userName'=>$userName);
		$this->assign('question',json_encode($data));
		$this->display();
	}
	public function resetPassword() {
		$userName = I('param.userName');
		$data = array('userName'=>$userName);
		$this->assign('name',json_encode($data));
		$this->display();
	}
	public function answerCheck() {
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
	public function ajaxReset() {
		$userName = I('post.userName');
		$password = I('post.password');
		$User = M('user');
		$updateData['password'] = $password;
		$result = $User->where("userName='{$userName}'")->save($updateData);
		if($result !== false) {
			$data = array('status'=>1);
		} else {
			$data = array('status'=>0);
		}
		echo json_encode($data);
		exit;
	}
}
?>