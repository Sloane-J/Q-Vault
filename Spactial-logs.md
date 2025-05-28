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
Downloadlog

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

### DownloadStatistics.php
- Total downloads (all-time and period-specific)
- Downloads per paper
- Downloads by department/course
- Downloads by exam type (final, resit)
- Downloads by student type (HND, B-Tech, Top-up)
- Downloads by level (100-400)
- Year-over-year comparison charts


### Content Analytics
#### Paper Popularity
-  - Top downloaded papers by department
- - Most popular courses based on paper downloads
- Trending papers (sudden increase in downloads)
- Least accessed papers
- Popularity by exam type

#### Content Growth
- Papers added per month/year
- Growth by department/course


#### Paper Management Analytics
- Paper uploads (new papers added)
- Paper updates/modifications
- Paper deletions or archiving

### Storage Analytics
- Total storage used
- Storage growth trends
- Storage usage by department
- File size distributions

## Audit Components (app/Livewire/Admin/Audit/)

### Core Audit Components

#### Admin Activity Logs
- Admin Activities - Login/logout and other events for admin users

#### User Activity Monitoring
- Login/logout events
- Password changes
- Account creation events

#### System Events
- **ErrorLogs.php** - Error logs (critical errors)

### Audit Page Features

#### Filtering Capabilities
- Combined filter component
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

## Implementation Notes

### Technology Stack
- **Laravel Livewire** for all components
- **Apex charts** for data visualization
- **Alpine.js** for enhanced interactivity (only when needed)
- **Spatie Laravel Activitylog** for data source

### Component Organization
```
app/Livewire/Admin/
├── Analytics/
│   ├── Downloads/
│   ├── Users/
│   ├── Content/
│   └── Storage/
└── Audit/
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