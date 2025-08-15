Priority Order Implementation
1. Paper Model (Most Critical)
Attributes to Track:

title,  department_name, semester, exam_type
course_name, exam_year

Custom Events to Log:

Paper uploaded
Paper downloaded (with user info)
File replaced/updated
Paper deleted

2. User Model (Security Important)
Attributes to Track:

name, email
Login attempts (successful/failed)

Optional Models (Lower Priority)
Download Model
Attributes to Track:

user_name(name), paper_id, downloaded_at


Additional Logging Considerations
System-Level Events to Track:

Failed file uploads
Search queries (for analytics)
Admin dashboard access

Security Events:

Multiple failed login attempts
Unauthorized access attempts
Permission changes
Data export activities

promt: let's deal with logging. i have installed spatie activity log package.