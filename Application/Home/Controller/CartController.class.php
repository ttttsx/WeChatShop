<?php 
namespace Home\Controller;
use Home\Common\Cart;
use Think\Controller;

class CartController extends Controller {
	public function index() {
		$this->assign('item',$_SESSION['cart']);
		$cart = new Cart();
		$this->assign('sumPrice', $cart->getPrice());
		$this->assign('sumItems', $cart->getCnt());
		$this->display();
	}
	public function ajaxGetCnt() {
		$cart = new Cart();
		echo $cart->getCnt();
	}
	public function changeQuantity() {
		$cart = new Cart();
		$itemId = I('param.itemId','','intval');
		$quantity = I('param.quantity','','intval');
		
		//$item = M('item')->field('goods_stock')->find($itemId);
		//if($item['goods_stock']<$quantity) {
		if($quantity>10000000000) {
			$data = array('status'=>0,'msg'=>'该商品库存不足');
		} else {
			$cart->modNum($itemId,$quantity);
			$data = array('status'=>1,'item'=>$cart->getItem($itemId),'sumPrice'=>$cart->getPrice());
		}
		echo json_encode($data);
	}
	public function dropItem() {
		$cart = new Cart();
		$itemId = I('param.itemId','','intval');
		
		$cart->delItem($itemId);
		$data = array('status'=>1,'item'=>$cart->getItem($itemId),'sumPrice'=>$cart->getPrice(),'itenSize'=>$cart->getCnt());
		echo json_encode($data);
	}
	public function delAll() {
		$cart = new Cart();
		$cart->clear();
		$data = array('status'=>1,'msg'=>'清空成功！');
		echo json_encode($data);
	}
}
?>