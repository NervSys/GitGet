<?php


namespace app\git;

use ext\conf;
use ext\errno;

class deploy
{
    const TEMP_PATH = __DIR__ . '/temp';


    public $tz = 'init,checkout,delete,reset_hard,get_local_branch,get_remote_branch';

    private $conf_acc  = [];
    private $conf_git  = [];
    private $conf_cmd  = [];
    private $conf_copy = [];

    private $copy_files = [];


    /**
     * action constructor.
     */
    public function __construct()
    {
        conf::load('app/git', 'conf');
        errno::load('app/git', 'error', false);

        $this->conf_acc  = conf::get('acc');
        $this->conf_git  = conf::get('git');
        $this->conf_cmd  = conf::get('cmd');
        $this->conf_copy = conf::get('copy');

        $local_path = $this->conf_git['local_path'];

        if (!is_dir($local_path)) {
            mkdir($local_path, 0777, true);
            chmod($local_path, 0777);
        }

        chdir($local_path);

        //copy files
        foreach ($this->conf_copy as $item) {
            $file_path = $local_path . DIRECTORY_SEPARATOR . trim($item, " /\\\t\n\r\0\x0B");

            if (!is_file($file_path)) {
                continue;
            }

            $temp_name = hash('sha1', uniqid(mt_rand(), true));
            $copy_path = self::TEMP_PATH . DIRECTORY_SEPARATOR . $temp_name;

            copy($file_path, $copy_path);

            $this->copy_files[] = [
                'source' => $file_path,
                'dest'   => $copy_path
            ];
        }

        //config git
        exec(sprintf($this->conf_cmd['config_name'], $this->conf_acc['name']), $output);
        exec(sprintf($this->conf_cmd['config_email'], $this->conf_acc['email']), $output);
    }


    /**
     * Initialize
     * clone & return branch info
     */
    public function init(): array
    {
        //Clone if not exist
        if (!is_dir($this->conf_git['local_path'] . DIRECTORY_SEPARATOR . '.git')) {
            exec(sprintf($this->conf_cmd['clone'], $this->conf_git['download_url'], $this->conf_git['local_path']), $output);
        }

        //Get local branch info
        exec($this->conf_cmd['branch_list_local'], $output);

        errno::set(1000);

        return [
            'log'  => $output,
            'info' => array_pop($output)
        ];
    }

    /**
     * @param string $branch
     *
     * @return array
     */
    public function checkout(string $branch): array
    {
        $branch_name = escapeshellcmd($branch);

        //Stash
        exec($this->conf_cmd['stash_save'], $output);

        //Fetch
        exec($this->conf_cmd['fetch'], $output);

        //Get local branch
        exec($this->conf_cmd['branch_list_local'], $branch_list);

        //Check branch
        $exist = false;
        foreach ($branch_list as $item) {
            $item = trim($item, " *\t\n\r\0\x0B");

            if (0 === strpos($item, $branch_name, true)) {
                $exist = true;
                break;
            }
        }

        if (!$exist) {
            //Track branch
            exec(sprintf($this->conf_cmd['branch'], $branch_name, $branch_name), $output);
        }

        //Checkout & pull
        exec(sprintf($this->conf_cmd['checkout'], $branch_name), $output);
        exec(sprintf($this->conf_cmd['pull'], $branch_name), $output);

        //Apply stash & drop
        exec($this->conf_cmd['stash_pop'], $output);

        //Get local branch info
        exec($this->conf_cmd['branch_list_local'], $branch_output);

        //Find current branch
        $commit = 'Checkout failed!';
        foreach ($branch_output as $item) {
            if (0 === strpos($item, '*')) {
                $commit = $item;
                break;
            }
        }

        errno::set(1000);

        return [
            'log'  => $output,
            'info' => $commit
        ];
    }

    /**
     * @param string $branch
     *
     * @return array
     */
    public function delete(string $branch): array
    {
        if ('master' === $branch) {
            return errno::get(1002, 1);
        }

        //Checkout & pull master
        exec(sprintf($this->conf_cmd['checkout'], 'master'), $output);
        exec(sprintf($this->conf_cmd['pull'], 'master'), $output);

        //Delete branch
        exec(sprintf($this->conf_cmd['branch_delete'], escapeshellcmd($branch)), $output);

        //Get local branch info
        exec($this->conf_cmd['branch_list_local'], $branch_output);

        //Find current branch
        $commit = 'Checkout failed!';
        foreach ($branch_output as $item) {
            if (0 === strpos($item, '*')) {
                $commit = $item;
                break;
            }
        }

        errno::set(1000);

        return [
            'log'  => $output,
            'info' => $commit
        ];
    }

    /**
     * @param string $commit
     *
     * @return array
     */
    public function reset_hard(string $commit): array
    {
        exec(sprintf($this->conf_cmd['reset_hard'], escapeshellcmd($commit)), $output);

        //Get local branch info
        exec($this->conf_cmd['branch_list_local'], $branch_output);

        //Find current branch
        $commit = 'Checkout failed!';
        foreach ($branch_output as $item) {
            if (0 === strpos($item, '*')) {
                $commit = $item;
                break;
            }
        }

        errno::set(1000);

        return [
            'log'  => $output,
            'info' => $commit
        ];
    }

    /**
     * @return mixed
     */
    public function get_local_branch(): array
    {
        exec($this->conf_cmd['branch_list_local'], $output);

        errno::set(1000);

        return $output;
    }

    /**
     * @return mixed
     */
    public function get_remote_branch(): array
    {
        exec($this->conf_cmd['branch_list_remote'], $output);

        errno::set(1000);

        return $output;
    }

    /**
     * 恢复备份文件
     */
    public function __destruct()
    {
        if (empty($this->copy_files)) {
            return;
        }

        //copy files
        foreach ($this->copy_files as $item) {
            rename($item['dest'], $item['source']);
        }
    }
}