<?php
namespace Admin\Controller;
use Think\Controller;

class PublicController extends Controller {

    public function login(){
	  if(IS_POST){ 
        $admin = I('post.');
        $rs = D('Admin', 'Service')->login($admin);
		if (!$rs['status']) {
            $this->error($rs['data']);
        }
		$this->success('登录成功，正在跳转...',"admin.php"  ,1);	//登录成功跳转到管理画面
	  }
	  else{
		  $this->display();
	  }
  
    }

	public function verify(){
	    ob_clean();
		$config =    array(
			'fontSize'    =>    20,    // 验证码字体大小
		    'length'      =>    4,     // 验证码位数
		    'imageH'	  => 	35,
		    'useNoise'    =>    false, // 关闭验证码杂点
		);
		$verify = new \Think\Verify($config);
		$verify->codeSet = '0123456789';
		$verify->entry();
	}
	
	public function logout() {
		D('Admin', 'Service')->logout();
		$this->redirect('Public/login');
	}
	

	

	public function changepwd() {
		if(IS_POST){
	    $password=I('post.password');
		$map = array();
		if(I('post.password')!=I('post.repassword'))
		{
			$data['statusCode']=300;
			$data['message']='两次输入密码不一致！';
		}
		$map['password'] = md5(md5((I('post.oldpassword'))));
		$map['id'] = session('uid');
		$User = M("user");
		if (!$User->where($map)->field('id')->find()) {
			//echo $User->getLastSql();
			$data['statusCode']=300;
			$data['message']='旧密码不符或者用户名错误！';
			
		} else {
		   // echo $User->getLastSql();
			if (empty($password) || strlen($password) < 5) {
				$data['statusCode']=300;
			    $data['message']='密码长度必须大于6个字符！';
			
			}else{
			$User->password =md5(md5(($password)));
			$User->save();
			$data['statusCode']=200;
			$data['message']='密码修改成功！';
			$data['tabid']=$_REQUEST['navTabId'];
			$data['closeCurrent']='true';
			$data['forward']='';
			$data['forwardConfirm']='';
			}
			
		}
		$this->dwzajaxReturn($data);
		}else{
		  $this->display();	
		}
	}
	
	public function selectuser() {

		$demo=M("User");
		$list=$demo->table(C('DB_PREFIX')."user a")
		->join("left join ".C('DB_PREFIX')."org b ON (a.orgid=b.id)")
		->join("left join ".C('DB_PREFIX')."dep c ON (a.depid=c.id)")
		->field("a.id as userid,a.username, a.password, a.sex,  a.tel,a.ins, a.phone, a.fax, a.email,  a.status, a.logintime, a.loginip, a.logins,a.depid, c.name as depname, a.orgid,b.name as orgname")
		->order("a.id asc")
		->select();
		 
		$this->assign('list',$list);
		

		$this->display();
	}
	
	public function changeinfo() {
		$id=session('uid');
		$rs=M('user')->find($id);
		if(IS_POST){
		    
			M('user')->save(I('post.'));
			
			$data['statusCode']=200;
			$data['message']='资料修改成功！';
			$data['tabid']=$_REQUEST['navTabId'];
			$data['closeCurrent']='true';
			$data['forward']='';
		   $this->dwzajaxReturn($data);
		}else{
		  $this->assign('id', $id);
		  $this->assign('Rs', $rs);
		  $this->display();	
		}
	}
	
    protected function dwzajaxReturn($data, $type='') {
        
		$udata['id']=session('uid');
        $udata['update_time']=date("Y-m-d H:i:s",time());
        
        $Rs=M("user")->save($udata);
        //echo M("user")->getLastSql();
        $dat['username'] = session('uid');
        $dat['content'] = $data['message'];
		$dat['os']=$_SERVER['HTTP_USER_AGENT'];
        $dat['url'] = U();
        $dat['addtime'] = date("Y-m-d H:i:s",time());
        $dat['ip'] = get_client_ip();
        M("log")->add($dat);
        
        
        $result = array();
        $result['statusCode'] = $data['statusCode'];
        $result['tabid'] = $data['tabid'];
        $result['dialogid'] = $data['dialogid'];
        $result['closeCurrent'] = $data['closeCurrent'];
        $result['message'] = $data['message'];
        $result['forward'] = $data['forward'];
		$result['forwardConfirm']=$data['forwardConfirm'];
		$result['divid']=$data['divid'];

       //var_dump($result);
        if (empty($type))
            $type = C('DEFAULT_AJAX_RETURN');
        if (strtoupper($type) == 'JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header("Content-Type:text/html; charset=utf-8");
            exit(json_encode($result));
        } elseif (strtoupper($type) == 'XML') {
            // 返回xml格式数据
            header("Content-Type:text/xml; charset=utf-8");
            exit(xml_encode($result));
        } elseif (strtoupper($type) == 'EVAL') {
            // 返回可执行的js脚本
            header("Content-Type:text/html; charset=utf-8");
            exit($data);
        } else {
            // TODO 增加其它格式
        }
    }	

