# GitHub Contributor Handbook

This repository follows an **issue-first workflow**.
All contributors are expected to follow the process below to keep work organized, reviewable, and traceable.

---

## 1. Standard Contribution Workflow

```text
1. Create an issue
2. Discuss the issue
3. Improve the issue/spec/RFC if needed
4. Assign a contributor
5. Contributor creates a branch
6. Contributor links the branch to the issue
7. Contributor completes the listed tasks
8. Contributor commits changes
9. Contributor pushes the branch
10. Contributor opens a pull request
11. Contributor requests Gemini Code Assist review
12. Contributor resolves AI review comments
13. Contributor resolves merge conflicts if any
14. Admin reviews the PR
15. Admin approves the PR
16. PR is merged
17. Issue is closed
```

GitHub supports linking pull requests to issues. Using closing keywords such as `Fixes #12`, `Closes #12`, or `Resolves #12` in the PR description or commit message can automatically close the issue when the PR is merged.

---

## 2. Basic Git Commands

### Check Git version

```bash
git --version
```

### Configure Git identity

```bash
git config --global user.name "Your Name"
git config --global user.email "your-email@example.com"
```

### Check repository status

```bash
git status
```

### View commit history

```bash
git log
git log --oneline
```

### View branches

```bash
git branch
git branch -a
```

### Create a new branch

```bash
git branch feature/issue-12-login-page
```

### Switch branch

```bash
git switch feature/issue-12-login-page
```

Older syntax:

```bash
git checkout feature/issue-12-login-page
```

### Create and switch branch immediately

```bash
git switch -c feature/issue-12-login-page
```

### Stage files

```bash
git add .
```

Stage one file:

```bash
git add path/to/file
```

### Commit changes

```bash
git commit -m "feat(auth): implement login page"
```

Recommended multiline style:

```bash
git commit -m "feat(auth): implement login page" \
  -m "Add login form, validation, API connection, and error display." \
  -m "Refs #12"
```

### Push branch

```bash
git push -u origin feature/issue-12-login-page
```

### Pull latest changes

```bash
git pull
```

Safer option for feature branches:

```bash
git pull --rebase
```

### Fetch without merging

```bash
git fetch origin
```

### Merge another branch into current branch

```bash
git merge main
```

### Rebase current branch onto main

```bash
git fetch origin
git rebase origin/main
```

### Undo unstaged changes

```bash
git restore path/to/file
```

### Unstage a file

```bash
git restore --staged path/to/file
```

### Delete a local branch

```bash
git branch -d feature/issue-12-login-page
```

Force delete:

```bash
git branch -D feature/issue-12-login-page
```

### Delete a remote branch

```bash
git push origin --delete feature/issue-12-login-page
```

---

## 3. Repository Setup

### Clone the repository

```bash
git clone https://github.com/OWNER/REPOSITORY.git
cd REPOSITORY
```

### Check remote URL

```bash
git remote -v
```

### Add remote manually

```bash
git remote add origin https://github.com/OWNER/REPOSITORY.git
```

### Change remote URL

```bash
git remote set-url origin https://github.com/OWNER/REPOSITORY.git
```

### Pull latest main branch

```bash
git switch main
git pull origin main
```

### Create your working branch from latest main

```bash
git switch main
git pull origin main
git switch -c feature/issue-12-login-page
```

---

## 4. GitHub Basics

### Repository

A repository stores the project source code, issues, branches, commits, pull requests, and history.

### Issue

An issue describes work before implementation.

Use issues for:

```text
- Feature request
- Bug report
- RFC
- Spec discussion
- Refactor proposal
- Documentation task
- Testing task
```

### Branch

A branch is an isolated workspace for one issue.

Recommended branch format:

```text
<type>/<short-description>
```

Examples:

```bash
feature/login-page
fix/cart-total-bug
docs/update-readme
refactor/clean-auth-service
test/add-checkout-tests
rfc/001-payment-flow
```

### Commit

A commit records one logical change.

Recommended format:

```text
<type>(<scope>): <description>
```

Examples:

```bash
feat(auth): add login form
fix(cart): correct subtotal calculation
docs(readme): update setup guide
test(order): add checkout validation tests
refactor(api): simplify product service
chore(deps): update package lock
```

### Pull Request

A pull request proposes merging a branch into the target branch, usually `main`.

---

## 5. Issue-First Workflow

### Step 1: Create the issue

Before coding, create an issue.

Issue title examples:

```text
RFC-001: Project Foundation and Repository Setup
feat: add login form
docs: update readme file
fix: correct subtotal calculation
```

### Step 2: Write the issue clearly

Use a structure like this:

```markdown
## Summary

Briefly explain the task.

## Problem

What problem does this solve?

## Goals

- Goal 1
- Goal 2
- Goal 3

## Non-Goals

- What this issue will not cover

## Tasks

- [ ] Task 1
- [ ] Task 2
- [ ] Task 3

## Acceptance Criteria

- [ ] Expected result 1
- [ ] Expected result 2
- [ ] Tests pass
- [ ] Documentation updated if needed

## Related Files

- src/app/views/...
- src/app/controllers/...

## Notes

Extra implementation notes.
```

### Step 3: Discuss and improve the issue

Before assignment, contributors and admins may comment on:

```text
- Missing requirements
- Incorrect scope
- Better implementation plan
- Testing requirements
- UI changes
- Database changes
- Security concerns
```

### Step 4: Assign a contributor

Once the issue is ready, assign a contributor.

Recommended metadata:

```text
Assignee: contributor-name
Label: feature / bug / docs / rfc / test / refactor / chore / ci
Milestone: v0.1.0 / v0.2.0 / etc.
```

---

## 6. Contributor Branch Workflow

### Start from latest main

```bash
git switch main
git pull origin main
```

### Create a branch linked to the issue

```bash
git switch -c feature/issue-12-login-page
```

### Work on the issue tasks

```bash
git status
git add .
git commit -m "feat(auth): implement login page" \
  -m "Complete login form, validation, API call, and error handling." \
  -m "Refs #12"
```

### Push the branch

```bash
git push -u origin feature/issue-12-login-page
```

### Open a PR

On GitHub:

```text
1. Go to the repository
2. Click Pull requests
3. Click New pull request
4. Set base branch to main
5. Compare against your feature branch
6. Create the pull request
```

---

## 7. Pull Request Template

Use this structure for PRs:

```markdown
## PR Changes Summary

<!-- Describe what this PR does -->

---

## Linked Issues

- Closes #

---

## Checklist

- [x] I have linked the issue using "Closes #<issue_number>"
- [x] I have reviewed all file changes
- [x] No unrelated changes are included
- [x] All tests have been run locally
- [x] All tests pass successfully
- [x] Lint checks have been run
- [x] Code follows project formatting rules
- [x] Gemini Code Assist review has been requested
- [x] AI review comments have been addressed
- [x] Documentation has been updated (if applicable)
- [x] README has been updated (if applicable)
- [x] CHANGELOG has been updated (if applicable)
- [x] Reviewer has been assigned
```

---

## 8. Gemini Code Assist Review Workflow

After opening a PR:

```text
1. Open the PR
2. Request Gemini Code Assist review
3. Wait for AI review comments
4. Read every comment carefully
5. Fix valid comments
6. Ignore only comments that are clearly not applicable
7. Push fixes to the same branch
8. Mark comments resolved
9. Request admin review
```

Commit after AI review:

```bash
git add .
git commit -m "fix(review): address Gemini Code Assist comments" \
  -m "Resolve review comments related to validation, naming, and edge cases." \
  -m "Refs #12"
git push
```

Re-request Gemini Code Assist review:

```bash
/gemini review
```

---

## 9. Admin Review Workflow

Admin checks:

```text
- Issue is properly linked
- PR solves the issue scope
- No unrelated changes
- Tests pass
- Code is readable
- AI review was requested
- AI review comments were resolved
- Merge conflicts are resolved
- Implementation matches the issue/spec/RFC
```

Admin actions:

```text
Approve PR
Request changes
Comment only
Merge PR
Close issue if not automatically closed
```

