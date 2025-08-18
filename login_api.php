<?php
// login_api.php
declare(strict_types=1);

// Allow module includes (your modules use this guard)
define("a328763fe27bba", true);

// IMPORTANT: load config.php BEFORE app_init.php so include_all_modules() runs and mysql.php is loaded
require_once __DIR__ . "/config.php";     // loads constants + include_all_modules()
require_once __DIR__ . "/app_init.php";   // uses modules loaded above

header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Jerusalem");

// --- Config (set real values via env or config.php) ---
$BREVO_API_KEY = getenv('BREVO_API_KEY') ?: 'PUT_BREVO_KEY';
$SENDER_EMAIL  = getenv('BREVO_SENDER') ?: 'no-reply@example.com';
$SENDER_NAME   = getenv('BREVO_SENDER_NAME') ?: 'Whisper Login';

// DEV mode: enabled if no real key or curl is missing (helps you test without sending email)
$DEV_MODE = (getenv('DEV_MODE_OTP') === '1') || str_starts_with($BREVO_API_KEY, 'PUT_') || !function_exists('curl_init');

// --- Small helpers ---
function now(): string { return date('Y-m-d H:i:s'); }
function plus_minutes(int $m): string { return date('Y-m-d H:i:s', time() + $m * 60); }
function ip(): string { return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'; }
function ua(): string { return substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255); }
function fail(string $msg, int $code = 400){ http_response_code($code); echo json_encode(['ok'=>false,'error'=>$msg]); exit; }
function ok(array $data = []){ echo json_encode(['ok'=>true] + $data); exit; }

function sendBrevoEmail(string $apiKey, string $fromEmail, string $fromName, string $toEmail, string $subject, string $text): bool {
    $payload = [
        'sender'      => ['email' => $fromEmail, 'name' => $fromName],
        'to'          => [['email' => $toEmail]],
        'subject'     => $subject,
        'textContent' => $text
    ];
    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => ["api-key: {$apiKey}", "Content-Type: application/json", "accept: application/json"],
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
    ]);
    $res  = curl_exec($ch);
    $err  = curl_error($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($err || $code >= 300) { error_log("[BREVO] code={$code} err={$err} res={$res}"); return false; }
    return true;
}

$action   = $_POST['action']    ?? $_GET['action'] ?? null;
$username = trim((string)($_POST['username'] ?? $_GET['username'] ?? ''));
$otp      = trim((string)($_POST['otp'] ?? ''));
$honeypot = $_POST['website']   ?? ''; // honeypot

if (!$action)         fail("missing action");
if ($honeypot !== '') fail("spam detected", 403);

// fetch user (must exist; your users table must have `username` and now `email`)
$user = null;
if ($username !== '') {
    $u = mysql_fetch_array("SELECT username, email FROM users WHERE username = ? LIMIT 1", [$username]);
    $user = $u[0] ?? null;
}

switch ($action) {
    case 'request_otp':
        if ($username === '') fail("missing username");
        if (!$user)          fail("user not found", 404);

        // throttles: 30s cooldown; max 4/hour; 10/day
        $last = mysql_fetch_array("SELECT MAX(sent_at) AS last FROM auth_otps WHERE username = ? AND used_at IS NULL", [$username]);
        if ($last && ($last[0]['last'] ?? null) && time() - strtotime($last[0]['last']) < 30) fail("too many requests, wait 30s");

        $hourCnt = mysql_fetch_array("SELECT COUNT(*) AS c FROM auth_otps WHERE username = ? AND sent_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)", [$username]);
        if ((int)($hourCnt[0]['c'] ?? 0) >= 4)  fail("hourly limit reached");

        $dayCnt = mysql_fetch_array("SELECT COUNT(*) AS c FROM auth_otps WHERE username = ? AND sent_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)", [$username]);
        if ((int)($dayCnt[0]['c'] ?? 0) >= 10) fail("daily limit reached");

        // generate & store OTP
        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $hash = password_hash($code, PASSWORD_DEFAULT);

        // IMPORTANT: use mysql_prepared_execute for statements with params
        mysql_prepared_execute(
            "INSERT INTO auth_otps (username, otp_hash, sent_at, expires_at, ip, user_agent)
             VALUES (?, ?, ?, ?, ?, ?)",
            [$username, $hash, now(), plus_minutes(10), ip(), ua()]
        );

        if ($DEV_MODE) {
            // Don’t send email in dev; return code so you can log in
            error_log("[DEV_OTP] username={$username} code={$code}");
            ok(['message' => 'OTP generated (dev mode).', 'dev_code' => $code]);
        } else {
            $sent = sendBrevoEmail($BREVO_API_KEY, $SENDER_EMAIL, $SENDER_NAME, $user['email'], "Your OTP", "Your OTP is: {$code}\nValid 10 minutes.");
            if (!$sent) fail("failed to send OTP", 502);
            ok(['message' => 'OTP sent']);
        }
        break;

    case 'verify_otp':
        if ($username === '') fail("missing username");
        if (!$user)          fail("user not found", 404);
        if ($otp === '')     fail("missing otp");

        $rows = mysql_fetch_array(
            "SELECT id, otp_hash
               FROM auth_otps
              WHERE username = ?
                AND used_at IS NULL
                AND expires_at >= NOW()
              ORDER BY sent_at DESC
              LIMIT 1",
            [$username]
        );
        if (!$rows) fail("no active otp or expired", 401);
        $row = $rows[0];

        if (!password_verify($otp, $row['otp_hash'])) fail("invalid otp", 401);

        mysql_prepared_execute("UPDATE auth_otps SET used_at = ? WHERE id = ?", [now(), $row['id']]);

        $token   = bin2hex(random_bytes(32)); // 64 hex
        $created = now();
        $exp     = date('Y-m-d H:i:s', time() + 24 * 3600);

        mysql_prepared_execute(
            "INSERT INTO auth_tokens (token, username, created_at, expires_at)
             VALUES (?, ?, ?, ?)",
            [$token, $username, $created, $exp]
        );

        ok(['token' => $token, 'expires_at' => $exp, 'username' => $username]);
        break;

    default:
        fail("unknown action");
}
