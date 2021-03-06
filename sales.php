<?php


require 'Slim/Slim.php';
require 'connect.php';


\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
//业务员登录
$app->post('/usersign',function ()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $username=$body->username;
    $password1=$body->password;
    $str1=str_split($password1,3);
    $password=null;
    for ($x=0;$x<count($str1);$x++){
        $password.=$str1[$x].$x;
    }
    if($username!=""||$username!=null){
        $selectStaement=$database->select()
            ->from('sales')
            ->where('exist','=',0)
            ->where('telephone','=',$username);
        $stmt=$selectStaement->execute();
        $data=$stmt->fetch();
         if ($data!=null){
             $selectStaement=$database->select()
                 ->from('sales')
                 ->where('password','=',$password)
                 ->where('exist','=',0)
                 ->where('telephone','=',$username);
             $stmt=$selectStaement->execute();
             $data2=$stmt->fetch();
             if($data2!=null){
                 echo json_encode(array('result'=>'0','desc'=>'登录成功','user'=>$data2));
             }else{
                 echo json_encode(array('result'=>'3','desc'=>'密码错误','user'=>''));
             }
         }else{
             echo json_encode(array('result'=>'2','desc'=>'用户不存在','user'=>''));
         }
    }else{
        echo json_encode(array('result'=>'1','desc'=>'用户名为空','user'=>''));
    }

});
//获取该业务员名下的公司
$app->get('/sales_tenant',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $sales_id = $app->request->get("sales_id");
    $database=localhost();
    $arrays=array();
    if($sales_id!=null||$sales_id!=""){
        $selectStatement = $database->select()
            ->from('sales')
            ->where('exist','=',0)
            ->where('id', '=', $sales_id);
        $stmt = $selectStatement->execute();
        $data1 = $stmt->fetch();
        if($data1!=null||$data1!=""){
            $selectStatement = $database->select()
                ->from('tenant')
                ->where('exist','=',0)
                ->where('sales_id', '=', $sales_id);
            $stmt = $selectStatement->execute();
            $data2 = $stmt->fetchAll();
            if($data2!=null||$data2!=""){
                for($x=0;$x<count($data2);$x++){
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('customer_id', '=', $data2[$x]['contact_id']);
                $stmt = $selectStatement->execute();
                $data3 = $stmt->fetch();
                $array['tenant_id']=$data2[$x]['tenant_id'];
       //        $array['user_name']=$data1['user_name'];
               $array['customer_name']=$data3['customer_name'];
               $array['customer_phone']=$data3['customer_phone'];
               $array['begin_time']=$data2[$x]['begin_time'];
               $array['end_time']=$data2[$x]['end_data'];
               $array['company']=$data2[$x]['company'];
               array_push($arrays,$array);
                }
                echo json_encode(array('result'=>'0','desc'=>'','company'=>$arrays));
            }else{
                echo json_encode(array('result'=>'1','desc'=>'该业务员尚未有业务数据','company'=>''));
            }
        }else{
            echo json_encode(array('result'=>'2','desc'=>'业务员不存在','company'=>''));
        }
    }else{
        echo json_encode(array('result'=>'3','desc'=>'业务员id不能为空','company'=>''));
    }
});
// 修改租户信息
$app->put('/tenantchange',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $sales_id=$body->sales_id;
    $tenant_id=$body->tenant_id;
    $customer_name=$body->customer_name;
    $customer_phone=$body->customer_phone;
    $address=$body->address;
    $end_time=$body->end_time;
    $qq=$body->qq;
    $email=$body->email;
    $arrays=array();
    $array1=array();
    $arrays['address']=$address;
    $arrays['end_date']=$end_time;
    $arrays['qq']=$qq;
    $arrays['email']=$email;
    $array1['customer_name']=$customer_name;
    $array1['customer_phone']=$customer_phone;
    if($sales_id!=null||$sales_id!=""){
        if($tenant_id!=null||$tenant_id!="") {
            $selectStatement = $database->select()
                ->from('sales')
                ->where('exist','=',0)
                ->where('id', '=', $sales_id);
            $stmt = $selectStatement->execute();
            $data1 = $stmt->fetch();
            if ($data1 != null || $data1 != "") {
                $selectStatement = $database->select()
                    ->from('tenant')
                    ->where('exist','=',0)
                    ->where('tenant_id', '=', $tenant_id);
                $stmt = $selectStatement->execute();
                $data2 = $stmt->fetch();
                if ($data2 != null || $data2 != "") {
                    $updateStatement = $database->update($arrays)
                        ->table('tenant')
                        ->where('tenant_id', '=', $tenant_id)
                        ->where('exist','=',0)
                        ->where('sales_id', '=', $sales_id);
                    $affectedRows = $updateStatement->execute();
                    $updateStatement = $database->update($array1)
                        ->table('customer')
                        ->where('customer_id', '=', $data2['contact_id'])
                        ->where('exist','=',0);
                    $affectedRows = $updateStatement->execute();
                    echo json_encode(array('result' => '0', 'desc' => '修改信息成功'));
                } else {
                    echo json_encode(array('result' => '1', 'desc' => '该公司不存在'));
                }
            } else {
                echo json_encode(array('result' => '2', 'desc' => '业务员不存在'));
            }
        }else{
            echo json_encode(array('result'=>'3','desc'=>'操作公司为空'));
        }
    }else{
        echo json_encode(array('result'=>'4','desc'=>'业务员id为空'));
    }
});
//统计业务员业务数据
$app->get('/tenantsum',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $sales_id = $app->request->get("sales_id");
    $database=localhost();
    $arrays=array();
    $arrays1=array();
    if($sales_id!=null||$sales_id!=""){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist','=',0)
            ->where('sales_id', '=', $sales_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null||$data2!=""){
            $selectStatement = $database->select()
                ->from('tenant')
                ->where('exist','=',0)
                ->where('sales_id', '=', $sales_id);
            $stmt = $selectStatement->execute();
            $data2 = $stmt->fetchAll();
           for($x=0;$x<count($data2);$x++){
               date_default_timezone_set("PRC");
              $time=$data2[$x]['begin_time'];
              $timestrap=strtotime($time);
               $date=date('m', $timestrap);
                $arrays[$date]++;
           }
           for($y=1;$y<=12;$y++){
               if($y<10){
                   $key='0'.$y;
               }else{
                   $key=$y;
               }
               if($arrays[$key]==null||$arrays[$key]==""){
                   $arrays[$key]=0;
               }
               $arrays1[$y]=$arrays[$key];

           }
            echo json_encode(array('result'=>'0','desc'=>'','count'=>$arrays1));
        }else{
            echo json_encode(array('result'=>'1','desc'=>'该业务员还没有数据','count'=>''));
        }
    }else{
        echo json_encode(array('result'=>'2','desc'=>'业务员id不能为空','count'=>''));
    }
});
//具体显示公司信息
$app->get('/tenantbyid',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $tenant_id = $app->request->get("tenant_id");
    $database=localhost();
    if($tenant_id!=null||$tenant_id!=""){
        $selectStatement = $database->select()
            ->from('tenant')
            ->where('exist','=',0)
            ->where('tenant_id', '=', $tenant_id);
        $stmt = $selectStatement->execute();
        $data2 = $stmt->fetch();
        if($data2!=null||$data2!=""){
            $selectStatement = $database->select()
                ->from('customer')
                ->where('customer_id', '=', $data2['contact_id']);
            $stmt = $selectStatement->execute();
            $data3 = $stmt->fetch();
            $array['customer_name']=$data3['customer_name'];
            $array['customer_phone']=$data3['customer_phone'];
            $array['company']=$data2['company'];
            //$array['begin_time']=$data2['begin_time'];
            $array['end_date']=$data2['end_date'];
            $array['address']=$data2['address'];
            $array['qq']=$data2['qq'];
            $array['email']=$data2['email'];
            echo json_encode(array('result'=>'0','desc'=>'','tenant'=>$array));
        }else{
            echo json_encode(array('result'=>'1','desc'=>'公司不存在','tenant'=>''));
        }
    }else{
        echo json_encode(array('result'=>'2','desc'=>'公司id为空','tenant'=>''));
    }
});
//业务员信息修改
$app->put('/sales',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $sales_id=$body->sales_id;
    $arrays=array();
    foreach($body as $key=>$value){
        if($key!="sales_id"){
            $arrays[$key]=$value;
        }
    }
    if($sales_id!=null||$sales_id!=""){
        $selectStatement = $database->select()
            ->from('sales')
            ->where('exist','=',0)
            ->where('id', '=', $sales_id);
        $stmt = $selectStatement->execute();
        $data1 = $stmt->fetch();
        if($data1!=null||$data1!=""){
            $updateStatement = $database->update($arrays)
                ->table('sales')
                ->where('id', '=', $sales_id);
            $affectedRows = $updateStatement->execute();
            echo json_encode(array('result' => '0', 'desc' => '修改信息成功'));
        }else{
            echo json_encode(array('result' => '2', 'desc' => '该业务员不存在'));
        }
    }else{
        echo json_encode(array('result' => '1', 'desc' => '缺少业务员id'));
    }
});
//获取业务员信息
$app->get('/sales',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $sales_id = $app->request->get("sales_id");
    $database=localhost();
    if($sales_id!=null||$sales_id!=""){
        $selectStatement = $database->select()
            ->from('sales')
            ->where('exist','=',0)
            ->where('id', '=', $sales_id);
        $stmt = $selectStatement->execute();
        $data1 = $stmt->fetch();
        if($data1!=null||$data1!=""){
            echo json_encode(array('result'=>'0','desc'=>'','sales'=>$data1));
        }else{
            echo json_encode(array('result' => '1', 'desc' => '业务员不存在','sales'=>''));
        }
    }else{
        echo json_encode(array('result' => '1', 'desc' => '缺少销售人员id','sales'=>''));
    }
});

