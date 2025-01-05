<?php
class DB extends DBmysql {
   public $dbhost = '{{ glpi_db_host }}';
   public $dbuser = '{{ glpi_db_user }}';
   public $dbpassword = '{{ glpi_db_password | urlencode }}';
   public $dbdefault = '{{ glpi_db_name }}';
   public $use_utf8mb4 = true;
   public $allow_myisam = false;
   public $allow_datetime = false;
   public $allow_signed_keys = false;
}
