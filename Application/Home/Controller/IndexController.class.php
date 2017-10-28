<?php
namespace Home\Controller;
use Think\Controller;
header('content-type:text/html;charset=utf-8');
class IndexController extends CommonController {
//	public function _initialize(){
//		if($_GET['openid']){
//			$menber =M('menber');
//			$user=$menber->where(array('openid'=>$_GET['openid']))->select();
//			S('name',$user[0]['name']);
//			S('userid',$user[0]['id']);
//			S('nickname',$user[0]['nickname']);
//		}
//	}
   //主页
	public function index(){
		$article =M('article');
		$intro= $article->order('aid DESC')->where(array('type'=>1))->select();
		$this->assign('intro',$intro[0]);
		$this->display();
	}

    //列表
    public function shop(){
        $article =M('product');
        $intro= $article->order('id DESC')->where(array('type'=>1))->select();
        $this->assign('intro',$intro);
        $this->display();
    }

    //详情
    public function detail(){
        $article =M('product');
        $intro= $article->where(array('id'=>$_GET['id']))->find();
        if($_POST['num']){
            $isbuy = M("orderlog")->where(array('userid'=>session('uid')))->find();
            if($isbuy['userid']){
                echo "<script>alert('您已经购买过商品');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
                echo "</script>";
                exit;
            }

            $users = M("menber")->where(array('uid'=>session('uid')))->find();

            if($isbuy['userid']){
                echo "<script>alert('您已经购买过商品');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
                echo "</script>";
                exit;
            }

            $pro = M("product")->where(array('id'=>$_GET['id']))->find();

            if($users['chargebag'] < $pro['price']){
                echo "<script>alert('积分余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
                echo "</script>";
                exit;
            }

            $order['userid'] =session('uid');
            $order['productid'] =$pro['id'] ;
            $order['productname'] =$pro['name'];
            $order['productmoney'] =$pro['price'];
            $order['states'] = 1;
            $order['orderid'] = $_POST['num'];
            $order['addtime'] = time();
            $order['addymd'] = date("Y-m-d",time());
            $order['num'] = $_POST['num'];
            $order['prices'] =$pro['price'];
            $order['totals'] =$pro['price'];
            $order['option'] =$_POST['addr'].','.$_POST['tel'].','.$_POST['name'].','.$_POST['youbian'];
            if($_POST['num'] > 0){
                M("orderlog")->add($order);
            }

            $income =M('incomelog');
            $data['type'] =6;
            $data['state'] =2;
            $data['reson'] ='下单购买';
            $data['addymd'] =date('Y-m-d',time());
            $data['addtime'] =time();
            $data['orderid'] =session('uid');
            $data['userid'] =session('uid');
            $data['income'] =$pro['price'];
            if($pro['price'] > 0){
                $income->add($data);
            }
            $menber = M("menber");
            $userinfo = $menber->where(array('uid'=>session('uid')))->find();
            $chargebag = bcsub($userinfo['chargebag'],$pro['price'],2);
            $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$chargebag));

            if((int)$pro['price'] == 100){
                $dongbag =bcadd($userinfo['dongbag'],1);
                $menber->where(array('uid'=>session('uid')))->save(array('dongbag'=>$dongbag));
            }
            if((int)$pro['price'] == 200){
                $dongbag =bcadd($userinfo['dongbag'],2);
                $menber->where(array('uid'=>session('uid')))->save(array('dongbag'=>$dongbag));
            }


