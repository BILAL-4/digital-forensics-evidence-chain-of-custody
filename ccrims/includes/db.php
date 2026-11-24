<?php
// includes/db.php — PDO connection
// Edit credentials to match your XAMPP setup

define('DB_HOST', getenv('CCRIMS_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('CCRIMS_DB_NAME') ?: 'ccrims');
define('DB_USER', getenv('CCRIMS_DB_USER') ?: 'root');
define('DB_PASS', getenv('CCRIMS_DB_PASS') ?: '');
define('DB_CHARSET', getenv('CCRIMS_DB_CHARSET') ?: 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die(json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

function appendTamperEvidentAudit(
    PDO $db,
    ?string $actorId,
    string $actorRole,
    string $action,
    string $resourceType,
    ?string $resourceId
): void {
    $previous = $db->query(
        "SELECT EntryHash FROM TAMPER_EVIDENT_AUDIT ORDER BY Timestamp DESC, AuditID DESC LIMIT 1"
    )->fetchColumn();
    $previous = $previous ?: str_repeat('0', 64);
    $payload = implode('|', [
        gmdate('Y-m-d\TH:i:s\Z'),
        $actorId ?: '',
        $actorRole,
        $action,
        $resourceType,
        $resourceId ?: '',
        $previous,
    ]);
    $entryHash = hash('sha256', $payload);
    $auditId = strtolower(sprintf(
        '%08s-%04s-%04s-%04s-%12s',
        substr(bin2hex(random_bytes(4)), 0, 8),
        substr(bin2hex(random_bytes(2)), 0, 4),
        substr(bin2hex(random_bytes(2)), 0, 4),
        substr(bin2hex(random_bytes(2)), 0, 4),
        substr(bin2hex(random_bytes(6)), 0, 12)
    ));

    $db->prepare(
        "INSERT INTO TAMPER_EVIDENT_AUDIT
         (AuditID, ActorID, ActorRole, Action, ResourceType, ResourceID, PreviousHash, EntryHash)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
    )->execute([$auditId, $actorId, $actorRole, $action, $resourceType, $resourceId, $previous, $entryHash]);
}
