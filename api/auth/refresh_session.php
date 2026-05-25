<?php
/**
 * Refresh Session
 * 
 * Extends the current session timeout
 */

require_once '../../utils/SessionManager.php';
require_once '../../utils/ResponseFormatter.php';
require_once '../../utils/ErrorHandler.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

try {
    SessionManager::start();
    
    if (SessionManager::isValid()) {
        SessionManager::refresh();
        
        ResponseFormatter::success([
            'remaining' => SessionManager::getTimeRemaining(),
            'message' => 'Session refreshed successfully'
        ]);
    } else {
        ResponseFormatter::unauthorized('Session expired or invalid');
    }
    
} catch (Exception $e) {
    $error = ErrorHandler::handle($e, [
        'module' => 'auth',
        'action' => 'refresh_session'
    ]);
    ResponseFormatter::serverError($error['message'], $error['code']);
}
?>
