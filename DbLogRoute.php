<?php

class DbLogRoute extends CDbLogRoute
{

    protected function createLogTable($db, $tableName)
    {
        parent::createLogTable($db, $tableName);
        $sql = "
            ALTER TABLE $tableName
            ADD datetime timestamp default CURRENT_TIMESTAMP,
            ADD session_id VARCHAR(50) default NULL,
            ADD user_id int(11) default NULL,
            ADD ip VARCHAR(13) default NULL,
            ADD httpinfo TEXT default NULL
        ";
        $db->createCommand($sql)->execute();
    }


    protected function processLogs($logs)
    {
        $logs = array_map(function ($log) {
            $log[0] = print_r($log[0], true);
            return $log;
        }, $logs);

        if (php_sapi_name() === 'cli') {
            parent::processLogs($logs);
            return;
        }

        $sql = "
            INSERT INTO {$this->logTableName}
            (level, category, logtime, message, ip, httpinfo, user_id, session_id)
            VALUES
            (:level, :category, :logtime, :message, :ip, :httpinfo, :user_id, :sessionid)
        ";

        $command = $this->getDbConnection()->createCommand($sql);
        foreach ($logs as $log) {
            $command->bindValue(':level', $log[1]);
            $command->bindValue(':category', $log[2]);
            $command->bindValue(':logtime', (int)$log[3]);
            $command->bindValue(':message', $log[0]);

            //personalized fields
            $command->bindValue(':sessionid', Yii::app()->session->sessionID);
            $command->bindValue(':ip', Yii::app()->request->getUserHostAddress());
            $command->bindValue(':httpinfo', $this->getHTTPParams());
            $command->bindValue(':user_id', Yii::app()->user->getId());
            $command->execute();
        }
    }

    protected function getHTTPParams()
    {
        $data = array(
            'browser' => Yii::app()->request->getUserAgent(),
            'requestUri' => Yii::app()->request->requestUri,
        );
        return json_encode($data);
    }

}
