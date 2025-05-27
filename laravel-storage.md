Use the private disk for exam papers (requires authentication to access)
Consider organizing files by department/year/course in subdirectories
Implement proper MIME type validation for PDF files
Set up appropriate file size limits

 private disk means files are NOT directly accessible via URL - only your application can serve them, which gives you complete control over who can access what.
How it works:
Public Disk (BAD for exam papers):

Files stored in storage/app/public/ and symlinked to public/storage/
Anyone with the direct URL can download: https://yoursite.com/storage/papers/exam.pdf
No authentication required - just knowing the filename gives access
Search engines could potentially index these files

Private Disk (PERFECT for exam papers):

Files stored in storage/app/private/ (or custom path)
NO direct URL access possible
All downloads must go through your Laravel routes/controllers
You control every single download request

What this means for your exam system:
Access Control:

Only logged-in users can download
You can restrict by user role (students vs admins)
Can implement department-specific access
Track who downloads what and when

Security Benefits:

No "guessing" URLs to get unauthorized papers
Can implement download limits per user
Easy to revoke access to specific papers
Audit trail of all file access

Business Logic:

Can show download count before actual download
Implement "preview" vs "full download" permissions
Add watermarks or user identification to downloaded files
Expire download links after certain time