<?php
namespace Api\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function _initialize() {
        if (stripos($_SERVER['HTTP_REFERER'], '115.159.64.43') === false && stripos($_SERVER['HTTP_REFERER'], 'cet.lcl.deadsoul.net') === false) {
            return die('invaild');
        }
    }

    public function index(){
        echo "this is not allowed to access";
    }

    public function find() {
        $name = I('post.username');
        $ticket = I('post.id');
        if (empty($name) || empty($ticket)) {
            return $this->ajaxReturn([
                "status" => -404,
                "info" => "no name or ticket"
            ]);
        }

        $ticket = (int)$ticket;
        $name = str_replace(" ", "", $name);
        $ret = shell_exec("python CetTicket/cet4.py $ticket $name");
        $ret = json_decode($ret, true);

        if (empty($ret['error'])) {
            $data = [
                "status" => 0,
                "info" => "Success",
                "data" => [
                    "name" => $ret['name'],
                    "school" => $ret['school'],
                    "tid" => $ret['ticket'],
                    "grade" => [
                        "all" => $ret['Total'],
                        "writing" => $ret['Writing'],
                        "listening" => $ret['Listening'],
                        "reading" => $ret['Reading']
                    ]
                ]
            ];
            $this->ajaxReturn($data);
        } else {
           $this->ajaxReturn([
               "status" => -404,
               "info" => "not found"
           ]);
        }
     }


    public function noTicketQuery () {
        $name = I('post.username');
        $province = I('post.province');
        $school = I('post.school');
        $type = I('post.type');

        if (empty($name) || empty($province) || empty($school) || empty($type)) {
            return $this->ajaxReturn([
                "status" => -404,
                "info" => "信息不全"
            ]);
        }

        $name = str_replace(" ", "", $name);
        $ret = $type == 1 ?
            shell_exec("python CetTicket/cet4.py $province $school $name") :
            shell_exec("python CetTicket/cet6.py $province $school $name");

        $ret = json_decode($ret, true);

        if ($ret['name'] !== null) {
            $data = [
                "status" => 0,
                "info" => "Success",
                "data" => [
                    "name" => $ret['name'],
                    "tid" => $ret['ticket'],
                    "school" => $ret['school'],
                    "grade" => [
                        "all" => $ret['Total'],
                        "writing" => $ret['Writing'],
                        "listening" => $ret['Listening'],
                        "reading" => $ret['Reading']
                    ]
                ]
            ];
            $this->ajaxReturn($data);
        } else {
            $this->ajaxReturn([
                "status" => -404,
                "info" => "not found"
            ]);
        }
    }

}