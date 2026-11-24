# Digital Forensics Evidence & Chain-of-Custody System

A role-based digital-forensics case-management system for collecting,
tracking, and reporting cyber investigation evidence. This project is framed
around evidence integrity and chain-of-custody controls, not as a routine CRUD
database exercise.

## Security architecture

- **SHA-256 evidence integrity:** evidence submissions require a valid
  64-character SHA-256 digest and store the digest as a unique evidence identity.
- **Strict RBAC:** citizen, officer, analyst, forensic expert, and administrator
  roles receive separate application capabilities.
- **Tamper-evident audit trail:** audit entries are linked with `PreviousHash`
  and `EntryHash`, producing a hash-chained operational log.
- **Chain of custody:** each evidence record identifies the collector, case,
  acquisition date, file metadata, and custody narrative.
- **SQL injection defense:** application database operations use PDO prepared
  statements and bound parameters.
- **Session security:** login performs session-ID regeneration and administrative
  and mutation routes use CSRF verification.

## Run locally

1. Install XAMPP or PHP 8+ with PDO MySQL.
2. Import `ccrims/database.sql`.
3. Configure `CCRIMS_DB_*` variables from `.env.example`.
4. Serve the project from a web server root containing `ccrims/`.

The seed database contains demonstration identities and credentials. Replace
or remove all sample personal data before public release.
