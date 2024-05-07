<?php
//订单形式
function b2_order_type($id = 0){
	$circle_name = b2_get_option('normal_custom','custom_circle_name');
	return apply_filters('b2_order_type',array(
		'g'=>__('合并付款','b2'),
		'gx'=>__('商城订单','b2'),
		'c'=>__('积分抽奖','b2'),
		'd'=>__('积分兑换','b2'),
		'w'=>__('文章内购','b2'),
		'x'=>__('资源下载','b2'),
		'ds'=>__('文章打赏','b2'),
		'cz'=>sprintf(__('%s充值','b2'),B2_MONEY_NAME),
		'vip'=>__('vip购买','b2'),
		'cg'=>__('积分购买','b2'),
		'v'=>__('视频购买','b2'),
		'verify'=>__('认证付费','b2'),
		'circle_join'=>sprintf(__('付费加入%s','b2'),$circle_name),
'circle_read_answer_pay'=>sprintf(**('付费查看%s 问答','b2'),$circle_name),
'circle_hidden_content_pay'=>**('付费查看隐藏帖子','b2'),
'mission'=>**('签到填坑','b2'),
'coupon'=>**('优惠劵使用','b2'),
'custom'=>$id ? get_the_id($id) : **('自定义支付','b2'),
'infomation_sticky'=>sprintf(**('%s 置顶','b2'),b2_get_option('normal_custom','custom_infomation_name'))
),$id);
}


* 商城订单项
* $order_type //订单类型
* c : 抽奖 ，d : 兑换 ，g : 购买 ，w : 文章内购 ，ds : 打赏 ，x : 资源下载 ，cz : 充值 ，vip : VIP购买 ,cg : 积分购买,
* v : 视频购买,verify : 认证付费,mission : 签到填坑 , coupon : 优惠劵订单,circle_join : 支付入圈 , circle_read_answer_pay : 付费查看提问答案,
* circle_hidden_content_pay : 付费查看隐藏内容，custom ：自定义支付，infomation_sticky ：信息置顶
*
* $order_commodity //商品类型
* 0 : 虚拟物品 ，1 : 实物
*
* $order_state //订单状态
* w : 等待付款 ，f : 已付款未发货 ，c : 已发货 ，s : 已删除 ，q : 已签收 ，t : 已退款