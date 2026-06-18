## 1. Agent Instructions

- [x] 1.1 Rewrite `ConversationalAgent::instructions()` in French with role, tool-usage mandate, and anti-hallucination rules
- [x] 1.2 Add test: instructions contain French keywords
- [x] 1.3 Add test: instructions forbid inventing data without tools

## 2. Tool Authorization

- [x] 2.1 Update `GetCandidateAnalysis::handle()` to scope query to authenticated user's job offers
- [x] 2.2 Update `GetJobRequirements::handle()` to scope query to authenticated user's job offers
- [x] 2.3 Fix `GetJobRequirements::handle()` column name from `min_years_experience` to `min_experience_years`
- [x] 2.4 Update `CompareCandidates::handle()` to scope both analyses to authenticated user's offers
- [x] 2.5 Write tests for each tool: authorized access returns data, unauthorized returns French error

## 3. Chat Controller and Routes

- [x] 3.1 Create `ConversationController` with `show` (GET) and `store` (POST) methods
- [x] 3.2 Register routes: `GET /conversations/{offre}/{candidat}` and `POST /conversations/{offre}/{candidat}`
- [x] 3.3 Wire controller to instantiate `ConversationalAgent` and use SDK conversation API
- [x] 3.4 Load existing messages from memory in `show` method

## 4. Chat UI

- [x] 4.1 Create `resources/views/conversations/show.blade.php` with message list and input form
- [x] 4.2 Add AlpineJS auto-scroll to latest message
- [x] 4.3 Add loading spinner state during AI response
- [x] 4.4 Add link to chat from candidate analysis detail page

## 5. Tests

- [x] 5.1 Write feature test: conversation show page renders for authorized user
- [x] 5.2 Write feature test: posting message returns agent response
- [x] 5.3 Write feature test: conversation memory persists across turns
- [x] 5.4 Write feature test: unauthorized user cannot access conversation data

## 6. Final Verification

- [x] 6.1 Run full test suite: `php artisan test --compact`
- [x] 6.2 Run `vendor/bin/pint --format agent` for code style
