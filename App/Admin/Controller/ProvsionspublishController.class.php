<?php
// 职务管理
namespace Admin\Controller;
//use ThinkPHP\Library\Think\Controller;

class ProvsionspublishController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        $this->opname = "基本规定";
        $this->dbname = 'provsionspublish'; 
        $this->selname = 'uv_getprovsionspublish';   
        // gie ni ka s fghrghfghhf
    }
    public function del(){
        
       
        $strid =  $_REQUEST ['id'];
        if(isset($strid)&&$strid!='')
        {
            $strDelID =  $strid;
        }
        else 
        {
            
            $strDelID =  $_REQUEST ['delids'];
        }
           
        if(isset($strDelID)&&count($strDelID)>0){
            $model=M($this->dbname);
            $model->startTrans();
            $model
            ->table(C('DB_PREFIX')."files")
            ->where(" attid in (select attid from ".C('DB_PREFIX')."provsionspublish where id in (".$strDelID."))")->delete();
            
            $model
            ->table(C('DB_PREFIX')."provsionspublish")
            ->where(array('id'=>array('in',$strDelID)))->delete();
            
           //var_dump($model->getLastSql());
            
            $model->commit();
            $this->mtReturn(200,"删除【".$this->opname."】成功".$id,$_REQUEST['navTabId'],false);
        }
        
    }
    
    function _filter(&$map) {
    
        if(IS_POST){
            if(isset($_REQUEST['s_startdt']) && $_REQUEST['s_startdt'] != ''&&isset($_REQUEST['s_enddt']) && $_REQUEST['s_enddt'] != ''){
                $map['_logic'] = 'or';
                $map['publishdt'] =array(array('egt',I('s_startdt')),array('elt',I('s_enddt'))) ;
                $map['_logic'] = 'or';
                $map['modifydt'] =array(array('egt',I('s_startdt')),array('elt',I('s_enddt'))) ;
            }

            if(isset($_REQUEST['s_title']) && $_REQUEST['s_title'] != ''){
                $map['_logic'] = 'and';
                $map['title'] =array('like',"%".I('s_title')."%") ;
            }
            
            if(isset($_REQUEST['s_provsid']) && $_REQUEST['s_provsid'] != ''){
                $map['_logic'] = 'and';
                $map['provsid'] =array('eq',I('s_provsid')) ;
            }
        }
    }


    public function _befor_add(){
        $attid=time();
        $vo=array(
                    "attid" => $attid
                  );
        $this->assign('Rs', $vo);
        $this->GetMasters();
        
    }
    
    public function _befor_insert($data)
    {
        $data["publishdt"]=date('Y-m-d',time());
        
        $data["uid"]=session("uid");
        
        return $data;
        
    }

    public function _befor_index(){

        $list=cateTree($id=0,$level=0,"Provsions",$status=1);
        //规定类型
        //$orglist=M("Provsions")->where("status=1")->field("id,name,level")->order("sort asc")->select();
        //var_dump($list);
        $this->assign('provsionslist',$list);
        
        
        
        //provsionslist
        
        
    }
    
    public function _befor_update($data)
    {
        $data["modifydt"]=date('Y-m-d',time());
        return $data;
    }

    public function _befor_view()
    {
        $this->GetMasters();
    }
        
    public function _befor_edit()
    {
        $this->GetMasters();
    }
    


    // 得到Menu
    public function GetMasters()
    {

        //规定类型
        //$orglist=M("Provsions")->where("status=1")->field("id,name,level")->order("sort asc")->select();
        //var_dump($list);
        $list=cateTree($id=0,$level=0,"Provsions",$status=1);
        $this->assign('provsionslist',$list);
        
        //部门
        $orglist=cateTree($id=0,$level=0,"Org");
        //M("Org")->where("status=1")->field("id,name,level")->order("sort asc")->select();
        //var_dump($list);
        $this->assign('orglist',$orglist);

    }

    
}