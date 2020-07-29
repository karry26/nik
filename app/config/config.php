<?php
define("ERROR_DEBUG_MODE",true); // true -> will show database errors to user , false->otherwise (set it while deployment)
define("SETUP_DEBUG_MODE",true); // true -> will force user to enter required data (set it while deployment) , false->otherwise
define("ADMIN_URL","admin");
define("MAIN_SCRIPTS",array("jquery-3.5.1.js","login_sys.js","main.js"));
define("MAIN_CSS",array("main.css","https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"));
define("MAIN_NAVBAR","navbar.html");
define("DB_NAME","system_d");
define("GOOGLE_CLIENT_ID","316686701656-1cs0k0pc8kpfihuirpetvltlrlp0nb9j.apps.googleusercontent.com");
require CONFIG.'stringhelper.php';
require CORE.'Application.php';

?>