   public function online(){
   $info=M('user');
   $where['update_time']=array('gt',time()-600);
   $numPerPage=10;
   cookie('_currentUrl_', __SELF__);
   $list=$info->where($where)->limit($numPerPage)->page($_GET['pageNum'].','.$numPerPage.'')->select();
   $this->assign('list',$list);
    $count = $info->where($where)->count();
    $this->assign('totalCount', $count);
    $this->assign('currentPage', !empty($_GET['pageNum']) ? $_GET['pageNum'] : 1);
    $this->assign('numPerPage', $numPerPage); 
    $this->display();
   }
   
   public function attfile(){
       $attid=I('attid');
	   $this->assign('attid',$attid);
       $this->display();
   }
   
   
   public function upload(){
     $upload = new \Think\Upload();
	 $upload->maxSize   =     C('UPLOAD_MAXSIZE') ;
	 $upload->exts      =     C('UPLOAD_EXTS');
	 $upload->savePath  =     C('UPLOAD_SAVEPATH');
	 $info   =  $upload->upload(); 
	 $gourl = 'admin.php/public/attfile/attid/'.I('attid').'/'; 
	 $errormsg=$upload->getError();
	 //var_dump($errormsg);
	 
	 if(!$info) {
        echo "<script language='javascript' type='text/javascript'>"; 
		echo "alert('上传失败！".$errormsg."');";
		echo "window.location.href='$gourl'"; 
		echo "</script>";  			 
	   //$this->error($upload->getError());    
	}else{     
	   //dump($info);
	   $data['attid']=I('attid');
	   $data['folder']='Uploads/'.str_replace('./','',$info["filename"]["savepath"]);
	   $data['filename']=$info["filename"]["savename"];
	   $data['filetype']=$info["filename"]["ext"];
	   $data['filedesc']=$info["filename"]["name"];
	   $data['UserID']=session('UserID');
	   $data['addtime']=date("Y-m-d H:i:s",time());
	   //dump($data);
	   M('files')->data($data)->add();
		$filename=$info["filename"]["name"];
		echo "<script language='javascript' type='text/javascript'>"; 
		echo "alert('文件$filename 上传成功');";
		echo "window.location.href='$gourl'"; 
		echo "</script>";  
	  }
	}
   	
   public function user(){
	      $info=M('user');
		if (isset($_REQUEST ['orderField'])) {$order = $_REQUEST ['orderField'];}
		if($order=='') {$order = $info->getPk();}
			
		if (isset($_REQUEST ['orderDirection'])) {$sort = $_REQUEST ['orderDirection'];}
		if($sort=='') {$sort = $asc ? 'asc' : 'desc';}

		if (isset($_REQUEST ['pageCurrent'])) {$pageCurrent = $_REQUEST ['pageCurrent'];}
		if($pageCurrent=='') {$pageCurrent =1;}   

       $key=I('keys');
	   if($key){
	   $where['username'] = array('like','%'.$key.'%');   
       $where['truename'] = array('like','%'.$key.'%');
       $where['depname'] = array('like','%'.$key.'%');
       $where['posname'] = array('like','%'.$key.'%');
       $where['_logic'] = 'or'; }
	 if(IS_POST&&isset($_REQUEST['filter']) && $_REQUEST['filter'] != ''){
		 $map['depname'] = array('EQ', I('filter'));
		 $where['_complex'] = $map;
		}
   $numPerPage=10;
   cookie('_currentUrl_', __SELF__);
   $list=$info->where($where)->order("`" . $order . "` " . $sort)->limit($numPerPage)->page($pageCurrent.','.$numPerPage.'')->select();
    $this->assign('list',$list);
    $count = $info->where($where)->count();
    $this->assign('totalCount', $count);
    $this->assign('currentPage', !empty($_GET['pageNum']) ? $_GET['pageNum'] : 1);
    $this->assign('numPerPage', $numPerPage); 
	$filters=orgcateTree($pid=0,$level=0,$type=0);
    $this->assign('filters',$filters);
    $this->display();
   }
   
   public function login_dialog() {
       if(IS_POST){
           $admin = I('post.');
           $rs = D('Admin', 'Service')->login($admin);
           if (!$rs['status']) {
               //$this->error($rs['data']);
               $data['statusCode']=300;
               $data['message']=$rs['data'];
               $data['tabid']=$_REQUEST['navTabId'];
               $data['closeCurrent']='false';
               $data['forward']='';
               $this->dwzajaxReturn($data);
           }
           
           
           $tabid= cookie("login_action_tabid");
           $data['statusCode']=200;
           $data['message']='登陆成功！';
           $data['tabid']=$tabid; //;
           $data['closeCurrent']='true';
           $data['divid']='indexLeft,indexHeader';
           $data['dialogid']='infopublish_view'; //;
           //$data['forwardConfirm']="ddddd?";
           //$data['forward']='admin.php?m=Admin&c=Public&a=login';
           
           //var_dump($data);
           //die();
           $this->dwzajaxReturn($data);
           //$this->success('登录成功，正在跳转...',"admin.php"  ,1);	//登录成功跳转到管理画面
            
           //$this->display("index/index");
       }
       else{
           
           //$this->assign("tabid",$tabid);
           $this->display();
       }
   }

}