<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 6/17/2019
 * Time: 2:28 PM
 * Note: ctrl.php
 */

namespace app\project;

use ext\errno;
use app\library\model;

class ctrl extends model
{
    public $tz = 'add,edit';

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
     * @param string $name
     * @param string $desc
     * @param array  $conf
     *
     * @return array
     */
    public function add(string $name, string $desc, array $conf): array
    {
        if ('' === $name) {
            return errno::get(3001, 1);
        }

        $time = time();

        $ret = $this->insert('project')
            ->value([
                'proj_name' => &$name,
                'proj_desc' => &$desc,
                'proj_conf' => json_encode($conf),
                'add_time'  => &$time
            ])
            ->execute();

        $this->insert('project_log')
            ->value([
                'proj_id'  => $this->last_insert(),
                'proj_log' => 'Project added!',
                'user_id'  => $this->user_id,
                'add_time' => &$time
            ])
            ->execute();

        return $ret ? errno::get(3002) : errno::get(3003, 1);
    }

    /**
     * @param int    $id
     * @param string $name
     * @param string $desc
     * @param array  $conf
     *
     * @return array
     */
    public function edit(int $id, string $name, string $desc, array $conf): array
    {
        if ('' === $name) {
            return errno::get(3001, 1);
        }

        $time = time();

        $ret = $this->update('project')
            ->value([
                'proj_name' => &$name,
                'proj_desc' => &$desc,
                'proj_conf' => json_encode($conf),
                'add_time'  => &$time
            ])
            ->where(['proj_id', $id])
            ->execute();

        $this->insert('project_log')
            ->value([
                'proj_id'  => &$id,
                'proj_log' => 'Project updated!',
                'user_id'  => $this->user_id,
                'add_time' => &$time
            ])
            ->execute();

        return $ret ? errno::get(3002) : errno::get(3003, 1);
    }
}