<?php die('For development purposes only');

/**
 * Code Completion for CodeIgniter + HMVC
 */

/**
 * built-in classes:
 * @property CI_Benchmark|MY_Benchmark             $benchmark       This class enables you to mark points and calculate the time difference between them.
 * @property CI_Calendar|MY_Calendar               $calendar        This class enables the creation of calendars.
 * @property CI_Cache|MY_Cache                     $cache           Caching Class
 * @property CI_Cart|MY_Cart                       $cart            Shopping Cart Class
 * @property CI_Config|MY_Config                   $config          This class contains functions that enable config files to be managed.
 * @property CI_Controller|MY_Controller           $controller      This class object is the super class that every library in CodeIgniter will be assigned to.
 * @property CI_DB_forge                           $dbforge         Database Forge Class
 * @property CI_DB_mysqli_driver                   $db
 * @property CI_DB_query_builder                   $db              This is the platform-independent base Query Builder implementation class.
 * @property CI_DB_utility                         $dbutil          Database Utility Class
 * @property CI_Driver_Library                     $driver          Driver Library Class
 * @property CI_Email|MY_Email                     $email           Permits email to be sent using Mail, Sendmail, or SMTP.
 * @property CI_Encrypt|MY_Encrypt                 $encrypt         Provides two-way keyed encoding using Mcrypt.
 * @property CI_Encryption|MY_Encryption           $encryption      Provides two-way keyed encryption via PHP's MCrypt and/or OpenSSL extensions.
 * @property CI_Exceptions|CI_Exceptions           $exceptions      Exceptions Class
 * @property CI_Form_validation|MY_Form_validation $form_validation Form Validation Class
 * @property CI_FTP|MY_FTP                         $ftp             FTP Class
 * @property CI_Hooks|MY_Hooks                     $hooks           Provides a mechanism to extend the base system without hacking.
 * @property CI_Image_lib|MY_Image_lib             $image_lib       Image Manipulation class
 * @property CI_Input|MY_Input                     $input           Pre-processes global input data for security.
 * @property CI_Javascript|MY_Javascript           $javascript      Javascript Class
 * @property CI_Jquery|MY_Jquery                   $jquery          Jquery Class
 * @property CI_Lang|MY_Lang                       $lang            Language Class
 * @property CI_Loader|MY_Loader                   $load            Loads framework components.
 * @property CI_Log|MY_Log                         $log             Logging Class
 * @property CI_Migration|MY_Migration             $migration       All migrations should implement this, forces up() and down() and gives access to the CI super-global.
 * @property CI_Model|MY_Model                     $model           CodeIgniter Model Class
 * @property CI_Output|MY_Output                   $output          Responsible for sending final output to the browser.
 * @property CI_Pagination|MY_Pagination           $pagination      Pagination Class
 * @property CI_Parser|MY_Parser                   $parser          Parser Class
 * @property CI_Profiler|MY_Profiler               $profiler        This class enables you to display benchmark, query, and other data in order to help with debugging and optimization.
 * @property CI_Router|MY_Router                   $router          Parses URIs and determines routing.
 * @property CI_Security|MY_Security               $security        Security Class
 * @property CI_Session|MY_Session                 $session         Session Class
 * @property CI_Table|MY_Table                     $table           Lets you create tables manually or from database result objects, or arrays.
 * @property CI_Trackback|MY_Trackback             $trackback       Trackback Sending/Receiving Class
 * @property CI_Typography|MY_Typography           $typography      Typography Class
 * @property CI_Unit_test|MY_Unit_test             $unit            Simple testing class
 * @property CI_Upload|MY_Upload                   $upload          File Uploading Class
 * @property CI_URI|MY_URI                         $uri             Parses URIs and determines routing.
 * @property CI_User_agent|MY_User_agent           $agent           Identifies the platform, browser, robot, or mobile device of the browsing agent.
 * @property CI_Xmlrpc|MY_Xmlrpc                   $xmlrpc          XML-RPC request handler class
 * @property CI_Xmlrpcs|MY_Xmlrpcs                 $xmlrpcs         XML-RPC server class
 * @property CI_Zip|MY_Zip                         $zip             Zip Compression Class
 * @property CI_Utf8|MY_Utf8                       $utf8            Provides support for UTF-8 environments.
 *
 * custom classes:
 * @property Acl                                   $acl             This class enables you to apply permissions to controllers, controller and models.
 * @property Curl                                  $curl            Makes it easy to do simple cURL requests and makes more complicated cURL requests easier too.
 * @property Format                                $format          Help to convert between various formats such as XML, JSON, CSV, etc.
 * @property Recaptcha                             $recaptcha       This is a PHP library that handles calling reCAPTCHA.
 * @property Theme                                 $theme           Theme class
 */
class CodeIgniter {}

class CI_Controller extends CodeIgniter {}
class CI_Model extends CodeIgniter {}
class CI_Migration extends CodeIgniter {}
class MX_Controller extends CodeIgniter {}
