1. Add Logging to Your Models
Add the LogsActivity trait to the models you want to track (Papers, Users, Departments, etc.). This automatically logs create, update, and delete actions.

# Models to be added to logging
Core Models:

User (role changes, profile updates)
Paper (uploads, edits, deletions, visibility changes)
Department (CRUD operations)
Student_Type (HND, B-Tech, Top-up changes)
Level (level modifications)

Version Control:

Paper_Version (new versions, updates)

Optional Models (depending on your needs):

Download (if you want to track changes to download records)
Search_History (if you modify/delete search records)

Priority Order:

Paper (most critical)
User (security important)
Department (admin changes)
Student_Type and Level (configuration changes)
Paper_Version (version tracking)

2. Configure What Gets Logged
In each model, specify which attributes should be tracked and what should be logged. You can control whether to log all changes or just specific fields.

3. Add Custom Activity Logging
For actions that aren't model changes (like downloads, searches, logins), you'll manually log these activities in your controllers or Livewire components.

4. Update Your Analytics Components
Modify your existing analytics Livewire components to query the activity_log table instead of your custom tables. The package provides helper methods to filter by user, model type, date ranges, etc.

5. Update Your Audit Components
Change your audit viewer components to display data from the activity log table. You'll get richer information like who did what, when, and what changed.

6. Clean Up (Optional)
Once everything is working with the activity log, you can remove your custom logging tables and related code.
Next immediate step. Start by adding the trait to your most important model (probably the Papers model) to test that logging is working properly.

# Plan for views
# Past Exam Paper Management System - Frontend Structure

## Analytics Components (app/Livewire/Admin/Analytics/)

### Download Statistics
- **DownloadTotals.php** - Total downloads (all-time and period-specific)
- **DownloadsByPaper.php** - Downloads per paper
- **DownloadsByDepartment.php** - Downloads by department/course
- **DownloadsByExamType.php** - Downloads by exam type (final, resit)
- **DownloadsByStudentType.php** - Downloads by student type (HND, B-Tech, Top-up)
- **DownloadsByLevel.php** - Downloads by level (100-400)
- **YearOverYearDownloads.php** - Year-over-year comparison charts

### User Engagement
- **ActiveUsers.php** - Active users per day/week/month
- **SessionDuration.php** - Average session duration
- **ReturnVisits.php** - Return visit frequency

### Content Analytics
#### Paper Popularity
- **TopDownloadedPapers.php** - Top downloaded papers by department
- **PopularCourses.php** - Most popular courses based on paper downloads
- **TrendingPapers.php** - Trending papers (sudden increase in downloads)
- **LeastAccessedPapers.php** - Least accessed papers
- **PopularityByExamType.php** - Popularity by exam type

#### Content Growth
- **PapersAddedStats.php** - Papers added per month/year
- **GrowthByDepartment.php** - Growth by department/course
- **VersionUpdates.php** - Version updates frequency
- **ContentFreshness.php** - Content freshness indicators

### Storage Analytics
- **StorageUsage.php** - Total storage used
- **StorageGrowth.php** - Storage growth trends
- **StorageByDepartment.php** - Storage usage by department
- **FileSizeDistribution.php** - File size distributions

## Audit Components (app/Livewire/Admin/Audits/)

### Core Audit Components

#### Admin Activity Logs
- **AdminActions.php** - User creation/modification actions
- **PermissionChanges.php** - Permission changes
- **SystemSettings.php** - System setting modifications
- **AdminLogins.php** - Login/logout events for admin users

#### Paper Management Audit Trail
- **PaperUploads.php** - Paper uploads (new papers added)
- **PaperUpdates.php** - Paper updates/modifications
- **PaperDeletions.php** - Paper deletions or archiving
- **VersionControl.php** - Version control actions (new versions uploaded)
- **VisibilityChanges.php** - Visibility setting changes (public/restricted)

#### User Activity Monitoring
- **UserLogins.php** - Login/logout events
- **PasswordChanges.php** - Password changes
- **ProfileUpdates.php** - Profile updates
- **AccountCreation.php** - Account creation events

#### System Events
- **SystemMaintenance.php** - System maintenance events
- **ErrorLogs.php** - Error logs (critical errors)
- **StorageWarnings.php** - Storage threshold warnings

### Audit Page Features

#### Filtering Capabilities
- **AuditFilters.php** - Combined filter component
  - By date range
  - By action type
  - By user (who performed the action)
  - By object (what was affected)

#### Data Export
- **AuditExport.php** - Export functionality
  - CSV export
  - PDF reports

### Audit Log Structure
Each audit entry contains:
- Timestamp (date and time of action)
- Actor (user who performed the action)
- Action type (create, update, delete, login, etc.)
- Object affected (paper, user, setting, etc.)
- Object ID (specific identifier)
- Previous state (for updates)
- New state (after the action)
- IP address
- Additional context (when relevant)

## Implementation Notes

### Technology Stack
- **Laravel Livewire** for all components
- **Apex charts** for data visualization
- **Alpine.js** for enhanced interactivity
- **Tailwind CSS** for styling
- **Spatie Laravel Activitylog** for data source

### Component Organization
```
app/Livewire/Admin/
├── Analytics/
│   ├── Downloads/
│   ├── Users/
│   ├── Content/
│   └── Storage/
└── Audits/
    ├── Admin/
    ├── Papers/
    ├── Users/
    └── System/
```

### Data Flow
1. Spatie Activitylog captures all activities
2. Analytics components query activity_log table for metrics
3. Audit components display filtered activity logs
4. Real-time updates using Livewire polling
5. Export functionality for compliance reporting