---

## 10. Merge Conflict Cheatsheet

Merge conflicts happen when Git cannot automatically combine competing changes from different branches.

### Scenario: PR has merge conflict with main

You are on your feature branch:

```bash
git switch feature/issue-12-login-page
```

Fetch latest changes:

```bash
git fetch origin
```

Merge main into your branch:

```bash
git merge origin/main
```

If a conflict happens, Git will show conflicted files.

Check status:

```bash
git status
```

Open conflicted files and fix the conflict markers:

```text
<<<<<<< HEAD
Your branch changes
=======
Main branch changes
>>>>>>> origin/main
```

After fixing:

```bash
git add .
git commit -m "fix(merge): resolve conflict with main" \
  -m "Resolve conflicting changes between feature branch and latest main." \
  -m "Refs #12"
git push
```

---

## 11. Alternative: Resolve Conflict by Rebase

Use this if the project prefers clean history.

```bash
git switch feature/issue-12-login-page
git fetch origin
git rebase origin/main
```

If conflict happens:

```bash
git status
```

Fix files manually, then:

```bash
git add .
git rebase --continue
```

Repeat if more conflicts appear.

After rebase:

```bash
git push --force-with-lease
```

Use `--force-with-lease` instead of plain `--force` because it is safer.

---

## 12. Example Scenario: Contributor Implements an Issue

### Issue

```text
Issue #12: Implement Login Page
```

### Create branch

```bash
git switch main
git pull origin main
git switch -c feature/issue-12-login-page
```

### Make changes

```bash
git status
git add .
git commit -m "feat(auth): implement login page" \
  -m "Add login form, validation, API integration, loading state, and error handling." \
  -m "Refs #12"
```

### Push branch

```bash
git push -u origin feature/issue-12-login-page
```

### Open PR

PR title:

```text
feat(auth): implement login page
```

PR description:

```markdown
## Summary

Implements the login page for customer, merchant, moderator, and admin users.

## Linked Issue

Fixes #12

## Changes Made

- Added login form
- Added validation
- Connected login API
- Added loading state
- Added error message display

## Testing

- [x] Valid login works
- [x] Invalid login shows error
- [x] Empty form shows validation
- [x] No console errors

## AI Review

- [x] Gemini Code Assist requested
- [x] Comments resolved
```

---

## 13. Example Scenario: PR Has Merge Conflict

If GitHub says:

```text
This branch has conflicts that must be resolved.
```

Fix locally:

```bash
git switch feature/issue-12-login-page
git fetch origin
git merge origin/main
```

If Git reports:

```text
CONFLICT (content): Merge conflict in src/app/views/auth/login.php
```

Open the file, remove conflict markers, and keep the correct final code.

Then:

```bash
git add src/app/views/auth/login.php
git commit -m "fix(merge): resolve login page conflict" \
  -m "Merge latest main into login page branch and resolve conflicting page changes." \
  -m "Refs #12"
git push
```

---

## 14. Example Scenario: Forgot to Link Issue

If a PR was opened without linking the issue, edit the PR description and add:

```markdown
Fixes #12
```

or:

```markdown
Closes #12
```

or:

```markdown
Resolves #12
```

---

## 15. Example Scenario: Committed to the Wrong Branch

If you accidentally committed on `main`:

```bash
git switch -c feature/issue-12-login-page
git push -u origin feature/issue-12-login-page
```

Then restore `main` to remote state:

```bash
git switch main
git fetch origin
git reset --hard origin/main
```

Use `reset --hard` only when you are sure you do not need the local changes on `main`.

---

## 16. Example Scenario: Need to Update PR After Review

Make the changes, then:

```bash
git status
git add .
git commit -m "fix(auth): handle empty login response" \
  -m "Address admin review by handling empty API response safely." \
  -m "Refs #12"
git push
```

The existing PR updates automatically.

---

## 17. Example Scenario: Need to Sync Branch With Main

### Merge method

```bash
git switch feature/issue-12-login-page
git fetch origin
git merge origin/main
git push
```

### Rebase method

