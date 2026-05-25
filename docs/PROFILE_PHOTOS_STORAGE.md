# Profile Photos Storage System

## Overview
This document describes the profile photo storage system for employee management.

## Architecture

### Storage Location
```
/uploads/profile_photos/
├── profile_{employeeId}_{timestamp}.jpg
├── profile_{employeeId}_{timestamp}.png
└── ...
```

### Database Schema
The employee table should include:
```sql
ALTER TABLE employees 
ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL,
ADD COLUMN profile_photo_filename VARCHAR(255) DEFAULT NULL;
```

## API Endpoints

### 1. Upload Profile Photo
**Endpoint:** `/api/employees/upload_profile_photo.php`

**Method:** POST

**Request Body:**
```json
{
  "photo": "data:image/jpeg;base64,/9j/4AAQSkZJRg...",
  "employeeId": "EMP-2024-001",
  "filename": "optional_custom_name.jpg"
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Profile photo uploaded successfully",
  "data": {
    "filename": "profile_EMP-2024-001_1716123456.jpg",
    "url": "/3ME/uploads/profile_photos/profile_EMP-2024-001_1716123456.jpg",
    "size": 45678,
    "type": "jpeg"
  }
}
```

**Response (Error):**
```json
{
  "success": false,
  "message": "Error message here"
}
```

### 2. Delete Profile Photo
**Endpoint:** `/api/employees/delete_profile_photo.php`

**Method:** POST or DELETE

**Request Body (by Employee ID):**
```json
{
  "employeeId": "EMP-2024-001"
}
```

**Request Body (by Filename):**
```json
{
  "filename": "profile_EMP-2024-001_1716123456.jpg"
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Profile photo deleted successfully",
  "filename": "profile_EMP-2024-001_1716123456.jpg"
}
```

## Client-Side Usage

### Upload Photo (Webcam or File)
```javascript
// After capturing or selecting photo
const photoData = 'data:image/jpeg;base64,...';
const employeeId = 'EMP-2024-001';

const result = await uploadProfilePhotoToServer(photoData, employeeId);
console.log('Photo URL:', result.url);
```

### Delete Photo
```javascript
// Delete by employee ID
await deleteProfilePhotoFromServer('EMP-2024-001');

// Or delete by filename
await deleteProfilePhotoFromServer(null, 'profile_EMP-2024-001_1716123456.jpg');
```

### Display Photo
```html
<!-- In employee table -->
<img src="${employee.profilePhoto}" class="emp-avatar-sm" style="object-fit: cover;" />

<!-- Fallback to avatar if no photo -->
${employee.profilePhoto ? `
    <img src="${employee.profilePhoto}" class="emp-avatar-sm" />
` : `
    <div class="emp-avatar-sm" style="background: ${employee.color};">
        ${employee.avatar}
    </div>
`}
```

## Security Features

### 1. File Type Validation
- Only image files allowed (JPEG, PNG, GIF, WebP)
- Validated on both client and server side
- MIME type checking

### 2. File Size Limits
- Client-side: 5MB maximum
- Server-side: PHP upload limits apply
- Configurable in php.ini

### 3. Directory Protection
- `.htaccess` prevents PHP execution
- Directory browsing disabled
- Only image files accessible

### 4. Filename Sanitization
- Automatic filename generation
- Prevents directory traversal attacks
- Uses `basename()` for security

### 5. Automatic Cleanup
- Old photos deleted when new ones uploaded
- Prevents storage bloat
- One photo per employee at a time

## Maintenance

### Cleanup Orphaned Photos
Run the cleanup utility periodically:

```bash
php utils/cleanup_orphaned_photos.php
```

This will:
- Find photos for non-existent employees
- Delete orphaned files
- Report space freed

### Scheduled Cleanup (Cron Job)
Add to crontab for weekly cleanup:

```cron
0 2 * * 0 /usr/bin/php /path/to/3ME/utils/cleanup_orphaned_photos.php
```

### Manual Cleanup
```bash
# Find photos older than 90 days with no matching employee
find /path/to/uploads/profile_photos/ -name "profile_*" -mtime +90 -type f
```

## Performance Optimization

### 1. Image Compression
Consider adding server-side image compression:

```php
// Example using GD library
$image = imagecreatefromjpeg($filepath);
imagejpeg($image, $filepath, 85); // 85% quality
imagedestroy($image);
```

### 2. Thumbnail Generation
Generate thumbnails for list views:

```php
// Create 150x150 thumbnail
$thumb = imagecreatetruecolor(150, 150);
imagecopyresampled($thumb, $image, 0, 0, 0, 0, 150, 150, $width, $height);
imagejpeg($thumb, $thumbPath, 85);
```

### 3. CDN Integration
For production, consider using a CDN:
- Amazon S3
- Cloudflare Images
- Azure Blob Storage

### 4. Lazy Loading
Implement lazy loading for employee lists:

```html
<img src="${employee.profilePhoto}" loading="lazy" />
```

## Backup Strategy

### 1. Include in Regular Backups
```bash
# Backup profile photos
tar -czf profile_photos_backup_$(date +%Y%m%d).tar.gz uploads/profile_photos/
```

### 2. Sync to Remote Storage
```bash
# Using rsync
rsync -avz uploads/profile_photos/ user@backup-server:/backups/profile_photos/
```

### 3. Database Backup
Ensure employee table with photo references is backed up:

```bash
mysqldump -u user -p database employees > employees_backup.sql
```

## Troubleshooting

### Issue: Photos not uploading
**Check:**
1. PHP upload limits: `upload_max_filesize`, `post_max_size`
2. Directory permissions: `chmod 755 uploads/profile_photos/`
3. PHP error logs: `/var/log/php/error.log`

### Issue: Photos not displaying
**Check:**
1. File exists: `ls -la uploads/profile_photos/`
2. Correct URL path in database
3. Browser console for 404 errors
4. `.htaccess` configuration

### Issue: Permission denied
**Fix:**
```bash
# Set correct ownership
chown -R www-data:www-data uploads/profile_photos/

# Set correct permissions
chmod 755 uploads/profile_photos/
chmod 644 uploads/profile_photos/*
```

## Best Practices

1. **Always validate file types** on both client and server
2. **Limit file sizes** to prevent storage issues
3. **Use unique filenames** to avoid conflicts
4. **Clean up old photos** when uploading new ones
5. **Implement proper error handling** for failed uploads
6. **Log upload activities** for audit trails
7. **Test with various image formats** and sizes
8. **Monitor storage usage** regularly
9. **Implement rate limiting** to prevent abuse
10. **Use HTTPS** for secure photo transmission

## Future Enhancements

1. **Image cropping** - Allow users to crop photos before upload
2. **Multiple photos** - Support multiple profile photos per employee
3. **Photo history** - Keep archive of previous photos
4. **Facial recognition** - Auto-detect and center faces
5. **Filters/effects** - Apply filters to photos
6. **Bulk upload** - Upload multiple employee photos at once
7. **Import from external sources** - LinkedIn, Google, etc.
8. **Photo approval workflow** - Require admin approval for photos
9. **Watermarking** - Add company watermark to photos
10. **Analytics** - Track photo upload/view statistics

## Support

For issues or questions:
- Check logs: `/var/log/apache2/error.log`
- Review API responses for error messages
- Contact system administrator

---

**Last Updated:** May 19, 2026
**Version:** 1.0.0
