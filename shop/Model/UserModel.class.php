<?php
//模型
namespace Model;
use Think\Model;

class UserModel extends Model{
    // 字段映射定义
    // 把form表单中自定义字段，变为数据表合法字段
    protected $_map             =   array(
        'name' => 'user_name',
        'password' => 'user_pwd',
        'email' => 'user_email',
    );  

    /****自动完成(填充字段信息)****/
    protected $_auto = array(
        array('add_time','time',1,'function'), //添加记录完成add_time的填充
        array('user_pwd','md5',1,'function'), //添加记录完成user_pwd加密的填充

    );
    /****自动完成(填充字段信息)****/

    //瞻前顾后机制(顾后)
    protected function _after_insert($data,$options) {
        //判断当前的动作为“注册”并发送邮件
        if($_POST['act']=='regist'){

            //生成校验码
            $code = md5(uniqid()); //生成一个唯一的校验码信息
            $this -> setField(array('user_id'=>$data['user_id'],'user_check_code'=>$code));//更新校验码到会员记录

            //具体邮件发送
            //sendMail(注册邮箱，title，content);
            //$link = "http://web.php41.com/index.php/Home/User/jihuo/user_id/".$data['user_id']."/checkcode/".$code;
            $link = rtrim(C('SITE_URL'),'/').U('User/jihuo',array('user_id'=>$data['user_id'],'checkcode'=>$code));
            sendMail($data['user_email'],'会员注册激活',"请点击一下超链接，激活您的账号：<a href='".$link."' target='_blank'>".$link."</a>");
        }
    }

    // 更新数据前的回调方法
    protected function _before_update(&$data,$options) {}
    // 更新成功后的回调方法
    protected function _after_update($data,$options) {}
}
