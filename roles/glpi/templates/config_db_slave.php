<?php
class DBSlave extends DBmysql {
   public $slave = true;
   public $dbhost = '{{ glpi_db_replica }}';
   public $dbuser = '{{ glpi_db_user }}';
   public $dbpassword = '{{ glpi_db_password | urlencode }}';
   public $dbdefault = '{{ glpi_db_name }}';
   public $dbssl = true;
   public $dbsslca = '{{ glpi_db_cafile }}';
   public $dbsslcert = '{{ glpi_db_certfile }}';
   public $dbsslkey = '{{ glpi_db_keyfile }}';
   public $use_utf8mb4 = true;
   public $allow_myisam = false;
   public $allow_datetime = false;
   public $allow_signed_keys = false;
}
