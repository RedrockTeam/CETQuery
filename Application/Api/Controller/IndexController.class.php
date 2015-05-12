<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        echo "this is not allowed to access";
    }

    public function find(){
        if(!IS_POST) {
            return $this->ajaxReturn([
                "status" => "-2",
                "info" => "invalid method"
            ]);
        }
        $_username = I('post.username');
        $_id = I('post.id');
        if(empty($_username) || empty($_id)){
            return $this->ajaxReturn([
                "status" => "-1",
                "info" => "invalid data"
            ]);
        }

        $arr = $this->proxy($_id, $_username);
        if(count($arr) == 1){
            return $this->ajaxReturn([
                "status" => "-3",
                "info" => "invalid return"
            ]);
        }
        return $this->ajaxReturn([
            "status" => "0",
            "info" => "Success",
            "data" => $arr
        ]);
    }

    private function proxy($id, $name) {
        $username = urlencode(iconv('UTF-8', 'GB2312', $name)); //转成GB2312
        $map ['id'] = $id;
        $map ['name'] = $username;
        $returnStr = curl_post_contents('http://cet.99sushe.com/find', formUrlEncoded($map), "http://cet.99sushe.com/");
        return explode(',', iconv('GB2312', 'UTF-8', $returnStr)); //转回UTF8
    }
}