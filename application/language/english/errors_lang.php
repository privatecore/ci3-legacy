<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Errors Language File
 */

// title
// 4xx: Client Error
$lang['errors_title_400'] = 'Bad Request';
$lang['errors_title_401'] = 'Unauthorized';
$lang['errors_title_403'] = 'Access Forbidden';
$lang['errors_title_404'] = 'Page Not Found';
$lang['errors_title_405'] = 'Method Not Allowed';
// 5xx: Server Error
$lang['errors_title_500'] = 'Internal Server Error';
$lang['errors_title_501'] = 'Not Implemented';
$lang['errors_title_503'] = 'Service Unavailable';

// message
// 4xx: Client Error
$lang['errors_message_400'] = 'The server found a syntax error in the client\'s request.';
$lang['errors_message_401'] = 'Authentication is required to access the requested resource.';
$lang['errors_message_403'] = 'Insufficient privileges or access denied.';
$lang['errors_message_404'] = 'The page you were looking for could not be found.<br>Possibly, an incorrect address was entered or the page has been deleted.';
$lang['errors_message_405'] = 'The client-specified method is not allowed for the current resource.';
// 5xx: Server Error
$lang['errors_message_500'] = 'An internal server error occurred that does not fit into other error classes.';
$lang['errors_message_501'] = 'The server does not support the functionality required to fulfill the request.';
$lang['errors_message_503'] = 'The server is temporarily unable to handle requests for technical reasons.';
