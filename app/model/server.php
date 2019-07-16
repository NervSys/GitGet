<?php

namespace app\model;


class server extends base_model
{
    public function getList(array $where, int $offset, int $limit)
    {
        return $this->where($where)->field("srv_id,srv_name,srv_port,srv_ip,srv_desc")
            ->order(['srv_id' => 'DESC'])->limit($offset, $limit)->get();
    }

    public function getCount(array $where)
    {
        return $this->where($where)->count();
    }

    public function getInfo(int $srv_id)
    {
        return $this->where(["srv_id", $srv_id])->get_one();
    }

    public function addSrv(array $data)
    {
        return $this->value($data)->create();
    }

    public function updateSrv(array $data, array $where)
    {
        return $this->where($where)->value($data)->update();
    }

    public function del_serv(int $srv_id){
        $this->where(['srv_id',$srv_id])->delete()->execute();
        return $this->last_affect();
    }

}