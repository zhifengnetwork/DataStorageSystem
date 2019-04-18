<?php
namespace app\document\controller;

use app\common\controller\Base;
use think\Controller;
use think\Db;
use think\Config;

class Index extends Base
{
    /**
     * 文档列表
     */
    public function index()
    {
        
        if( request()->isAjax() ){

            $cat_id = input('cat_id');
            $where['cat_id'] = ['eq',$cat_id];
            if( session('user_id') ) $where['is_show'] = ['eq','1'];

            //接收所有传过来的post数据
            $datatables = request()->post();
            //得到排序的方式
            $order = $datatables['order'][0]['dir'];
            //得到limit参数
            $limit_start = $datatables['start'];
            $limit_length = $datatables['length'];
            //得到搜索的关键词
            $search = $datatables['search']['value'];
            
            if($search){
                $where['doc_name'] = ['like', "%{$search}%"];
            }
            
            $data = Db::name('document')
                    ->where($where)
                    ->order("doc_id $order")
                    ->limit( $limit_start,$limit_length )
                    ->select();
            $cnt = Db::name('document')->where($where)->count();
            
            $list = [
                'draw'=> request()->post('draw'), // ajax请求次数，作为标识符
                'recordsTotal'=>count($data),  // 获取到的结果数(每页显示数量)
                'recordsFiltered'=>$cnt,       // 符合条件的总数据量
                'data'=>$data,
            ];
            return json( $list );
        }
        
        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){

        if(!input('cat_id')) $this->error('参数错误!');

        if( request()->isPost() ){
            $data = input('post.');
            
            if(!$data['doc_name']) $this->error('类别名称必须填写！');

            //文档处理
            if($_FILES['url']['error'] == 0){
                $data['url'] = $this->public_doc('','url');
            }else{
                $this->error('请上传文档！');
            }
            $data['add_time'] = time();
            if ( Db::name('document')->insert($data) ) {
                layer_close('添加成功');
            }else {
                layer_close('添加失败');
            }
        }
        
        return $this->fetch();
    }

    /**
     * 修改
     */
    public function edit(){

        $info = Db::name('document')->where('doc_id',input('doc_id'))->find();
        
        if( request()->isPost() ){
            $data = input('post.');

            if(!$data['doc_name']) $this->error('文档名称必须填写！');

            //文档处理
            if($_FILES['url']['error'] == 0){
                $data['url'] = $this->public_doc('','url');
                @unlink( ROOT_PATH . Config('c_pub.img') . $info['url'] );
            }
            if ( Db::name('document')->update($data) !== false ) {
                layer_close('修改成功');
            }else {
                layer_close('修改失败');
            }
        }

        return $this->fetch( '',[
            'info'  =>  $info,
        ]);
    }

    /*
     * 预览文档
     */
    public function preview(){
        $doc_id = input('doc_id');
        if(!$doc_id) layer_close('参数错误！');

        $info = Db::name('document')->find($doc_id);
        if(!$info) $this->error('参数错误！');
        $fileUrl = request()->domain() . '/public/uploads/doc/' . $info['url'];
        header('HTTP/1.1 301 Moved Permanently');
	    header('Location: https://view.officeapps.live.com/op/view.aspx?src='.$fileUrl);//fileUrl 必须是绝对路径

    }

    /*
     * 删除分类
     */
    public function del(){
        $doc_id = input('id');
        if(!$doc_id){
            returnJson(100,'参数错误');
        }
        $info = Db::name('document')->find($doc_id);
        if(!$info){
            returnJson(100,'参数错误');
        }
        // if( Db::name('document')->where('doc_id',$doc_id)->find() ){
        //     returnJson(100,'该类别含有文档，不能删除');
        // }

        if( Db::name('document')->where('doc_id',$doc_id)->delete() ){
            if( $info['url'] ){
                @unlink( ROOT_PATH . Config('c_pub.url') . $info['url'] );
            }
            returnJson([],'删除分类成功！');
        }

    }

    /**
     * ajax显示隐藏
     */
    public function is_show(){
        if( request()->isAjax() ){
            $data['doc_id'] = input('id');
            $data['is_show'] = input('is_show');

            if( !$data['doc_id'] ){
                return json(['code'=>0,'msg'=>lang('参数错误!')]);
            }

            if( $data['is_show'] ){
                $status = ['code'=>1,'msg'=>lang('显示成功!')];
            }else{
                $status = ['code'=>1,'msg'=>lang('隐藏成功!')];
            }

            $info = Db::name('document')->find($data['doc_id']);
            if( !$info ){
                return json(['code'=>0,'msg'=>lang('显示或隐藏失败!')]);
            }
            
            $res = Db::name('document')->update($data,$data['doc_id']);
            return json($status);;
        }
    }
}
