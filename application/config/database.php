<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;

$db['default']['hostname'] = (LMHAKSA) ? '11.11.11.11:3306': ((TMHAKSA) ? '11.11.11.11:3306' : ((QMHAKSA) ? '22.22.22.22:3306' : '33.33.33.33'));
$db['default']['username'] = (LMHAKSA) ? 'db_name' : ((TMHAKSA) ? 'db_name' : ((QMHAKSA) ? 'db_name' : 'db_name'));
$db['default']['password'] = (LMHAKSA) ? 'db_password' : ((TMHAKSA) ? 'db_password' : ((QMHAKSA) ? 'db_password' : 'db_password'));
$db['default']['database'] = 'database_name';
$db['default']['dbdriver'] = 'mysql';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'euckr';
$db['default']['dbcollat'] = 'euckr_korean_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;


$db['haksa2080']['hostname'] = (LMHAKSA) ? '11.11.11.11:3306': ((TMHAKSA) ? '11.11.11.11:3306' : ((QMHAKSA) ? '22.22.22.22:3306' : '33.33.33.33'));
$db['haksa2080']['username'] = (LMHAKSA) ? 'db_name' : ((TMHAKSA) ? 'db_name' : ((QMHAKSA) ? 'db_name' : 'db_name'));
$db['haksa2080']['password'] = (LMHAKSA) ? 'db_password' : ((TMHAKSA) ? 'db_password' : ((QMHAKSA) ? 'db_password' : 'db_password'));
$db['haksa2080']['database'] = 'database_name';
$db['haksa2080']['dbdriver'] = 'mysql';
$db['haksa2080']['dbprefix'] = '';
$db['haksa2080']['pconnect'] = FALSE;
$db['haksa2080']['db_debug'] = TRUE;
$db['haksa2080']['cache_on'] = FALSE;
$db['haksa2080']['cachedir'] = '';
$db['haksa2080']['char_set'] = 'euckr';
$db['haksa2080']['dbcollat'] = 'euckr_korean_ci';
$db['haksa2080']['swap_pre'] = '';
$db['haksa2080']['autoinit'] = TRUE;
$db['haksa2080']['stricton'] = FALSE;

## SMS �߼� (SC_TRAN)
$db['tsms']['hostname'] = (LMHAKSA) ? '11.11.11.11:3306': ((TMHAKSA) ? '11.11.11.11:3306' : ((QMHAKSA) ? '22.22.22.22:3306' : '10.20.19.107'));
$db['tsms']['username'] = (LMHAKSA) ? 'db_name' : ((TMHAKSA) ? 'db_name' : ((QMHAKSA) ? 'db_name' : 'SMS_champ'));
$db['tsms']['password'] = (LMHAKSA) ? 'db_password' : ((TMHAKSA) ? 'db_password' : ((QMHAKSA) ? 'db_password' : 'smscha12@!'));
$db['tsms']['database'] = (LMHAKSA) ? 'database_name' : ((TMHAKSA) ? 'database_name' : ((QMHAKSA) ? 'database_name' : 'database_name'));
$db['tsms']['dbdriver'] = 'mysql';
$db['tsms']['dbprefix'] = '';
$db['tsms']['pconnect'] = FALSE;
$db['tsms']['db_debug'] = TRUE;
$db['tsms']['cache_on'] = FALSE;
$db['tsms']['cachedir'] = '';
$db['tsms']['char_set'] = 'euckr';
$db['tsms']['dbcollat'] = 'euckr_korean_ci';
$db['tsms']['swap_pre'] = '';
$db['tsms']['autoinit'] = TRUE;
$db['tsms']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */