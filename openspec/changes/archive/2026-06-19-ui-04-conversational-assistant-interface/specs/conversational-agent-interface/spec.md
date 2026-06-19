## ADDED Requirements

### Requirement: Message history is loaded from database on page render

The conversation page SHALL load all persisted messages from `agent_conversation_messages` when the page renders, ordered by creation time.

#### Scenario: All messages are displayed on page load
- **WHEN** the HR user visits a conversation page that has persisted messages
- **THEN** all messages SHALL be displayed in the chat in chronological order
- **AND** the chat SHALL auto-scroll to the most recent message

#### Scenario: Empty conversation shows welcome state
- **WHEN** the HR user visits a conversation page with no messages
- **THEN** a centered welcome card SHALL be displayed with the candidate name and suggested questions
- **AND** the message input SHALL be focused

### Requirement: Messages are sent via AJAX

The conversation form SHALL send messages using the Fetch API instead of a full-page form POST, and append the new messages to the chat without reloading.

#### Scenario: AJAX POST sends message and receives AI response
- **WHEN** the HR user types a message and presses Enter
- **THEN** the message SHALL be sent via a `POST` AJAX request to the conversation store endpoint
- **AND** the assistant's response SHALL be appended to the chat without a page reload
- **AND** the input SHALL be cleared and re-focused after sending

### Requirement: Streaming assistant response via SSE

The system SHALL provide an SSE endpoint that streams the assistant's response token by token, and the chat SHALL update the assistant message in real time.

#### Scenario: Response appears token by token
- **WHEN** the assistant is generating a response
- **THEN** the chat SHALL display a new assistant message bubble
- **AND** the content SHALL update incrementally as each token is received
- **AND** the chat SHALL auto-scroll to follow the streaming content

#### Scenario: Streaming shows typing indicator before first token
- **WHEN** the user sends a message and the assistant has not yet produced a token
- **THEN** a typing indicator with animated dots SHALL be displayed in an assistant-style bubble
- **AND** the typing indicator SHALL be replaced by the streaming response when the first token arrives

### Requirement: Message bubbles have ChatGPT-style layout

Each message SHALL be rendered as a styled bubble with avatar, content, and timestamp. User and assistant messages SHALL have distinct visual styles.

#### Scenario: Assistant message bubble
- **WHEN** the message role is `assistant`
- **THEN** the bubble SHALL be left-aligned
- **AND** SHALL display a briefcase SVG icon inside a neutral circle avatar
- **AND** SHALL have a `bg-neutral-100` background with `rounded-2xl rounded-bl-sm` border radius
- **AND** SHALL render basic markdown: bold (`**text**`), inline code (`` `code` ``), and unordered lists (`- item`)

#### Scenario: User message bubble
- **WHEN** the message role is `user`
- **THEN** the bubble SHALL be right-aligned
- **AND** SHALL display a user SVG icon inside a primary-colored circle avatar
- **AND** SHALL have a `bg-primary-600` background with white text and `rounded-2xl rounded-br-sm` border radius

#### Scenario: Timestamps are displayed
- **WHEN** a message is displayed
- **THEN** a timestamp SHALL appear below each message bubble in `text-xs text-neutral-400`
- **AND** the timestamp SHALL be formatted as `HH:MM` for today or `DD/MM HH:MM` for older messages

### Requirement: Message states indicate delivery status

Each user message SHALL display a subtle state indicator showing whether the message has been sent, delivered, or failed.

#### Scenario: Sent state
- **WHEN** the message has been persisted to the database but the assistant has not yet responded
- **THEN** a single grey checkmark icon SHALL appear next to the timestamp

#### Scenario: Delivered state
- **WHEN** the assistant's response has been received and displayed
- **THEN** a double checkmark icon SHALL appear next to the timestamp
- **AND** both checkmarks SHALL be coloured with the primary colour

#### Scenario: Error state
- **WHEN** the AJAX request fails or the streaming connection is lost
- **THEN** an error icon and "Échec d'envoi" text SHALL appear
- **AND** a "Renvoyer" button SHALL be displayed

### Requirement: Input area has chat-style enhancements

The input area SHALL include an auto-resizing textarea, Enter to send (without Shift), and Ctrl+Enter for newline.

#### Scenario: Enter sends message
- **WHEN** the user presses Enter in the textarea
- **THEN** the message SHALL be sent
- **WHEN** the user presses Shift+Enter or Ctrl+Enter
- **THEN** a newline SHALL be inserted instead of sending

#### Scenario: Textarea auto-resizes
- **WHEN** the user types a message longer than one line
- **THEN** the textarea SHALL grow vertically up to a maximum of 6 lines
- **AND** the input SHALL show a character counter when approaching the 2000 character limit

#### Scenario: Input is disabled during streaming
- **WHEN** a message is being sent or the assistant is streaming
- **THEN** the textarea and send button SHALL be disabled
- **AND** a subtle "Assistant répond..." hint SHALL appear in the input area

### Requirement: Auto-scroll with scroll anchor

The chat container SHALL auto-scroll to the bottom when new messages arrive, with a scroll anchor button when the user scrolls up.

#### Scenario: Auto-scroll on new message
- **WHEN** a new message is added or the streaming content updates
- **THEN** the chat container SHALL smoothly scroll to the bottom
- **AND** scrolling SHALL NOT be interrupted during streaming

#### Scenario: Scroll anchor button
- **WHEN** the user scrolls up in the chat history
- **THEN** a floating down-arrow button SHALL appear at the bottom of the chat
- **WHEN** the user clicks the button
- **THEN** the chat SHALL scroll to the bottom

## MODIFIED Requirements

### Requirement: Chat UI renders message history

#### Scenario: Messages are displayed in chronological order
- **WHEN** the user visits the conversation page
- **THEN** all previous messages SHALL be loaded from `agent_conversation_messages` and displayed in chronological order
- **AND** the most recent message SHALL be visible (auto-scroll)

#### Scenario: Loading state is shown during AI response
- **WHEN** the user submits a message via AJAX
- **THEN** a typing indicator with animated dots SHALL be displayed while the AI generates a response
- **AND** the input SHALL be disabled during loading
- **AND** the response SHALL stream token by token via SSE
