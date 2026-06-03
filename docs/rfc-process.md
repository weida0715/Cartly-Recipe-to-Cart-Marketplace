# RFC Process (Request for Comments)

## Purpose

The RFC process ensures that important architectural and design decisions are:
- Discussed before implementation
- Documented clearly
- Reviewed collaboratively
- Agreed upon by maintainers

This reduces rework and prevents conflicting designs.

---

## When RFC is Required

You must create an RFC if you are proposing:
- Major architectural changes
- New core modules or services
- Database schema changes
- API redesigns
- Cross-system integrations
- Large-scale refactors

For small changes, use normal issues or pull requests.

---

## How to Submit an RFC

1. Create a new GitHub Issue
2. Select template: **RFC (Request for Comments)**
3. Fill in all required sections:
   - Summary
   - Motivation
   - Proposed Solution
   - Alternatives
   - Impact
   - Risks
   - Implementation Plan

4. Add label: rfc

---

## RFC Lifecycle

### 1. Draft
- RFC is submitted
- Open for discussion

### 2. Review
- Maintainers and contributors comment
- Revisions may be requested

### 3. Decision
- Accepted → can proceed to implementation
- Rejected → archived
- Needs revision → updated and re-reviewed

### 4. Implementation
- Linked PRs reference RFC issue
- Implementation follows approved design

---

## Discussion Period

Minimum waiting time before approval:
- 2–5 days depending on complexity

---

## Best Practices

- Keep RFCs focused and specific
- Include diagrams if needed
- Compare alternatives honestly
- Think long-term, not just quick fixes