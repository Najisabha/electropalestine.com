# PowerShell script to remove .env from Git history
# ⚠️ WARNING: This will rewrite Git history. Make sure you have a backup!

Write-Host "⚠️  WARNING: This script will rewrite Git history!" -ForegroundColor Red
Write-Host "Make sure you have a backup of your repository!" -ForegroundColor Yellow
Write-Host ""
$confirm = Read-Host "Are you sure you want to continue? (yes/no)"

if ($confirm -ne "yes") {
    Write-Host "Operation cancelled." -ForegroundColor Yellow
    exit
}

Write-Host ""
Write-Host "Step 1: Removing .env from Git tracking..." -ForegroundColor Cyan
git rm --cached .env

Write-Host ""
Write-Host "Step 2: Removing .env from Git history using filter-branch..." -ForegroundColor Cyan
Write-Host "This may take a while..." -ForegroundColor Yellow

git filter-branch --force --index-filter `
  "git rm --cached --ignore-unmatch .env" `
  --prune-empty --tag-name-filter cat -- --all

Write-Host ""
Write-Host "Step 3: Cleaning up Git references..." -ForegroundColor Cyan
git for-each-ref --format="delete %(refname)" refs/original | git update-ref --stdin
git reflog expire --expire=now --all
git gc --prune=now --aggressive

Write-Host ""
Write-Host "✅ Done! .env has been removed from Git history." -ForegroundColor Green
Write-Host ""
Write-Host "⚠️  IMPORTANT NEXT STEPS:" -ForegroundColor Red
Write-Host "1. Change all sensitive values in your .env file (APP_KEY, DB_PASSWORD, API keys, etc.)" -ForegroundColor Yellow
Write-Host "2. Force push to remote: git push origin --force --all" -ForegroundColor Yellow
Write-Host "3. Force push tags: git push origin --force --tags" -ForegroundColor Yellow
Write-Host "4. If the repository is public, consider making it private temporarily" -ForegroundColor Yellow
Write-Host ""
Write-Host "⚠️  WARNING: Force push will affect all collaborators!" -ForegroundColor Red
