# Attendance Modal Components

This directory contains all modal components for the Time & Attendance module.

## 📁 File Structure

```
attendance-modal/
├── modal-wrapper.php              # Main wrapper that includes all modals
├── modal-attendance-helpers.php   # Helper functions and utilities
├── modal-add-roster.php          # Create new roster modal
├── modal-edit-roster.php         # Edit existing roster modal
├── modal-view-roster.php         # View roster details modal
├── modal-add-correction.php      # Create correction request modal
├── modal-edit-correction.php     # Edit correction request modal
├── modal-view-correction.php     # View correction details modal
└── README.md                     # This file
```

## 🎯 Modal Functions

### Roster Management

#### `openAddRosterModal()`
Opens a modal to create a new shift roster.

**Features:**
- Shift name and company selection
- Start/end time with duration preview
- Break duration and late grace period
- Overtime rule configuration
- Effective date picker
- Notes field

**Validation:**
- All required fields must be filled
- Times must be in valid format
- Effective date must be selected

**Example:**
```javascript
openAddRosterModal();
```

---

#### `viewRoster(rosterId)`
Opens a modal to view roster details in read-only format.

**Parameters:**
- `rosterId` (string): The unique ID of the roster

**Features:**
- Displays shift timing with duration
- Shows break and grace period settings
- Displays overtime policy
- Includes Edit and Duplicate buttons

**Example:**
```javascript
viewRoster('RST-1234567890');
```

---

#### `editRoster(rosterId)`
Opens a modal to edit an existing roster.

**Parameters:**
- `rosterId` (string): The unique ID of the roster

**Features:**
- Pre-filled form with existing data
- All fields editable
- Delete button with confirmation
- Real-time duration calculation

**Example:**
```javascript
editRoster('RST-1234567890');
```

---

#### `duplicateRoster(rosterId)`
Creates a copy of an existing roster.

**Parameters:**
- `rosterId` (string): The unique ID of the roster to duplicate

**Features:**
- Copies all roster settings
- Appends "(Copy)" to shift name
- Adds to rosters array
- Shows success notification

**Example:**
```javascript
duplicateRoster('RST-1234567890');
```

---

#### `assignEmployees(rosterId)`
Opens interface to assign employees to a roster (placeholder).

**Parameters:**
- `rosterId` (string): The unique ID of the roster

**Status:** Coming soon

---

### Correction Management

#### `openAddCorrectionModal()`
Opens a modal to create a new attendance correction request.

**Features:**
- Employee search with autocomplete
- Correction type selection
- Original date picker
- Corrected time in/out fields
- Reason textarea
- Document upload support

**Validation:**
- Employee must be selected
- Correction type required
- Original date required
- Reason must be provided

**Example:**
```javascript
openAddCorrectionModal();
```

---

#### `viewCorrection(correctionId)`
Opens a modal to view correction request details.

**Parameters:**
- `correctionId` (string): The unique ID of the correction

**Features:**
- Employee information display
- Status and type badges
- Correction details
- Request and approval information
- Approve/Reject buttons (if pending)

**Example:**
```javascript
viewCorrection('COR-1234567890');
```

---

#### `editCorrection(correctionId)`
Opens a modal to edit a correction request.

**Parameters:**
- `correctionId` (string): The unique ID of the correction

**Features:**
- Pre-filled form with existing data
- Status dropdown
- Delete button with confirmation
- All fields editable

**Example:**
```javascript
editCorrection('COR-1234567890');
```

---

#### `approveCorrection(correctionId)`
Approves a pending correction request.

**Parameters:**
- `correctionId` (string): The unique ID of the correction

**Features:**
- Updates status to "Approved"
- Sets approval user and date
- Refreshes table
- Shows success notification

**Example:**
```javascript
approveCorrection('COR-1234567890');
```

---

#### `rejectCorrection(correctionId)`
Rejects a pending correction request.

**Parameters:**
- `correctionId` (string): The unique ID of the correction

**Features:**
- Updates status to "Rejected"
- Refreshes table
- Shows warning notification

**Example:**
```javascript
rejectCorrection('COR-1234567890');
```

---

### Import Management

#### `viewImportDetails(importId)`
Opens a modal to view import statistics and details.

**Parameters:**
- `importId` (string): The unique ID of the import

**Features:**
- Import statistics with progress bar
- Success rate calculation
- File details
- View Data and Download Log buttons

**Example:**
```javascript
viewImportDetails('IMP-2024-001');
```

---

#### `downloadImportLog(importId)`
Downloads a text log file for an import.

