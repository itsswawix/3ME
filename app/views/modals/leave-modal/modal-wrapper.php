<!-- modal-wrapper.php - Leave Modal Wrapper -->
<!-- This file is included in leave.php and loads all leave modal components -->

<?php
// Include all leave modal components
$modalPath = __DIR__;

// Helper modals
if (file_exists($modalPath . '/modal-leave-helpers.php')) {
    include $modalPath . '/modal-leave-helpers.php';
}

// Leave type modals
if (file_exists($modalPath . '/modal-add-leave-type.php')) {
    include $modalPath . '/modal-add-leave-type.php';
}
if (file_exists($modalPath . '/modal-edit-leave-type.php')) {
    include $modalPath . '/modal-edit-leave-type.php';
}

// Leave request modals
if (file_exists($modalPath . '/modal-add-leave-request.php')) {
    include $modalPath . '/modal-add-leave-request.php';
}
if (file_exists($modalPath . '/modal-edit-leave-request.php')) {
    include $modalPath . '/modal-edit-leave-request.php';
}
if (file_exists($modalPath . '/modal-view-leave-request.php')) {
    include $modalPath . '/modal-view-leave-request.php';
}

// Leave balance modals
if (file_exists($modalPath . '/modal-view-balance.php')) {
    include $modalPath . '/modal-view-balance.php';
}
?>

<script>
// Initialize leave modals
(function() {
    console.log('✅ Leave modals loaded successfully');
    
    // Verify all required functions are available
    const requiredFunctions = [
        'openAddLeaveTypeModal',
        'editLeaveType',
        'viewLeaveType',
        'openAddLeaveRequestModal',
        'editLeaveRequest',
        'viewLeaveRequest',
        'viewBalanceDetails',
        'requestLeaveForEmployee'
    ];
    
    const missingFunctions = requiredFunctions.filter(fn => typeof window[fn] !== 'function');
    
    if (missingFunctions.length > 0) {
        console.warn('⚠️ Missing leave modal functions:', missingFunctions);
    } else {
        console.log('✅ All leave modal functions are available');
    }
})();
</script>