```bash
git switch feature/issue-12-login-page
git fetch origin
git rebase origin/main
git push --force-with-lease
```

Use merge if the team wants a simpler and safer flow.
Use rebase if the team wants a clean linear history.

---

## 18. Recommended Branch Naming

Good examples:

```text
feat/login-page
fix/cart-total
docs/contributor-handbook
test/checkout-tests
refactor/product-service
rfc/001-store-approval-flow
chore/update-dependencies
```

Avoid:

```text
my-branch
test
final
new
changes
login
weida-work
```

---

## 19. Recommended Commit Types

| Type         | Use For                                    |
| ------------ | ------------------------------------------ |
| `feat`     | New feature                                |
| `fix`      | Bug fix                                    |
| `docs`     | Documentation only                         |
| `style`    | Formatting only                            |
| `refactor` | Code restructuring without behavior change |
| `test`     | Adding or updating tests                   |
| `chore`    | Maintenance work                           |
| `ci`       | CI/CD changes                              |
| `build`    | Build system or dependency changes         |
| `perf`     | Performance improvement                    |
| `revert`   | Reverting a previous commit                |

---

## 20. Good Commit Examples

```bash
git commit -m "feat(recipe): add ingredients to cart flow" \
  -m "Implement ingredient selection, product matching, and cart insertion flow." \
  -m "Refs #24"
```

```bash
git commit -m "fix(cart): prevent duplicate ingredient items" \
  -m "Check existing cart entries before adding recipe ingredients to avoid duplicate rows." \
  -m "Refs #24"
```

```bash
git commit -m "docs(contributing): add pull request workflow" \
  -m "Document issue-first workflow, AI review requirement, merge conflict handling, and admin approval process." \
  -m "Refs #8"
```

---

## 21. Bad Commit Examples

Avoid:

```bash
git commit -m "update"
git commit -m "fix"
git commit -m "done"
git commit -m "changes"
git commit -m "final final"
git commit -m "asdf"
```

Better:

```bash
git commit -m "fix(order): correct order status transition"
```

---

## 22. PR Review Rules

Before requesting admin review, the contributor must check:

```text
- [ ] PR is linked to an issue
- [ ] Branch name includes issue number
- [ ] PR description is complete
- [ ] Gemini Code Assist review requested
- [ ] AI comments resolved
- [ ] Tests pass
- [ ] No merge conflicts
- [ ] No unrelated files changed
- [ ] Screenshots added for UI changes
- [ ] Documentation updated if needed
```

---

## 23. Admin Merge Rules

Admin should only merge when:

```text
- Issue is approved
- Contributor is assigned
- PR links to issue
- Tasks are completed
- Gemini Code Assist review is resolved
- Admin review is approved
- CI/tests pass
- No merge conflicts
- PR matches project scope
```

Recommended merge methods:

```text
Squash and merge: good for feature branches with many small commits.
Merge commit: good when preserving full branch history.
Rebase and merge: good for clean linear history.
```

---

## 24. Full Contributor Command Flow

```bash
# 1. Start from main
git switch main
git pull origin main

# 2. Create issue branch
git switch -c feature/issue-12-login-page

# 3. Work on files
git status

# 4. Stage changes
git add .

# 5. Commit changes
git commit -m "feat(auth): implement login page" \
  -m "Add login form, validation, API integration, loading state, and error handling." \
  -m "Refs #12"

# 6. Push branch
git push -u origin feature/issue-12-login-page

# 7. Open PR on GitHub
# 8. Link PR using: Fixes #12
# 9. Request Gemini Code Assist review
# 10. Resolve review comments

# 11. Sync with main if needed
git fetch origin
git merge origin/main

# 12. Resolve conflicts if needed
git status
git add .
git commit -m "fix(merge): resolve conflict with main" \
  -m "Resolve latest main conflicts before admin review." \
  -m "Refs #12"

# 13. Push final branch
git push
```

---

## 25. Final Project Rule

No direct commits to `main`.

All work must go through:

```text
Issue → Discussion → Assignment → Branch → Commit → PR → AI Review → Conflict Resolution → Admin Review → Merge
```
