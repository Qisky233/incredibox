<?php

function _init_auto_publish_games(){
    $cronjob_data = get_pref('cron-job');
    if(!is_null($cronjob_data)){
        $cronjob_data = json_decode($cronjob_data, true);
        if(isset($cronjob_data['auto-post'])){
            $task_date = $cronjob_data['auto-post']['date'];
            $cur_date = date("Y-m-d H:i:s");
            if($cur_date >= $task_date){
                $datetime1 = date_create($cur_date);
                $datetime2 = date_create($task_date);
                $interval = date_diff($datetime1, $datetime2);
                $diff = $interval->format('%d');
    
                if($diff < 4){
                    $new_task_date = date('Y-m-d H:i:s', strtotime('+8 hours', strtotime(date('Y-m-d H:i:s'))));
                    $cronjob_data['auto-post']['date'] = $new_task_date;
                    update_option('cron-job', json_encode($cronjob_data));
                    //
                    define("CRON", "auto-post");
                    require( dirname(__FILE__) . '/do-action-auto-add.php' );
                } else { //More than 4 days inactive
                    echo 'remove';
                    unset($cronjob_data['auto-post']);
                    update_option('cron-job', json_encode($cronjob_data));
                }
            }
        } else {
            //Inactive
        }
    }
}

_init_auto_publish_games();

?>