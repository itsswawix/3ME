<!-- modal-wrapper.php - Attendance Modal Wrapper -->
<!-- This file is included in attendance.php and loads all attendance modal components -->

<?php
// Include all attendance modal components
$modalPath = __DIR__;

// Helper modals
if (file_exists($modalPath . '/modal-attendance-helpers.php')) {
    include $modalPath . '/modal-attendance-helpers.php';
}

// Roster modals
if (file_exists($modalPath . '/modal-add-roster.php')) {
    include $modalPath . '/modal-add-roster.php';
}
if (file_exists($modalPath . '/modal-edit-roster.php')) {
    include $modalPath . '/modal-edit-roster.php';
}
if (file_exists($modalPath . '/modal-view-roster.php')) {
    include $modalPath . '/modal-view-roster.php';
}

// Correction modals
if (file_exists($modalPath . '/modal-add-correction.php')) {
    include $modalPath . '/modal-add-correction.php';
}
if (file_exists($modalPath . '/modal-edit-correction.php')) {
    include $modalPath . '/modal-edit-correction.php';
}
if (file_exists($modalPath . '/modal-view-correction.php')) {
    include $modalPath . '/modal-view-correction.php';
}
?>

<script>
// Initialize attendance modals
(function() {
    console.log('✅ Attendance modals loaded successfully');
    
    // Verify all required functions are available
    const requiredFunctions = [
        'openAddRosterModal',
        'editRoster',
        'viewRoster',
        'duplicateRoster',
        'assignEmployees',
        'openAddCorrectionModal',
        'editCorrection',
        'viewCorrection',
        'approveCorrection',
        'rejectCorrection',
        'viewImportDetails',
        'downloadImportLog',
        'viewErrors',
        'exportPreviewData'
    ];
    
    const missingFunctions = requiredFunctions.filter(fn => typeof window[fn] !== 'function');
    
    if (missingFunctions.length > 0) {
        console.warn('⚠️ Missing attendance modal functions:', missingFunctions);
    } else {
        console.log('✅ All attendance modal functions are available');
    }
})();
</script>
