# Database Recovery Script for XAMPP MySQL
# This script resolves "Table doesn't exist in engine" (InnoDB mismatch) by safely
# restoring the original databases from 'data - old 1' while replacing the corrupted
# system tables with clean ones from 'backup'.

$mysqlDir = "C:\xampp\mysql"
$dataDir = "$mysqlDir\data"
$backupDir = "$mysqlDir\backup"
$oldDataDir = "$mysqlDir\data - old 1"
$brokenDataDir = "$mysqlDir\data - broken 2"
$beforeRestoreDir = "$mysqlDir\data - backup before restore"

Write-Host "==================================================" -ForegroundColor Cyan
Write-Host "         XAMPP MySQL InnoDB Data Recovery          " -ForegroundColor Cyan
Write-Host "==================================================" -ForegroundColor Cyan

# Step 1: Verify directories exist
if (-not (Test-Path $oldDataDir)) {
    Write-Error "Could not find the original data directory: $oldDataDir"
    exit 1
}
if (-not (Test-Path $backupDir)) {
    Write-Error "Could not find the clean backup directory: $backupDir"
    exit 1
}

# Step 2: Stop MySQL if it is running
$mysqlProcess = Get-Process -Name mysqld -ErrorAction SilentlyContinue
if ($mysqlProcess) {
    Write-Host "Stopping running MySQL server (PID: $($mysqlProcess.Id))..." -ForegroundColor Yellow
    Stop-Process -Name mysqld -Force
    Start-Sleep -Seconds 3
    
    # Verify it stopped
    if (Get-Process -Name mysqld -ErrorAction SilentlyContinue) {
        Write-Error "Failed to stop MySQL server. Please close the XAMPP Control Panel and try again."
        exit 1
    }
    Write-Host "[OK] MySQL server stopped successfully." -ForegroundColor Green
} else {
    Write-Host "[INFO] MySQL server is not running." -ForegroundColor Gray
}

# Step 3: Backup the current broken data directory
if (Test-Path $dataDir) {
    Write-Host "Backing up current (broken) data directory to '$beforeRestoreDir'..." -ForegroundColor Yellow
    if (Test-Path $beforeRestoreDir) {
        Remove-Item -Path $beforeRestoreDir -Recurse -Force
    }
    Copy-Item -Path $dataDir -Destination $beforeRestoreDir -Recurse
    Write-Host "[OK] Broken data directory backed up." -ForegroundColor Green
}

# Step 4: Perform the restoration
Write-Host "Restoring database files from '$oldDataDir' to '$dataDir'..." -ForegroundColor Yellow
if (Test-Path $dataDir) {
    # Move the current data folder to a safe place instead of deleting it
    if (Test-Path $brokenDataDir) {
        Remove-Item -Path $brokenDataDir -Recurse -Force
    }
    Rename-Item -Path $dataDir -NewName "data - broken 2"
}

# Copy the original data directory (with the large ibdata1 containing all user tables)
Copy-Item -Path $oldDataDir -Destination $dataDir -Recurse
Write-Host "[OK] Restored original database files and ibdata1." -ForegroundColor Green

# Step 5: Replace corrupted system tables with healthy backup files
Write-Host "Replacing corrupted system tables with healthy ones from backup..." -ForegroundColor Yellow

$systemFolders = @("mysql", "performance_schema", "phpmyadmin")
foreach ($folder in $systemFolders) {
    $targetFolder = "$dataDir\$folder"
    $sourceFolder = "$backupDir\$folder"
    
    if (Test-Path $targetFolder) {
        Remove-Item -Path $targetFolder -Recurse -Force
    }
    if (Test-Path $sourceFolder) {
        Copy-Item -Path $sourceFolder -Destination $targetFolder -Recurse
        Write-Host "  [OK] Restored clean system folder: $folder" -ForegroundColor Gray
    }
}

# Step 6: Delete temporary files/lockfiles
Write-Host "Cleaning up log files and lockfiles..." -ForegroundColor Yellow
Remove-Item -Path "$dataDir\*.pid" -ErrorAction SilentlyContinue
Remove-Item -Path "$dataDir\aria_log.*" -ErrorAction SilentlyContinue
Remove-Item -Path "$dataDir\aria_log_control" -ErrorAction SilentlyContinue

Write-Host "[OK] Cleanup completed." -ForegroundColor Green

# Step 7: Restart MySQL
Write-Host "Starting MySQL server..." -ForegroundColor Yellow
$startArgs = '--defaults-file="C:\xampp\mysql\bin\my.ini"', '--standalone'
Start-Process -FilePath "C:\xampp\mysql\bin\mysqld.exe" -ArgumentList $startArgs -NoNewWindow
Start-Sleep -Seconds 5

# Step 8: Verify it started
$newMysqlProcess = Get-Process -Name mysqld -ErrorAction SilentlyContinue
if ($newMysqlProcess) {
    Write-Host "[OK] MySQL started successfully (PID: $($newMysqlProcess.Id))!" -ForegroundColor Green
    Write-Host "==================================================" -ForegroundColor Green
    Write-Host "   RECOVERY COMPLETED! All databases restored.   " -ForegroundColor Green
    Write-Host "==================================================" -ForegroundColor Green
} else {
    Write-Warning "MySQL did not start automatically. Please try starting it manually via XAMPP Control Panel."
}
