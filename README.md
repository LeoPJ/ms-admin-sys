# MySQL表

jw_goods表用来给店家预先设定商品的列表与库存，其中`id`字段为商品ID，`kucun`字段为库存数量。

jw_order表用来存放已抢购成功的用户，其中`id`字段为行数，自增序列自动生成，`goods_id`为商品ID，`user_id`为用户ID，`num`为该次订单用户抢购的商品数量，`trade_no`字段为订单号，结合抢购时间自动生成，`createtime`字段为订单创建时间，存储的是时间戳格式。

# 思路步骤：

1. 基于jw_goods表中的库存，创建redis商品库存队列。通过访问/刷新 http://localhost/ms-admin-sys/Home.php/index/add_goods 实现初始化。
2. http://localhost/ms-admin-sys/Home.php/index/get_goods 可以查看当前redis里的库存信息。
3. http://localhost/ms-admin-sys/Home.php/index/index 为用户抢购商品的页面，用户ID自定义输入，商品ID需要输入库存里已有商品的对应ID，抢购的数量可选，点击提交完成抢购，成功与否都会返回提示信息。
4. 判断抢购成功与否的逻辑：先从redis的商品库存队列中查询对应商品的剩余库存，判断抢购数量是否小于当前库存，若小于等于则抢购成功，否则抢购失败。
5. 抢购结束后需要访问并刷新 http://localhost/ms-admin-sys/Home.php/index/get_user_goods 实现把抢购信息存入jw_order表中，若抢购成功的有n件，则需要刷新n次保证所有信息存入MySQL。
6. 抢购结束后从jw_order表中按照`createtime`字段大于开始抢购时间来筛选出抢购成功的订单信息。
7. 刷新第2步里的链接可查看抢购结束后的库存剩余情况，刷新第1步里的链接重置所有信息，进行新一轮抢购。

# 实现

* 利用ThinkPHP框架
* URL模式选用默认的PATHINFO模式
* 功能实现的核心代码为ms-admin-sys\Application\Home\Controller\IndexController.class.php
* 抢购页面代码为ms-admin-sys\Application\Home\View\Index\index.html
* MySQL配置文件为ms-admin-sys\Application\Common\Conf\config.php，数据表初始化参考ms-admin-sys\ms-admin-sys.sql
* 抢购商品的访问规则实例： http://localhost/ms-admin-sys/Home.php/index/user_goods?user_id=123&goods_id=4&num=1

# 压力测试

利用apache自带的压测工具ab.exe，在cmd输入

```bash
ab.exe -n 100 -c 100 "http://localhost/ms-admin-sys/Home.php/index/user_goods?user_id=123&goods_id=4&num=1"
```

![image-20210430225512897](http://img.cuper.top/html20210430225524.png)

从“Failed requests: 0”看出压测成功。查询MySQL中该次抢购的订单信息，没有出现超卖：

![image-20210430225833156](http://img.cuper.top/html20210430225835.png)