            echo "<script>alert('购买成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
            echo "</script>";
            exit;
        }
        $this->assign('intro',$intro);
        $this->display();
    }


    /**
     * 公司简介
     */
    public function introduce(){
        $article =M('article');
        $intro= $article->order('aid DESC')->where(array('type'=>5))->select();
        $this->assign('intro',$intro[0]);
        $this->display();
    }

    /**
     * 公告
     */
    public function advertising(){
        $article =M('article');
        $intro= $article->where(array('aid'=>$_GET['id']))->select();
        $this->assign('intro',$intro[0]);
        $this->display();
    }

    /**
     * 值班团队
     */
    public function gruop(){
        $article =M('article');
        $intro= $article->where(array('aid'=>$_GET['id']))->select();
        $this->assign('intro',$intro[0]);
        $this->display();
    }

    /**
     * 分析专家
     */
    public function professor(){
        $article =M('article');
        $intro= $article->where(array('aid'=>$_GET['id']))->select();
        $this->assign('intro',$intro[0]);
        $this->display();
    }


	//我的产品
	public function financial(){
		$orderlog =M('orderlog');
		$result  = $orderlog->join('p_product ON p_orderlog.productid=p_product.id')->where(array('userid'=>session('uid')))->select();
		foreach($result as $k=>$v){
			if($v['states']==0){
				$v['total'] = $v['prices'] *$v['num'];
				$data['wait'][] =$v;
			}
			if($v['states']==1){
				$v['total'] = $v['prices'] *$v['num'];
				$data['coming'][] =$v;
			}
			if($v['states']==2){
				$v['total'] = $v['prices'] *$v['num'];
				$data['comoever'][] =$v;
			}
		}
		$this->assign('res',$data);
		$this->display();
	}



    public function K(){
        $rite =M("rite")->order("id desc")->limit(7)->select();
        $this->assign('seven',$rite);
        $this->display();
    }

    public function choose(){
        $log = M('incomelog')->order('id DESC')->where(array('userid'=>session('uid'),'type'=>2))->select();
        $this->assign('log',$log);
        $this->display();
    }

    public function qrcode(){
        Vendor('phpqrcode.phpqrcode');
        $id=I('get.id');
        //生成二维码图片
        $object = new \QRcode();
        $url="http://".$_SERVER['HTTP_HOST'].'/index.php/Admin/Article/editearticle/id/'.$id;//网址或者是文本内容

        $level=3;
        $size=5;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $object->png($url, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }

    public function gongPai(){
        echo "<script>alert('显示公排暂未开放，敬请期待');";
        echo "window.location.href='".__ROOT__."/index.php/Home/Index/index';";
        echo "</script>";
        exit;

        $orderlog = M('orderlog');
        $allorder = $orderlog->where(array('type'=>2,'userid'=>session('uid')))->order('logid ASC')->select();
//        print_r($allorder);die;
        $this->assign('res',$allorder[0]);
        $this->display();
    }

    public function gongpai_buy(){
        if($_POST['num']){
            $menber = M('menber');
            $userinfo = $menber->where(array('uid'=>session('uid')))->select();
            if($_POST['num'] > $userinfo[0]['chargebag']){
                echo "<script>alert('充值钱包余额不足');";
                echo "window.location.href='".__ROOT__."/index.php/Home/Index/gongpai_buy';";
                echo "</script>";
                exit;
            }
            $left =bcsub( $userinfo[0]['chargebag'],$_POST['num'],2);
            $menber->where(array('uid'=>session('uid')))->save(array('chargebag'=>$left));

            $orderlog = M('orderlog');
            $allorder = $orderlog->where(array('type'=>2))->order('logid DESC')->select();
            $allcount =count($allorder);
            if($allorder[0]){
                $bianhao = $allorder[0]['bianhao'] + 1;
                $num = $allorder[0]['num'] + 1;
                $ceng = $this->getceng($allcount) ;

                // 处理层级关系
                $isaddcen = $this->isaddceng($allcount);
                if($isaddcen){
                    foreach ($allorder as $k=>$v){
                        $afterceng =$v['ceng']+1;
                        $orderlog->where(array('logid'=>$v['logid']))->save(array('ceng'=>$afterceng));
                        $fengs = bcpow(2,$afterceng) ;
                        $fengs = bcmul (4,$fengs) ;
                        if($v['userid']){   // 积分增加
                            $newuser = $menber->where(array('uid'=>$v['userid']))->select();
                            $newfeng = $fengs;
                            $dongbag = $newuser[0]['dongbag'] + $fengs;
                            $menber->where(array('uid'=>$v['userid']))->save(array('jifeng'=>$newfeng,'dongbag'=>$dongbag));

                            // 收入日志
                            $income =M('incomelog');
                            $data['type'] = 11 ;
                            $data['state'] = 1 ;
                            $data['reson'] ='公排收益';
                            $data['addymd'] =date('Y-m-d',time());
                            $data['addtime'] =time();
                            $data['orderid'] =session('uid');
                            $data['userid'] = $v['userid'];
                            $data['income'] = $fengs;
                            $income->add($data);

                        }
                    }
                }
            }else{
                $ceng = 1;
                $bianhao = 10000;
                $num =1;
            }

            // 下单
            $orderdata['userid'] =session('uid');
            $orderdata['productname'] ='购买公排';
            $orderdata['productmoney'] =$_POST['num'];
            $orderdata['states'] = 1 ;
            $orderdata['orderid'] =$bianhao;
            $orderdata['addtime'] =time();
            $orderdata['num'] = $num ;
            $orderdata['prices'] =$_POST['num'];
            $orderdata['addymd'] =date('Y-m-d',time());
            $orderdata['type'] =  2;
            $orderdata['ceng'] = 0;
            $orderdata['bianhao'] = $bianhao;
            $orderdata['totals'] =$_POST['num'];
            $logid = $orderlog->add($orderdata);

            // 收入日志
            $income =M('incomelog');
            $data['type'] =6;
            $data['state'] =2;
            $data['reson'] ='购买公排';
            $data['addymd'] =date('Y-m-d',time());
            $data['addtime'] =time();
            $data['orderid'] =$logid;
            $data['userid'] = session('uid');
            $data['income'] = $_POST['num'];
            $income->add($data);

            echo "<script>alert('购买成功');";
            echo "window.location.href='".__ROOT__."/index.php/Home/Index/gongpai';";
            echo "</script>";
            exit;
        }

        $config = M('config')->where(array('id'=>17))->select();
        $this->assign('config',$config[0]['value']);
        $this->display();
    }

    private function isaddceng($cen){
        if(in_array($cen,array(1,3,7,15,31,63,127,255,511))){
            return 1;
        }else{
            return 0;
        }
    }



    private function getceng($count){
        if($count ==0 ){     // 1
            return 1;
        }elseif ($count >=1 && $count <3){   // 2
            return 2;
        }elseif ($count >=3 && $count <7){   // 3
            return 3;
        }elseif ($count >=7 && $count <15){  // 4
            return 4;
        }elseif ($count >=15 && $count <31){  // 5
            return 5;
        }elseif ($count >=31 && $count <63){   // 6
            return 6;
        }elseif ($count >=63 && $count <127 ){  // 7
            return 7;
        }elseif ($count >=127 && $count <255){  // 8
            return 8;
        }elseif ($count >=255 && $count <511){  // 9
            return 9;
        }elseif ($count >=511 && $count <1024){     // 10
            return 10;
        }
    }

    // 1首页 2公告 3值班团队 4分析专家 5公司简介  gruop

    public function types(){
        $type = isset($_GET['type']) ? $_GET['type']: 2 ;
        if($type ==2){
            $title = "公告列表";
        }elseif ($type == 3){
            $title = "值班团队";
        }elseif ($type == 4){
            $title = "分析专家";
        }
        $article =M('article');
        $intro= $article->order('aid DESC')->where(array('type'=>$type))->select();
        $this->assign('title',$title);
        $this->assign('res',$intro);
        $this->display();
    }

    /**
	 * 获取当前页面完整URL地址
	 */
	private function get_url() {
		$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
		$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
		$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
		$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
		return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
	}


	private function getlists($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}

	private function curlget($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
//		执行并获取HTML文档内容
		$output = curl_exec($ch);
		//释放curl句柄
		curl_close($ch);
		return json_decode($output, true);
	}
}