$app->post('/addsales',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $database=localhost();
    $body=$app->request->getBody();
    $body=json_decode($body);
    $sales_name=$body->sales_name;
    $sex=$body->sex;
    $card_id=$body->card_id;
    $telephone=$body->telephone;
    $address=$body->address;
    $zip_code=$body->zip_code;
    $qq=$body->qq;
    $weixin=$body->weixin;
    $higherlevel=$body->higherlevel;
    if($sales_name!=null||$sales_name!=""){
         if($sex!=null||$sex!=""){
              if($card_id!=null||$card_id!=""){
                  $selectStatement = $database->select()
                      ->from('sales')
                      ->where('exist','=',0)
                      ->where('card_id', '=', $card_id);
                  $stmt = $selectStatement->execute();
                  $data1 = $stmt->fetch();
                  if($data1==null||$data1==""){
                       if($telephone!=null||$telephone!=""){
                           if($address!=null||$address!=""){
                               if($zip_code!=null||$zip_code!=""){
                                   if($higherlevel!=null||$higherlevel!=""){
                                       $selectStatement = $database->select()
                                           ->from('sales');
                                       $stmt = $selectStatement->execute();
                                       $data2 = $stmt->fetch();
                                       $num=count($data2)+1;
                                       $num2=rand(1000000,10000000000);
                                       $str1=str_split($num2,3);
                                       $password=null;
                                       for ($x=0;$x<count($str1);$x++){
                                           $password.=$str1[$x].$x;
                                       }
                                       $insertStatement = $database->insert(array('exist','sales_name','sex','card_id','telephone','address'
                                         ,'zip_code','qq','weixin','password','higherlevel_username'))
                                           ->into('sales')
                                           ->values(array(0,$sales_name,$sex,$card_id,$telephone,$address,$zip_code,$qq,$weixin,$password
                                           ,$higherlevel));
                                       $insertId = $insertStatement->execute(false);
                                       $arrays['password']=$num2;
                                       echo json_encode(array('result' => '0', 'desc' => '添加成功','sales'=>$arrays));
                                   }else{
                                       echo json_encode(array('result' => '8', 'desc' => '上一级不能为空','sales'=>''));
                                   }
                               }else{
                                   echo json_encode(array('result' => '7', 'desc' => '邮箱不能为空','sales'=>''));
                               }
                           }else{
                               echo json_encode(array('result' => '6', 'desc' => '地址不能为空','sales'=>''));
                           }
                       }else{
                           echo json_encode(array('result' => '5', 'desc' => '电话不能为空','sales'=>''));
                       }
                  }else{
                      echo json_encode(array('result' => '4', 'desc' => '业务员已经存在','sales'=>''));
                  }
              }else{
                  echo json_encode(array('result' => '3', 'desc' => '业务员身份证编号为空','sales'=>''));
              }
         }else{
             echo json_encode(array('result' => '2', 'desc' => '没有选择性别','sales'=>''));
         }
    }else{
        echo json_encode(array('result' => '1', 'desc' => '业务员姓名为空','sales'=>''));
    }
});
$app->run();

function localhost(){
    return connect();
}
?>
