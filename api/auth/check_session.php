<?php
/**
 * Check Session Status
 * 
 * Returns current session information including time remaining
 */

require_once '../../utils/SessionManager.php';
require_once '../../utils/ResponseFormatter.php';
require_once '../../utils/ErrorHandler.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

try {
    SessionManager::start();
    
    if (SessionManager::isValid()) {
        ResponseFormatter::success([
            'user_id' => SessionManager::getUserId(),
            'user_role' => SessionManager::getUserRole(),
            'user_name' => SessionManager::getUserName(),
            'user_email' => SessionManager::getUserEmail(),
            'remaining' => SessionManager::getTimeRemaining(),
            'should_warn' => SessionManager::shouldShowWarning()
        ]);
    } else {
        ResponseFormatter::unauthorized('Session expired or invalid');
    }
    
} catch (Exception $e) {
    $error = ErrorHandler::handle($e, [
        'module' => 'auth',
        'action' => 'check_session'
    ]);
    ResponseFormatter::serverError($error['message'], $error['code']);
}
?>