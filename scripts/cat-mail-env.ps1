# Print or apply Gmail SMTP settings to .env
#
# Usage:
#   .\scripts\cat-mail-env.ps1
#   .\scripts\cat-mail-env.ps1 -Apply
#   .\scripts\cat-mail-env.ps1 -Apply -ClearConfig

param(
    [switch]$Apply,
    [switch]$ClearConfig
)

$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
$Block = Join-Path $Root "scripts\mail.env"
$EnvFile = Join-Path $Root ".env"

if (-not (Test-Path $Block)) {
    Write-Host "Missing $Block"
    Write-Host "Run: Copy-Item scripts\mail.env.example scripts\mail.env"
    exit 1
}

Write-Host "# -- Mail (Gmail SMTP) --"
Get-Content $Block

if (-not $Apply) { exit 0 }

if (-not (Test-Path $EnvFile)) {
    Copy-Item (Join-Path $Root ".env.example") $EnvFile
}

$lines = Get-Content $EnvFile | Where-Object {
    $_ -notmatch '^MAIL_' -and $_ -notmatch '^# -- Mail'
}
$mail = Get-Content $Block
$output = @($lines) + @('') + @('# -- Mail (Gmail SMTP) --') + @($mail)
$output | Set-Content $EnvFile -Encoding UTF8

Write-Host ""
Write-Host "Updated $EnvFile"

if ($ClearConfig -and (Get-Command php -ErrorAction SilentlyContinue)) {
    Push-Location $Root
    php artisan config:clear
    php artisan cache:clear
    Pop-Location
    Write-Host "Laravel config cache cleared."
}