**Parameters:**
- `importId` (string): The unique ID of the import

**Features:**
- Generates formatted log file
- Includes all import statistics
- Auto-downloads file
- Shows success notification

**Example:**
```javascript
downloadImportLog('IMP-2024-001');
```

---

#### `viewErrors(importId)`
Opens a modal to view import errors.

**Parameters:**
- `importId` (string): The unique ID of the import

**Features:**
- Lists all failed records
- Shows row number and error details
- Download Error Report button

**Example:**
```javascript
viewErrors('IMP-2024-001');
```

---

#### `exportPreviewData(importId)`
Exports preview data to CSV file.

**Parameters:**
- `importId` (string): The unique ID of the import

**Features:**
- Generates CSV from preview data
- Includes all headers and rows
- Auto-downloads file
- Shows success notification

**Example:**
```javascript
exportPreviewData('IMP-2024-001');
```

---

## 🛠️ Helper Functions

### `formatTimeForInput(timeString)`
Formats time string for HTML time input (HH:MM).

**Parameters:**
- `timeString` (string): Time in any format

**Returns:** String in HH:MM format

---

### `formatDateForInput(dateString)`
Formats date string for HTML date input (YYYY-MM-DD).

**Parameters:**
- `dateString` (string): Date in any format

**Returns:** String in YYYY-MM-DD format

---

### `formatDateForDisplay(dateString)`
Formats date for display (Jan 15, 2024).

**Parameters:**
- `dateString` (string): Date in any format

**Returns:** String in display format

---

### `calculateDuration(startTime, endTime)`
Calculates duration between two times.

**Parameters:**
- `startTime` (string): Start time in HH:MM format
- `endTime` (string): End time in HH:MM format

**Returns:** String like "9h 30m"

---

### `isValidTime(timeString)`
Validates time format.

**Parameters:**
- `timeString` (string): Time to validate

**Returns:** Boolean

---

### `showValidationErrors(errors)`
Displays validation errors in modal.

**Parameters:**
- `errors` (array): Array of error messages

---

### `showToast(message, type)`
Shows toast notification.

**Parameters:**
- `message` (string): Message to display
- `type` (string): 'success', 'error', 'warning', or 'info'

---

## 🎨 Styling

All modals use the shared modal styles from `modals/modal-wrapper.php`:

- **Modal Panel:** 460px width, right-side slide-in
- **Form Elements:** Consistent styling with focus states
- **Buttons:** Primary, secondary, success, warning, danger variants
- **Badges:** Status and type indicators
- **Validation:** Error display with icons

---

## 📝 Usage in attendance.php

Include all modals at the end of the file:

```php
<!-- Include Modal Components -->
<?php include 'modals/modal-wrapper.php'; ?>
<?php include 'modals/attendance-modal/modal-add-roster.php'; ?>
<?php include 'modals/attendance-modal/modal-edit-roster.php'; ?>
<?php include 'modals/attendance-modal/modal-view-roster.php'; ?>
<?php include 'modals/attendance-modal/modal-add-correction.php'; ?>
<?php include 'modals/attendance-modal/modal-edit-correction.php'; ?>
<?php include 'modals/attendance-modal/modal-view-correction.php'; ?>
<?php include 'modals/attendance-modal/modal-attendance-helpers.php'; ?>
```

---

## 🧪 Testing

Use the test file to verify all functions:

```bash
# Open in browser
test-attendance-modals.html
```

Or test directly in attendance.php by clicking action buttons in the tables.

---

## 🔄 Data Flow

### Roster Creation Flow
1. User clicks "New Roster" button
2. `openAddRosterModal()` opens form
3. User fills form and submits
4. `handleAddRoster()` validates data
5. New roster added to `window.rosters` array
6. Table refreshed with `renderRosterTable()`
7. Success toast displayed

### Correction Approval Flow
1. User clicks approve icon in table
2. `approveCorrection(correctionId)` called
3. Correction status updated to "Approved"
4. Approval user and date set
5. Table refreshed with `renderCorrectionTable()`
6. Success toast displayed

---

## 🚀 Future Enhancements

- [ ] Employee assignment interface for rosters
- [ ] Bulk correction approval
- [ ] Import error auto-fix suggestions
- [ ] Roster templates
- [ ] Correction workflow with multiple approvers
- [ ] Email notifications for approvals
- [ ] Audit trail for changes

---

## 📞 Support

For issues or questions about attendance modals, contact the development team.

---

**Last Updated:** May 21, 2026
**Version:** 1.0.0
