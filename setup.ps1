param(
    [string]$DbHost = "127.0.0.1",
    [int]$DbPort = 3306,
    [string]$DbName = "integrador",
    [string]$DbUser = "root",
    [string]$DbPassword = "",
    [string]$ComposerPath = "",
    [string]$PhpPath = "C:\laragon\bin\php\php-7.4.19-Win32-vc15-x64\php.exe",
    [string]$MySqlPath = "C:\laragon\bin\mysql\mysql-5.7.33-winx64\bin\mysql.exe",
    [switch]$UseMigrations,
    [switch]$RunTests,
    [switch]$StartServer
)

$ErrorActionPreference = "Stop"
Set-Location -Path $PSScriptRoot

if (-not (Test-Path "artisan")) {
    throw "No se encontró 'artisan'. Ejecuta este script en la raíz del proyecto Lockers."
}

function Write-Step($message) {
    Write-Host "`n==> $message" -ForegroundColor Cyan
}

function Ensure-Command($name) {
    if (-not (Get-Command $name -ErrorAction SilentlyContinue)) {
        throw "No se encontró '$name' en PATH. Instálalo o agrégalo al PATH."
    }
}

function Resolve-ComposerCommand {
    if ($ComposerPath -ne "" -and (Test-Path $ComposerPath)) {
        return @{ Type = "phar"; Value = $ComposerPath }
    }

    $composerCmd = Get-Command "composer" -ErrorAction SilentlyContinue
    if ($composerCmd) {
        return @{ Type = "cmd"; Value = "composer" }
    }

    $laragonComposer = "C:\laragon\bin\composer\composer.phar"
    if (Test-Path $laragonComposer) {
        return @{ Type = "phar"; Value = $laragonComposer }
    }

    throw "No se encontró Composer. Instálalo o pásalo con -ComposerPath C:\ruta\composer.phar"
}

function Invoke-ComposerInstall($composerMeta) {
    if ($composerMeta.Type -eq "cmd") {
        & $composerMeta.Value install
        return
    }

    & $PhpPath $composerMeta.Value install
}

function Invoke-MySqlQuery([string]$query) {
    $args = @("-h", $DbHost, "-P", $DbPort, "-u", $DbUser)
    if ($DbPassword -ne "") {
        $args += "-p$DbPassword"
    }
    $args += @("-e", $query)
    & $MySqlPath @args
}

function Invoke-MySqlImport([string]$database, [string]$filePath) {
    $pwdArg = ""
    if ($DbPassword -ne "") {
        $pwdArg = "-p$DbPassword"
    }

    $command = '"{0}" -h {1} -P {2} -u {3} {4} {5} < "{6}"' -f $MySqlPath, $DbHost, $DbPort, $DbUser, $pwdArg, $database, $filePath
    cmd /c $command
}

Write-Step "Verificando herramientas"
$composerMeta = Resolve-ComposerCommand
if (-not (Test-Path $PhpPath)) {
    throw "No se encontró PHP en: $PhpPath"
}
if (-not (Test-Path $MySqlPath)) {
    throw "No se encontró MySQL en: $MySqlPath"
}

Write-Step "Instalando dependencias PHP"
Invoke-ComposerInstall $composerMeta

Write-Step "Configurando entorno"
if (-not (Test-Path ".env") -and (Test-Path ".env.example")) {
    Copy-Item ".env.example" ".env" -Force
}

& $PhpPath artisan key:generate --force

Write-Step "Creando base de datos si no existe"
Invoke-MySqlQuery "CREATE DATABASE IF NOT EXISTS $DbName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

if ($UseMigrations) {
    Write-Step "Aplicando migraciones y seeders (Opción B)"
    & $PhpPath artisan migrate --force
    & $PhpPath artisan db:seed --class=RoleUsersSeeder --force
} else {
    $dumpPath = Join-Path $PSScriptRoot "database\integrador_workbench_full.sql"

    if (-not (Test-Path $dumpPath)) {
        throw "No se encontró el dump: $dumpPath"
    }

    Write-Step "Importando dump completo (Opción A)"
    Invoke-MySqlImport -database $DbName -filePath $dumpPath

    Write-Step "Asegurando usuarios demo"
    & $PhpPath artisan db:seed --class=RoleUsersSeeder --force
}

if ($RunTests) {
    Write-Step "Corriendo pruebas"
    & $PhpPath artisan test
}

Write-Step "Setup terminado"
Write-Host "Puedes iniciar la app con:" -ForegroundColor Green
Write-Host "  $PhpPath artisan serve" -ForegroundColor Yellow
Write-Host "`nAccesos demo:" -ForegroundColor Green
Write-Host "  admin@utnlockers.test / password123"
Write-Host "  tutor@utnlockers.test / password123"
Write-Host "  estudiante@utnlockers.test / password123"

if ($StartServer) {
    Write-Step "Iniciando servidor Laravel"
    & $PhpPath artisan serve
}
