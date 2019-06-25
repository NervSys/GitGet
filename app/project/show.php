<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 2:28 PM
 * Note: show.php
 */

namespace app\project;

use ext\errno;
use app\library\model;

class show extends model
{
    public $tz = 'list';

    private $user_id = 0;

    /**
     * ctrl constructor.
     */
    public function __construct()
    {
        parent::__construct();

        errno::load('app', 'proj_ctrl');

        if (0 === $this->user_id = $this->get_user_id()) {
            errno::set(3000);
            parent::stop();
        }
    }

    /**
     * @return array
     */
    public function list(): array
    {
        errno::set(3002);
        $res =  $this->select('project_team AS a')
            ->join('project AS b', ['a.proj_id', 'b.proj_id'])
            ->field('a.proj_id', 'b.proj_name', 'b.proj_desc', 'b.add_time')
            ->where(['a.user_id', $this->user_id])
            ->order(['b.add_time' => 'desc'])
            ->fetch();
        return $res;
    }
}