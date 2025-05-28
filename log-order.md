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

Custom Events to Log:
Login attempts (successful/failed)

3. Department Model (Admin Changes)
Attributes to Track:

name, description

Custom Events to Log:

Department created
Department updated
Department deleted
Papers reassigned between departments

4. Student_Type Model (Configuration Changes)
Attributes to Track:

name (HND, B-Tech, Top-up)


Optional Models (Lower Priority)
Download Model
Attributes to Track:

user_id, paper_id, downloaded_at


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