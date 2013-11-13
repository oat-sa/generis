<?php
/*
 * PHP > = 5.4
 */
/**
 * CREATE TABLE `session` (
    `session_id` varchar(255) NOT NULL,
    `session_value` text NOT NULL,
    `session_time` int(11) NOT NULL,
    PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 *
 *
 * PostgresSQL
 *
 * CREATE TABLE session (
    session_id character varying(255) NOT NULL,
    session_value text NOT NULL,
    session_time integer NOT NULL,
    CONSTRAINT session_pkey PRIMARY KEY (session_id)
);
 *
 * Ms SQL
 * CREATE TABLE [dbo].[session](
    [session_id] [nvarchar](255) NOT NULL,
    [session_value] [ntext] NOT NULL,
    [session_time] [int] NOT NULL,
    PRIMARY KEY CLUSTERED(
        [session_id] ASC
    ) WITH (
        PAD_INDEX  = OFF,
        STATISTICS_NORECOMPUTE  = OFF,
        IGNORE_DUP_KEY = OFF,
        ALLOW_ROW_LOCKS  = ON,
        ALLOW_PAGE_LOCKS  = ON
    ) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
 *
 *
 */

class common_session_storage_DbSessionStorage
    // for php 5.4
    //implements SessionHandlerInterface

    //for php >5.3
    implements common_session_storage_SessionStorage
{
    private $dbWrapper = null;
    public function open($savePath, $sessionName){

        $this->dbWrapper = core_kernel_classes_DbWrapper::singleton();
        //checks if the session table is existing and performs a local upgrade if needed
        //should be removed on tao 2.6 release
        try{
       $statement =
           "
           CREATE TABLE if not exists sessions(
           session_id varchar(255) NOT NULL,
           session_value text NOT NULL,
           session_time int(11) NOT NULL,
           PRIMARY KEY (session_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
           ";
       $this->dbWrapper->query($statement);
       }
		catch (PDOException $e){
			throw new common_Exception("Unable to create session storage table in the database");
		}
       
        return true;
    }

    public function close()
    {
        $this->dbWrapper->destruct();
        return true;
    }

    public function read($id)
    {
       try{
        $statement = 'SELECT session_value FROM sessions WHERE session_id = ? LIMIT 1';
        $sessionValue = $this->dbWrapper->query($statement, array($id));
        while ($row = $sessionValue->fetch()) {
         return $row["session_value"];
        }
       }
		catch (PDOException $e){
			throw new common_Exception("Unable to read session value");
		}
        return false;
    }

    public function write($id, $data)
    {
       try{
       $statement = 'REPLACE INTO sessions (session_id, session_value, session_time) VALUES(?, ?, ?)';
       $sessionValue = $this->dbWrapper->query($statement, array($id, $data, time()));
       return (bool)$sessionValue->rowCount();
       }
		catch (PDOException $e){
			throw new common_Exception("Unable to write the session storage table in the database");
		}
        return false;
    }

    public function destroy($id){

        try{
       $statement = 'DELETE FROM sessions  WHERE session_id = ?';
       $sessionValue = $this->dbWrapper->query($statement, array($id));
       return (bool)$sessionValue->rowCount();
       }
		catch (PDOException $e){
			throw new common_Exception("Unable to delete the session storage table in the database");
		}
        return false;
    }

    public function gc($maxlifetime)
    {   
        $statement =
            'DELETE FROM sessions WHERE session_time <  ?';
        $timeOut = (time()-$maxlifetime);
        $this->dbWrapper->query($statement, array($timeOut));
        return true;
        
    }
   
 }
?>