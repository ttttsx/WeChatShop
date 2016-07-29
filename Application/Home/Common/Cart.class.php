<?php
namespace Home\Common;

class Cart {
	/*
	 * 构造函数
	 */
	public function __construct() {
		if(!isset($_SESSION['cart'])) {
			$_SESSION['cart'] = array();
		}
	}
	/*
	 * 添加商品
	 */
	public function addItem($id,$name,$price,$num,$img) {
		if(isset($_SESSION['cart'][$id])) {
			$this->incNum($id,$num);
			return 1;
		}
		
		$item = array();
		$item['id'] = $id;
		$item['name'] = $name;
		$item['price'] = $price;
		$item['num'] = $num;
		$item['img'] = $img;
		$_SESSION['cart'][$id] = $item;
	}
	/*
	 * 修改数量
	 */
	public function modNum($id,$num=1) {
		if(!isset($_SESSION['cart'][$id])) {
			return false;
		}
		$_SESSION['cart'][$id]['num'] = $num;
	}
	/*
	 * 增加数量
	 */
	public function incNum($id,$num=1) {
		if(isset($_SESSION['cart'][$id])) {
			$_SESSION['cart'][$id]['num'] += $num;
		}
	}
	/*
	 * 减少数量
	 */
	public function decNum($id,$num=1) {
		if(isset($_SESSION['cart'][$id])) {
			$_SESSION['cart'][$id]['num'] -= $num;
		}
		if($_SESSION['cart'][$id]['num']<1) {
			$this->delItem($id);
		}
	}
	/*
	 * 删除商品
	 */
	public function delItem($id) {
		unset($_SESSION['cart'][$id]);
	}
	/*
	 * 获取单个商品信息
	 */
	public function getItem($id) {
		return $_SESSION['cart'][$id];
	}
	/*
	 * 查询商品种类数
	 */
	public function getCnt() {
		return count($_SESSION['cart']);
	}
	/*
	 * 获取商品个数
	 */
	public function getNum() {
		if($this->getCnt() == 0) {
			return 0;
		}
		$sum = 0;
		$data = $_SESSION['cart'];
		foreach ($data as $item) {
			$sum += $item['num'];
		}
		return $sum;
	}
	/*
	 * 获取商品总金额
	 */
	public function getPrice() {
		if($this->getCnt() == 0) {
			return 0;
		}
		$price = 0.00;
		$data = $_SESSION['cart'];
		foreach ($data as $item) {
			$price += $item['num'] * $item['price'];
		}
		return sprintf("%01.2f",$price);
	}
	
	/*
	 * 清空购物车
	 */
	public function clear() {
		$_SESSION['cart'] = array();
	}
}

?>