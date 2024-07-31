<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| HTTP Status Codes
|--------------------------------------------------------------------------
|
| This is a list of Hypertext Transfer Protocol (HTTP) response status codes.
| Status codes are issued by a server in response to a client's request made
| to the server. It includes codes from IETF Request for Comments (RFCs), other
| specifications, and some additional codes used in some common applications of
| the Hypertext Transfer Protocol (HTTP). The first digit of the status code
| specifies one of five standard classes of responses. The message phrases
| shown are typical, but any human-readable alternative may be provided.
|
*/
// Informational
defined('HTTP_CONTINUE')						OR define('HTTP_CONTINUE', 100);
defined('HTTP_SWITCHING_PROTOCOLS')				OR define('HTTP_SWITCHING_PROTOCOLS', 101);
defined('HTTP_PROCESSING')						OR define('HTTP_PROCESSING', 102); // RFC2518
// Success
defined('HTTP_OK')								OR define('HTTP_OK', 200);
defined('HTTP_CREATED')							OR define('HTTP_CREATED', 201);
defined('HTTP_ACCEPTED')						OR define('HTTP_ACCEPTED', 202);
defined('HTTP_NON_AUTHORITATIVE_INFORMATION')	OR define('HTTP_NON_AUTHORITATIVE_INFORMATION', 203);
defined('HTTP_NO_CONTENT')						OR define('HTTP_NO_CONTENT', 204);
defined('HTTP_RESET_CONTENT')					OR define('HTTP_RESET_CONTENT', 205);
defined('HTTP_PARTIAL_CONTENT')					OR define('HTTP_PARTIAL_CONTENT', 206);
defined('HTTP_MULTI_STATUS')					OR define('HTTP_MULTI_STATUS', 207); // RFC4918
defined('HTTP_ALREADY_REPORTED')				OR define('HTTP_ALREADY_REPORTED', 208); // RFC5842
defined('HTTP_IM_USED')							OR define('HTTP_IM_USED', 226); // RFC3229
// Redirection
defined('HTTP_MULTIPLE_CHOICES')				OR define('HTTP_MULTIPLE_CHOICES', 300);
defined('HTTP_MOVED_PERMANENTLY')				OR define('HTTP_MOVED_PERMANENTLY', 301);
defined('HTTP_FOUND')							OR define('HTTP_FOUND', 302);
defined('HTTP_SEE_OTHER')						OR define('HTTP_SEE_OTHER', 303);
defined('HTTP_NOT_MODIFIED')					OR define('HTTP_NOT_MODIFIED', 304);
defined('HTTP_USE_PROXY')						OR define('HTTP_USE_PROXY', 305);
defined('HTTP_RESERVED')						OR define('HTTP_RESERVED', 306);
defined('HTTP_TEMPORARY_REDIRECT')				OR define('HTTP_TEMPORARY_REDIRECT', 307);
defined('HTTP_PERMANENTLY_REDIRECT')			OR define('HTTP_PERMANENTLY_REDIRECT', 308); // RFC7238
// Client Error
defined('HTTP_BAD_REQUEST')						OR define('HTTP_BAD_REQUEST', 400);
defined('HTTP_UNAUTHORIZED')					OR define('HTTP_UNAUTHORIZED', 401);
defined('HTTP_PAYMENT_REQUIRED')				OR define('HTTP_PAYMENT_REQUIRED', 402);
defined('HTTP_FORBIDDEN')						OR define('HTTP_FORBIDDEN', 403);
defined('HTTP_NOT_FOUND')						OR define('HTTP_NOT_FOUND', 404);
defined('HTTP_METHOD_NOT_ALLOWED')				OR define('HTTP_METHOD_NOT_ALLOWED', 405);
defined('HTTP_NOT_ACCEPTABLE')					OR define('HTTP_NOT_ACCEPTABLE', 406);
defined('HTTP_PROXY_AUTHENTICATION_REQUIRED')	OR define('HTTP_PROXY_AUTHENTICATION_REQUIRED', 407);
defined('HTTP_REQUEST_TIMEOUT')					OR define('HTTP_REQUEST_TIMEOUT', 408);
defined('HTTP_CONFLICT')						OR define('HTTP_CONFLICT', 409);
defined('HTTP_GONE')							OR define('HTTP_GONE', 410);
defined('HTTP_LENGTH_REQUIRED')					OR define('HTTP_LENGTH_REQUIRED', 411);
defined('HTTP_PRECONDITION_FAILED')				OR define('HTTP_PRECONDITION_FAILED', 412);
defined('HTTP_REQUEST_ENTITY_TOO_LARGE')		OR define('HTTP_REQUEST_ENTITY_TOO_LARGE', 413);
defined('HTTP_REQUEST_URI_TOO_LONG')			OR define('HTTP_REQUEST_URI_TOO_LONG', 414);
defined('HTTP_UNSUPPORTED_MEDIA_TYPE')			OR define('HTTP_UNSUPPORTED_MEDIA_TYPE', 415);
defined('HTTP_REQUESTED_RANGE_NOT_SATISFIABLE')	OR define('HTTP_REQUESTED_RANGE_NOT_SATISFIABLE', 416);
defined('HTTP_EXPECTATION_FAILED')				OR define('HTTP_EXPECTATION_FAILED', 417);
defined('HTTP_I_AM_A_TEAPOT')					OR define('HTTP_I_AM_A_TEAPOT', 418); // RFC2324
defined('HTTP_UNPROCESSABLE_ENTITY')			OR define('HTTP_UNPROCESSABLE_ENTITY', 422); // RFC4918
defined('HTTP_LOCKED')							OR define('HTTP_LOCKED', 423); // RFC4918
defined('HTTP_FAILED_DEPENDENCY')				OR define('HTTP_FAILED_DEPENDENCY', 424); // RFC4918
defined('HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL') OR define('HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL', 425); // RFC2817
defined('HTTP_UPGRADE_REQUIRED')				OR define('HTTP_UPGRADE_REQUIRED', 426); // RFC2817
defined('HTTP_PRECONDITION_REQUIRED')			OR define('HTTP_PRECONDITION_REQUIRED', 428); // RFC6585
defined('HTTP_TOO_MANY_REQUESTS')				OR define('HTTP_TOO_MANY_REQUESTS', 429); // RFC6585
defined('HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE')	OR define('HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE', 431); // RFC6585
// Server Error
defined('HTTP_INTERNAL_SERVER_ERROR')			OR define('HTTP_INTERNAL_SERVER_ERROR', 500);
defined('HTTP_NOT_IMPLEMENTED')					OR define('HTTP_NOT_IMPLEMENTED', 501);
defined('HTTP_BAD_GATEWAY')						OR define('HTTP_BAD_GATEWAY', 502);
defined('HTTP_SERVICE_UNAVAILABLE')				OR define('HTTP_SERVICE_UNAVAILABLE', 503);
defined('HTTP_GATEWAY_TIMEOUT')					OR define('HTTP_GATEWAY_TIMEOUT', 504);
defined('HTTP_VERSION_NOT_SUPPORTED')			OR define('HTTP_VERSION_NOT_SUPPORTED', 505);
defined('HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL') OR define('HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL', 506); // RFC2295
defined('HTTP_INSUFFICIENT_STORAGE')			OR define('HTTP_INSUFFICIENT_STORAGE', 507); // RFC4918
defined('HTTP_LOOP_DETECTED')					OR define('HTTP_LOOP_DETECTED', 508); // RFC5842
defined('HTTP_NOT_EXTENDED')					OR define('HTTP_NOT_EXTENDED', 510); // RFC2774
defined('HTTP_NETWORK_AUTHENTICATION_REQUIRED')	OR define('HTTP_NETWORK_AUTHENTICATION_REQUIRED', 511);
