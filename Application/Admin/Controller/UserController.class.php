<?php
namespace Admin\Controller;
use Think\Controller;
class UserController extends Controller {

    public function qrcode(){
        Vendor('phpqrcode.phpqrcode');
        $id=I('get.id');
        //生成二维码图片 http://localhost/index.php/Home/Login/reg
        $object = new \QRcode();
        $url="http://".$_SERVER['HTTP_HOST'].'/index.php/Home/Login/reg/fid/'.$id;

        $level=3;
        $size=5;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

	public function login(){
        if(IS_POST){
            $name = I('post.name');
            $pwd = I('post.pwd');
            $user = M('user');
            if(!$name || !$pwd){
                echo "<script>alert('用户名或密码不存在');";
                echo "window.history.go(-1);";
                echo "</script>";
            }
            $result= $user->where(array('name'=>$name))->select();
            if($result[0]['password'] ==$pwd){
                $_SESSION['uname']=$name;
                echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/Index/main';</script>";
            }else{
                    echo "<script>alert('用户名或密码不存在');";
                    echo "window.history.go(-1);";
                    echo "</script>";
                }
        }
        $this->display();
    }

    public function logOut(){
        session('uname',null);
        cookie('is_login',null);
        echo "<script>window.location.href = '".__ROOT__."/index.php/Admin/User/login';</script>";
    }

    /**
     * 静态收益 ok
     * 1收益 2充值 3静态提现  4动态体现  5 注册下级 6下单购买 7积分体现 8积分转账 9复投码转账 10分红收益 11 动态收益
     */
    public function crontab(){  //我的团队
        $incomelog =M('incomelog');
//        $res = $incomelog->where(array('addymd'=>date('Y-m-d'),'type'=>10))->select();
//
//        if($res[0]){
//            print_r('今日受益已结算');die;
//        }
        $menber = M("menber");
        $configobj =M('config')->where(array('id'=>2))->select();
        $config2 =$configobj[0]['value'];
        $alluser = $menber->select();
        foreach($alluser as $key=>$val) {
            if($val['dongbag'] > 0){
                // 查询是否有收益
                if(!$this->getusernums($val['uid'],$val['dongbag'])){
                    M("incomelog")->where(array('userid'=>$val['uid'],'state'=>1,'type'=>10))->save(array('state'=>0));
                    $menber->where(array('uid'=>$val['uid']))->save(array('dongbag'=>0));
                }

                if($this->isshang($val['uid'])){
                    continue;
                }

                $todayincome = bcadd($val['dongbag'],$config2,2);
                $data['state'] = 1;
                $data['reson'] = "分红收益";
                $data['type'] = 10;
                $data['addymd'] = date('Y-m-d', time());
                $data['addtime'] = time();
                $data['orderid'] =$val['dongbag'];
                $data['userid'] = $val['uid'];
                $data['income'] = $config2;

                $userinfos = $menber->where(array('uid'=>$val['uid']))->select();
//                $afterincom = bcadd($userinfos[0]['chargebag'],$todayincome,2);
//                $menber->where(array('uid'=>$val['uid']))->save(array('chargebag'=>$todayincome));
                $todayincome = $config2;
                if ($todayincome > 0) {
                    $userinfo = $menber->where(array('uid'=>$val['uid']))->select();
                    $afterincom = bcadd($userinfo[0]['chargebag'],$todayincome,2);
                    $menber->where(array('uid'=>$val['uid']))->save(array('chargebag'=>$afterincom));
                    $this->savelog($data);
                    if($val['fuids'] && $val['fuid']){   // 处理上家
                        $newstrs = substr($val['fuids'],0,strlen($val['fuids'])-1);
                        $array =array_reverse(explode(',',$newstrs));

                        foreach ($array as $k1=>$v1){
                            if($k1){
                                $configx =M('config')->where(array('complan'=>$k1))->find();
                                $data['state'] = 1;
                                $data['reson'] = "动态收益";
                                $data['type'] = 11;
                                $data['addymd'] = date('Y-m-d', time());
                                $data['addtime'] = time();
                                $data['orderid'] =$val['uid'];
                                $data['userid'] = $v1;
                                $incomesnet =bcmul($configx['value'],$todayincome,2);
                                $data['income'] = $incomesnet;
                                if($incomesnet){
                                    if($this->isshang($v1)){
                                        continue;
                                    }
                                    $userinfos = $menber->where(array('uid'=>$v1))->select();
                                    $afterincom = bcadd($userinfos[0]['chargebag'],$incomesnet,2);
                                    $menber->where(array('uid'=>$v1))->save(array('chargebag'=>$afterincom));
                                     $this->savelog($data);
                                }
                            }

                        }
                    }

                }
            }
        }


        echo '成功';
    }

    /**
     * @return int 1大于  0小于 没有到上限
     * 每日收益上限
     */
    public function isshang($uid){
        // 查询今日收益上线
        $todayincomeall = M("incomelog")->where(array('userid'=>$uid,'state'=>1,'addymd'=>date('Y-m-d',time())))->sum('income');
        $config= M("Config")->where(array('id'=>13))->find();
        if($todayincomeall > $config['value']){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * @return int ok
     * 是否有每日收益
     */
    public function getusernums($userid,$num){
        $income =M('incomelog');
        $daycomelogs = $income->where(array('type'=>10,'userid'=>$userid,'state'=>1))->select();
        $daycome =0;
        foreach($daycomelogs as $k=>$v){
            $daycome=bcadd($daycome,$v['income'],2);
        }
        $conf = M("config")->where(array('id'=>1))->select();
        $endmoney = bcmul($conf[0]['value'],$num,2);
        if($daycome>=$endmoney){
            return 0;
        }else{
            return 1;
        }
    }

    private function savelog($data){
        $incomelog =M('incomelog');
        return $incomelog->add($data);
    }


    public function crantabUserIncome(){
        $menber =M('menber');
        $income =M('incomelog');
        if($_GET['uid']){
            $map['uid']  = $_GET['uid'];
        }else{
            $map['uid']  = array('gt',9);
        }
        $result_user = $menber->where($map)->select();
        foreach($result_user as $k=>$v){
            $chargebag = $v['chargebag'];
            $incomebag = $v['incomebag'];
            $allIncome =bcadd($chargebag,$incomebag,2);  // 所有钱包

            $daycomelogs = $income->where(array('state'=>1,'userid'=>$v['uid']))->select();
            $userIncome = 0;
            foreach($daycomelogs as $k1=>$v1){         // 收益
                $userIncome =bcadd($userIncome,$v1['income'],2);
            }
            if($_GET['uid']){
                print_r("每日收益==》".$userIncome);
            }
            $dayoutlogs = $income->where(array('state'=>2,'userid'=>$v['uid']))->select();

            $userOut = 0;                              // 支出
            foreach($dayoutlogs as $k2=>$v2){
                $userOut =bcadd($userOut,$v2['income'],2);
            }
            if($_GET['uid']){
                print_r("<br>总支出==》".$userOut);
            }
            $allIncomesUser =bcsub($userIncome,$userOut,2);      // 总收入
            if($allIncomesUser < 0){
                print_r("userID".$v['uid']."收入日志异常");
            }
            $layout =$allIncomesUser-$allIncome;
            if($layout!=0){
               print_r("用户ID：".$v['uid']."<br>");
               print_r("钱包总额：".$allIncome."<br>");
               print_r("收入总额：".$allIncomesUser."<br><br><br>");
            }
        }
//        print_r($result_user);die;
    }


    function crontabRite(){
        $today = date('m-d',time());
        $isdate = M("Rite")->where(array('date'=>$today))->select();
        if($isdate[0]){
//            $config= M("Config")->where(array('name'=>'daily_income'))->select();
//            M("Rite")->where(array('date'=>$today))->save(array('cont'=>$config[0]['val'],'date'=>$today));
            echo 2;exit();
        }else{
            $config= M("Config")->where(array('id'=>1))->select();
            M("Rite")->add(array('cont'=>$config[0]['value'],'date'=>$today));
            echo 1;exit();
        }
    }
}



 ?>