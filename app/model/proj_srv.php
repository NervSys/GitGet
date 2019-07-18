<?php

namespace app\model;


class proj_srv extends base_model
{
    public function getList()
    {
        return $this->field("srv_id")->get();
    }

    public function getSrvids(int $proj_id)
    {
        return $this->where(['proj_id',$proj_id])->field("srv_id")
            ->get_col();
    }

    public function getListExcProj(int $proj_id)
    {
        return $this->where([['proj_id','!=',$proj_id]])->field("srv_id")
            ->get();
    }

    public function addProjectSrv(array $data){
        $this->value($data)->create();
        return $this->last_insert();
    }

    public function delSrv(int $proj_id){
        $this->where(['proj_id',$proj_id])->delete()->execute();
    }


}