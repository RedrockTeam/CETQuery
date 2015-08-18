<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        echo "this is not allowed to access";
    }

    public function find(){
        if(!IS_POST) {
            return $this->ajaxReturn(array(
                "status" => "-2",
                "info" => "invalid method"
            ));
        }
        $_username = I('post.username');
        $_id = I('post.id');
        if(empty($_username) || empty($_id)){
            return $this->ajaxReturn(array(
                "status" => "-1",
                "info" => "invalid data"
            ));
        }

        $ret = $this->proxy($_id, $_username);

        if(!$ret) {
            $ret = $this->proxy2($_id, $_username);
        }

        if(!$ret){
            return $this->ajaxReturn(array(
                "status" => "-3",
                "info" => "invalid return"
            ));
        }
        return $this->ajaxReturn(array(
            "status" => "0",
            "info" => "Success",
            "data" => $ret
        ));
    }

    private function proxy($id, $name) {
        $username = urlencode(iconv('UTF-8', 'GB2312', $name)); //转成GB2312
        $map ['id'] = $id;
        $map ['name'] = $username;
        $returnStr = curl_post_contents('http://cet.99sushe.com/find', formUrlEncoded($map), "http://cet.99sushe.com/");
        $arr = explode(',', iconv('GB2312', 'UTF-8', $returnStr)); //转回UTF8
        if(count($arr) == 1) return false;
        return array(
            "name" => $arr[6],
            "school" => $arr[5],
            "tid" => $id,
            "grade" => array(
                "listening" => $arr[1],
                "reading" => $arr[2],
                "writing" => $arr[3],
                "all" => $arr[4]
            )
        );
    }

    public function find2(){
        if(!IS_POST) {
            return $this->ajaxReturn(array(
                "status" => "-2",
                "info" => "invalid method"
            ));
        }
        $_username = I('post.username');
        $_id = I('post.id');
        if(empty($_username) || empty($_id)){
            return $this->ajaxReturn(array(
                "status" => "-1",
                "info" => "invalid data"
            ));
        }

        $arr = $this->proxy2($_id, $_username);
        if(count($arr) == 1){
            return $this->ajaxReturn(array(
                "status" => "-3",
                "info" => "invalid return"
            ));
        }
        return $this->ajaxReturn(array(
            "status" => "0",
            "info" => "Success",
            "data" => $arr
        ));
    }

    private function proxy2($id, $name){
        $map ['zkzh'] = $id;
        $map ['xm'] = urlencode($name);
        $returnStr = curl_post_contents('http://www.chsi.com.cn/cet/query?'.formUrlEncoded($map), '', "http://www.chsi.com.cn/cet/");
//        echo $returnStr;
        $tableArr = array();
        preg_match_all('/<table border[\s\S]+<\/table>/', $returnStr, $tableArr);
        $table = $tableArr[0][0];

        $arr = array();
        $numbers = array();
        preg_match_all('/span class=\"color666\">[^>]+<\/span>([^<]+)</', $table, $arr);
        $numbers['listening'] = trim($arr[1][0]);
        $numbers['reading'] = trim($arr[1][1]);
        $numbers['writing'] = trim($arr[1][2]);
        $numbers['all'] = $numbers['listening'] + $numbers['reading'] + $numbers['writing'];

        $data = array(
            "name" => $name,
            "school" => $this->getData('学校', $table),
            "type" => $this->getData('考试类别', $table),
            "tid" => $id,
            "time" => $this->getData('考试时间', $table),
            "grade" => $numbers
        );
        return $data;
    }

    /**
     *  $type [姓名,学校,考试类别,准考证号,考试时间]
     */
    private function getData($type, $raw){
        $arr = array();
        preg_match('/<th[^>]*>'.$type.'：<\/th>[^<]+<td[^>]*>([^<]+)<\/td>/', $raw, $arr);
        return $arr[1];
    }

    private function get_ticket($province, $school, $name, $type = 1){ //type 1 = cet4, 2 = cet6
        if($type == 1)
            return shell_exec("python CetTicket/cet4.py $province $school $name");
        return shell_exec("python CetTicket/cet6.py $province $school $name");
    }
    public function no_ticket_query(){//input province school name type
        //optput result
        echo $this->get_ticket("重庆", "重庆邮电大学", "李青", $type=1);
    }

    public function noTicketQuery(){
        if (stripos($_SERVER['HTTP_REFERER'], 'http://115.159.64.43/CETQuery') !== false) {
            return die('');
        }
        $user = I('post.username');
        $province = I('post.province');
        $school = I('post.school');
        $type = I('post.type', 1, 'int');
        if(!$user || !$province || !$school) return $this->ajaxReturn(array(
            "status" => -1,
            "info" => "信息不完整"
        ));

        $tid = (int)$this->get_ticket($province, $school, $user, $type); //强制类型转换去掉\n

        if(!$tid)return $this->ajaxReturn(array(
            "status" => -5,
            "info" => "查无此人"
        ));


        $data = $this->proxy($tid, $user);
        if(!$data) {
            $data = $this->proxy2($tid, $user);
        }

        $this->ajaxReturn(array(
            "status" => "0",
            "tid" => $tid,
            "data" => $data
        ));
    }
}