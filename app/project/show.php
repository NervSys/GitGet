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

        return $this->select('project_team AS a')
            ->join('project AS b', ['a.user_id', 'b.user_id'])
            ->field('a.proj_id', 'a.proj_name', 'a.proj_desc', 'a.add_time')
            ->where(['b.user_id', $this->user_id])
            ->order(['a.add_time' => 'desc'])
            ->fetch();
    }
}