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
            ->where('user_name','=',$username);
        $stmt=$selectStaement->execute();
        $data=$stmt->fetch();
         if ($data!=null){
             $selectStaement=$database->select()
                 ->from('sales')
                 ->where('password','=',$password)
                 ->where('exist','=',0)
                 ->where('user_name','=',$username);
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
    $sales_id = $app->request->headers->get("sales_id");
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
            $data2 = $stmt->fetch();
            if($data2!=null||$data2!=""){
                $selectStatement = $database->select()
                    ->from('customer')
                    ->where('customer_id', '=', $data2['contact_id']);
                $stmt = $selectStatement->execute();
                $data3 = $stmt->fetch();
                for($x=0;$x<count($data2);$x++){
                    $array['tenant_id']=$data2['tenant_id'];
       //        $array['user_name']=$data1['user_name'];
               $array['customer_name']=$data2['customer_name'];
               $array['customer_phone']=$data2['customer_phone'];
               $array['begin_time']=$data2['begin_time'];
               $array['end_time']=$data2['end_data'];
               $array['company']=$data3['company'];
               array_push($arrays,$array);
                }
            }else{
                echo json_encode(array('result'=>'1','desc'=>'该业务员尚未有公司','company'=>$arrays));
            }
        }else{
            echo json_encode(array('result'=>'1','desc'=>'业务员不存在','company'=>''));
        }
    }else{
        echo json_encode(array('result'=>'1','desc'=>'业务员id不能为空','company'=>''));
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
    $arrays=array();
    foreach($body as $key=>$value){
        if($key!="tenant_id"&&$key!="company"&&$key!="business_l_p"){
            $array[$key]=$value;
        }
    }
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
                    $updateStatement = $database->update($array)
                        ->table('tenant')
                        ->where('tenant_id', '=', $tenant_id)
                        ->where('exist','=',0)
                        ->where('sales_id', '=', $sales_id);
                    $affectedRows = $updateStatement->execute();
                    echo json_encode(array('result' => '0', 'desc' => '修改信息成功'));
                } else {
                    echo json_encode(array('result' => '2', 'desc' => '该公司不存在'));
                }
            } else {
                echo json_encode(array('result' => '2', 'desc' => '业务员不存在'));
            }
        }else{
            echo json_encode(array('result'=>'2','desc'=>'操作公司为空'));
        }
    }else{
        echo json_encode(array('result'=>'2','desc'=>'业务员id为空'));
    }
});
//统计业务员业务数据
$app->get('/tenantsum',function()use($app){
    $app->response->headers->set('Content-Type','application/json');
    $sales_id = $app->request->headers->get("sales_id");
    $database=localhost();
    $arrays=array();
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
            $data2 = $stmt->fetch();
            $sum=0;
           for($x=1;$x<=12;$x++){
              $time=$data2['begin_time'];
              $timestrap=strtotime($time);
              for($y=0;$y<count($data2);$y++) {
                  if (date('m', $timestrap) == $x) {
                      $sum++;
                  }
              }
              $array=$sum;
              array_push($arrays,$array);
           }
            echo json_encode(array('result'=>'1','desc'=>'该业务员还没有数据','count'=>$arrays));
        }else{
            echo json_encode(array('result'=>'1','desc'=>'该业务员还没有数据','count'=>''));
        }
    }else{
        echo json_encode(array('result'=>'1','desc'=>'业务员id不能为空','count'=>''));
    }
});

$app->run();

function localhost(){
    return connect();
}
?>
