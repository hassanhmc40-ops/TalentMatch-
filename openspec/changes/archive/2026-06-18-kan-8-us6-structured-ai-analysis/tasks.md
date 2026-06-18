## 1. Agent Configuration

- [x] 1.1 Update `CvAnalysisAgent::instructions()` from English to French; verify it returns French system instructions
- [x] 1.2 Verify `CvAnalysisAgent::schema()` defines all 10 required fields with correct types and constraints
- [x] 1.3 Write test: agent instructions contain expected French keywords
- [x] 1.4 Write test: agent schema includes all required fields with correct types
- [x] 2.1 Verify `AnalyseCvJob` builds a prompt containing CV text, offer title, description, required skills, and min experience
- [x] 2.2 Write test: job prompt contains CV text and all offer criteria fields (add assertPrompted test if missing)
- [x] 3.1 Integration test: job calls agent → validates → persists with completed status (already exists)
- [x] 3.2 French-to-English key mapping covers all 10 fields (already exists in ValidateStructuredAnalysisTest)
- [x] 3.3 Invalid AI response (missing field) results in failed status (already exists)
- [x] 3.4 Invalid AI response (wrong type) results in failed status (already exists)
- [x] 3.5 Run `vendor/bin/pint --format agent` to ensure code style
- [x] 3.6 Run full test suite: `php artisan test --compact`
