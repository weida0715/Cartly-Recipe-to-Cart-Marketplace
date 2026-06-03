# AI Tools Usage Guidelines

## Gemini Code Assist

Gemini Code Assist is used for:
- Code suggestions
- Refactoring recommendations
- Documentation assistance

### Rules
- Must not replace human review
- Must not be the sole reviewer
- Do not send sensitive data (passwords, tokens, DB dumps)
- All AI suggestions must be validated manually

## PR Review Expectations
- AI can assist with review comments
- Final approval must be human

## Qodo (PR-Agent)

Qodo is used for automated PR analysis.

### Features enabled
- PR summary generation
- Change explanation
- Risk detection
- Suggested improvements

### Behavior
- Runs automatically on every PR
- Posts summary as PR comment
- Does not replace human review