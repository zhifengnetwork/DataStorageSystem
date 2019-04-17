<?php
namespace app\category\controller;

use app\common\controller\Base;
use think\Controller;
use think\Db;
use think\Config;

class Index extends Base
{
    /**
     * 类别列表
     */
    public function index()
    {
        
        if( request()->isAjax() ){
            //接收所有传过来的post数据
            $datatables = request()->post();
            //得到排序的方式
            $order = $datatables['order'][0]['dir'];
            //得到limit参数
            $limit_start = $datatables['start'];
            $limit_length = $datatables['length'];
            //得到搜索的关键词
            $search = $datatables['search']['value'];
            $where = [];
            if($search){
                $where['cat_name'] = ['like', "%{$search}%"];
            }
            
            $data = Db::name('category')
                    ->where($where)
                    ->order("cat_id $order")
                    ->limit( $limit_start,$limit_length )
                    ->select();
            $cnt = Db::name('category')->where($where)->count();
            
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

        if( request()->isPost() ){
            $data = input('post.');

            if( isset($data['img']) ){
                
                $saveName = request()->time().rand(0,99999) . '.png';

                $img=base64_decode($data['img']);
                //生成文件夹
                $names = "category" ;
                $name = "category/" .date('Ymd',time()) ;
                if (!file_exists(ROOT_PATH . Config('view_replace_str.__IMAGES__') .'/'.$names)){ 
                    mkdir(ROOT_PATH . Config('view_replace_str.__IMAGES__') .'/'.$names,0777,true);
                } 
                //保存图片到本地
                file_put_contents(ROOT_PATH . Config('view_replace_str.__IMAGES__') .'/'.$name.$saveName,$img);

                $data['img'] = $name.$saveName;
            }
            
            if ( Db::name('category')->insert($data) ) {
                $this->success('添加成功');
            }else {
                $this->error('添加失败');
            }
        }
        
        return $this->fetch();
    }

    /**
     * 修改
     */
    public function edit(){

        if( request()->isPost() ){
            $data = input('post.');

            if( isset($data['img']) ){
                
                $saveName = request()->time().rand(0,99999) . '.png';

                $img=base64_decode($data['img']);
                //生成文件夹
                $names = "category" ;
                $name = "category/" .date('Ymd',time()) ;
                if (!file_exists(ROOT_PATH . Config('view_replace_str.__IMAGES__') .'/'.$names)){ 
                    mkdir(ROOT_PATH . Config('view_replace_str.__IMAGES__') .'/'.$names,0777,true);
                } 
                //保存图片到本地
                file_put_contents(ROOT_PATH . Config('view_replace_str.__IMAGES__') .'/'.$name.$saveName,$img);

                $data['img'] = $name.$saveName;

                if($info['img']){
                    @unlink( ROOT_PATH . Config('view_replace_str.__IMAGES__') .'/' . $info['img'] );
                }
            }
            
            if ( Db::name('category')->update($data) !== false ) {
                $this->success('修改成功');
            }else {
                $this->error('修改失败');
            }
        }

        $info = Db::name('category')->where('cat_id',input('cat_id'))->find();
        
        return $this->fetch( '',[
            'info'  =>  $info,
        ]);
    }

    /*
     * 删除分类
     */
    public function del(){
        $cat_id = input('id');
        if(!$cat_id){
            returnJson(100,'参数错误');
        }
        $info = Db::name('category')->find($cat_id);
        if(!$info){
            returnJson(100,'参数错误');
        }
        // if( Db::name('document')->where('cat_id',$cat_id)->find() ){
        //     returnJson(100,'该类别含有文档，不能删除');
        // }

        if( Db::name('category')->where('cat_id',$cat_id)->delete() ){
            if( $info['img'] ){
                @unlink( ROOT_PATH . Config('view_replace_str.__IMAGES__') .'/' . $info['img'] );
            }
            returnJson([],'删除分类成功！');
        }

    }

    /**
     * ajax显示隐藏
     */
    public function is_show(){
        if( request()->isAjax() ){
            $data['cat_id'] = input('cat_id');
            $data['is_show'] = input('is_show');

            if( !$data['cat_id'] ){
                return json(['status'=>0,'info'=>lang('参数错误!')]);
            }

            if( $data['is_show'] ){
                $status = ['status'=>1,'info'=>lang('显示成功!')];
            }else{
                $status = ['status'=>1,'info'=>lang('隐藏成功!')];
            }

            $info = Db::name('category')->find($data['cat_id']);
            if( !$info ){
                return json(['status'=>0,'info'=>lang('显示或隐藏失败!')]);
            }
            
            $res = Db::name('category')->update($data,$data['cat_id']);
            return json($status);;
        }
    }
}
