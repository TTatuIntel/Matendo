# Matendo

This repository contains a small Laravel test application and standalone PHP scripts.

## Document Storage

Uploaded resumes, licenses and certifications are now stored directly in the database using `LONG_BLOB` columns. Files are uploaded via `join.php` as binary data and can be downloaded through the `admin.applications.download` route.

