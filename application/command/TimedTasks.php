<?php

namespace app\command;


use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\Db;

class TimedTasks extends Command
{
    /*php think timedtasks */
    protected function configure()
    {
        $this->setName('timedtasks')->setDescription('');
    }

    protected function execute(Input $input, Output $output)
    {
        try {

            $data = [

            ];
            $this->tasks($data);


        } catch (\Exception $e) {
            $this->execute($input, $output);
        }
    }

    protected function tasks($data)
    {
        Db::startTrans();
        try {
            Db::commit();
        } catch (\Exception $e) {
            Db::rollback();
        }
    }
}