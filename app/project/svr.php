<?php
/**
 * Created by PhpStorm.
 * User: 25791
 * Date: 2020/4/8
 * Time: 17:00
 * Note: svr.php
 */

namespace app\project;


use app\lib\api;
use app\lib\model\svr as model_svr;

class svr extends api
{
    /**
     * 列表
     *
     * @param int $page
     * @param int $page_size
     *
     * @return array
     */
    public function list(int $page, int $page_size)
    {
        return model_svr::new()->where(['status', 1])->get_page($page, $page_size);
    }

    /**
     * 信息
     *
     * @param int $svr_id
     *
     * @return array
     */
    public function info(int $svr_id)
    {
        return model_svr::new()->where([['svr_id', $svr_id], ['status', 1]])->get_one();
    }

    /**
     * 编辑或新增
     *
     * @param string $url
     * @param string $svr_name
     * @param int    $svr_id
     *
     * @return bool
     */
    public function edit(string $url, string $svr_name = '', int $svr_id = 0)
    {
        $value = [
            'url'      => $url,
            'svr_name' => $svr_name,
        ];
        if ($svr_id) {
            return model_svr::new()->value($value)->where(['svr_id', $svr_id])->save();
        } else {
            return model_svr::new()->value($value)->add();
        }
    }

    /**
     * 删除
     *
     * @param int $svr_id
     *
     * @return bool
     */
    public function del(int $svr_id)
    {
        return model_svr::new()->where(['svr_id', $svr_id])->value(['status' => 2])->save();
    }
}