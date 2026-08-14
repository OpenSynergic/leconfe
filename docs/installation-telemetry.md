# Installation and usage telemetry

Leconfe keeps one combined, default-on setting for installation registration
and daily aggregate telemetry. The setting is stored in site metadata. The
installation token is encrypted before it is persisted and is never sent to
the browser.

The post-upgrade notice is informational and non-blocking: existing
installations remain default-on until an administrator opts out. The legacy
APP_BEACON=false setting remains an explicit opt-out during the transition.

The version check only requests the current release. Registration and snapshot
submission use separate versioned API requests and a dedicated installation
token. The authenticated-request and scheduler triggers both call the same
sender; a persisted local date prevents duplicate sends on the same day.

The v1 snapshot contains only environment/plugin versions, inventory counts,
workflow counts, payment counts, and feature-adoption counts by scheduled
conference. Payment data is counts/configuration only. It does not contain
submission titles/content, participant/author/reviewer identities, review
content, filenames/file content, payment amounts/currencies/methods,
invoice/receipt numbers, or payer identity.

Snapshot dates use UTC so the client and Control Panel agree at timezone
boundaries. The current Leconfe schema has no `registrations` table; the
registration metric is therefore omitted rather than represented as a false
zero, while participant records remain reported separately.

Installation ID, base URL/domain, admin name/email, and the request IP are
identifying registry fields. The data is not anonymous. Opting out stops all
future registration and snapshot requests; it does not delete historical
records. Missing metrics remain absent, and the client never fabricates
previous-day snapshots.
