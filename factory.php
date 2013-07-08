<?php

	class DBManager{
		public static function setDriver($driver){
			$this->driver = $driver;
		}
		
		public static function connect(){
			if($this->driver == "mysql"){
				$mm = new MySQLMenager();
				$mm->setHost('localhost');
				$mm->setDB('test');
				$mm->setUserName('root');
				$mm->setPassword('');
				$this->connection = $mm->connect();
			}
			elseif($this->driver == "pgsql"){
				$pg = new PostgreSQLMenager();
				$pg->setHost('localhost');
				$pg->setDB('test');
				$pg->setUserName('root');
				$pg->setPassword('');
				$this->connection = $pg->connect();
			}
		}
	}
	
	class MySQLMenager{
		public function setHost($host){}
		public function setDB($db_name){}
		public function setUserName($db_user){}
		public function setPassword($db_password){}
		public function connect(){}
	}
	
	class PostgreSQLMenager{
		public function setHost($host){}
		public function setDB($db_name){}
		public function setUserName($db_user){}
		public function setPassword($db_password){}
		public function connect(){}
	}