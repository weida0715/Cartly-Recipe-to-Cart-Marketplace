# RFC Workflow

## Purpose

The RFC process ensures that important architectural and design decisions are:
- Discussed before implementation
- Documented clearly
- Reviewed collaboratively
- Agreed upon by maintainers

This reduces rework and prevents conflicting designs.

---

## When to use RFCs

Use an RFC when proposing:
- Architectural changes
- Major refactors
- New system components
- Database redesign
- API redesign
- Cross-module changes

Do NOT use RFCs for:
- Bug fixes
- Small improvements
- Simple features with no architectural impact

---

## Required Label

All RFC issues must include: rfc

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

## Review Process

1. RFC is created as a GitHub Issue
2. Maintained in **Draft / Open discussion state**
3. Minimum discussion period: **2–5 days**
4. Contributors and maintainers review and comment
5. Decision is made:
   - Accepted
   - Rejected
   - Needs revision

---

## Approval Rules

An RFC is considered approved when:
- At least 1–2 core maintainers approve (or your defined team leads)
- No unresolved critical objections remain

---

## Post-Approval

Once accepted:
- RFC becomes implementation reference
- Linked PRs must reference RFC issue
- Implementation tracked separately
