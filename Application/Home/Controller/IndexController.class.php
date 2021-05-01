<?php


namespace Home\Controller;


use Think\Controller;

class IndexController extends Controller
{

    public function index(){
        $this->display();
    }

    /**
     * 设置商品库存  定时任务完成
     */
    public function add_goods(){
        //设定商品数量
        $strKey="2021_04_28_goods_list";
        //创建连接redis对象
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        //读取数据库，获取商品信息
        $goods=M("goods")->select();
        $redis ->set($strKey,json_encode($goods));
        echo "商品信息设置成功";
    }

    /**
     * 获取redis缓存的商品数据
     */
    public function get_goods(){
        $strKey="2021_04_28_goods_list";
        //创建连接redis对象
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $goods=$redis->get($strKey);
        echo $goods?$goods:"商品数据为空";
    }

    /**
     * 用户提交的数据
     */
    public function user_goods(){
        $strKey="2021_04_28_goods_list";

        //创建连接redis对象
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $user_id=intval(I("user_id"));
        $goods_id=intval(I("goods_id"));
        $num=intval(I("num"));
        if (!$user_id||!$goods_id||!$num){
            echo "参数错误！请检查";
            exit;
        }
        $data=[];
        $data["user_id"]=$user_id;
        $data["goods_id"]=$goods_id;
        $data["num"]=$num;
        //用户入队列
        $redis->lPush("list", json_encode($data));

        $orderKey="2021_04_28_buy_order_".$goods_id;



        $goods=json_decode($redis->get($strKey),true);
        //已经抢购成功的用户
        $user=json_decode($redis->rpop('list'),true);
        $num_str=$redis->llen($orderKey);
        foreach ($goods as $key=>$value){
                if ($value["id"]==$user["goods_id"]){

                    //用户出队
                    //说明是当前商品
                    if ($value["kucun"]>$user["num"]){
                        //说明还有库存
                        $data=[];
                        $trade_no = 'ssh' . $goods_id . '_' . date('YmdHis', time()) . rand_num(5);
                        $data["goods_id"]=$value["id"];
                        $data["trade_no"]=$trade_no;
                        $data["user_id"]=$user_id;
                        $data["num"]=$num;
                        $redis->lpush($orderKey,json_encode($data));
                        $goods[$key]["kucun"]=$value["kucun"]-$user["num"];
                        $redis ->set($strKey,json_encode($goods));
                        echo "抢购成功";
                        exit;
                    }else{
                        echo "抢购失败";
                        exit;
                    }
                }

        }
        echo "抢购失败";
        exit;
    }


    /**
     * 获取抢购成功的数据
     */
    public function get_user_goods(){
        //创建连接redis对象
        $redis = new \Redis();
        $redis->connect('127.0.0.1', 6379);
        $goods=M("goods")->select();
        foreach ($goods as $key=>$value){
            $orderKey="2021_04_28_buy_order_".$value["id"];
            $data=json_decode($redis->LPOP($orderKey),true);
            if (!$data){
                echo "入库结束";
                continue;
            }
            $data["createtime"]=time();
            //写入数据库
            M("order")->add($data);
            echo "入库成功";
           exit;
        }
    }

}