# Leconfe - Conference Management System

Leconfe is an open-source conference management system that allows users to manage conferences, papers, reviews, including publishing the issue. 
This project is created to make the conference management seamless including the management of the participant, venue, registration, payment, and the crucial aspect of the conference event. 

[Leconfe](https://openjournaltheme.com) is a [Open Journal Theme](https://openjournalteam.com) product.

## Features ✨

WIP

## Why is this open-source? 🔓

WIP

## Requirements ⚙️

Leconfe is a regular Laravel application that can be installed on any server that meets the [Laravel server requirements](https://laravel.com/docs/10.x/deployment#server-requirements).

## Local development 💻

WIP

## Contributing 🤝

Thank you for considering contributing to Leconfe! Here are some guidelines to help you get started:

### Structure and Maintainability

-   Avoid adding new dependencies unless absolutely necessary.
-   Use the `__()` helper function instead of hardcoding translations.
-   Each Eloquent model should have a sensible [Database Factory](https://laravel.com/docs/10.x/database-testing#factories).
-   Use [Queued Jobs](https://laravel.com/docs/10.x/queues) to perform long-running tasks. Notify users that a task is running.
-   Use [Notifications](https://laravel.com/docs/10.x/notifications) to send emails to users, or a [Mailable](https://laravel.com/docs/10.x/mail) when it's unimaginable that a notification would be sent to anything other than the main channel.
-   Prefer enums over constants.

### Security and Performance

-   Encrypt all sensitive data in Eloquent models.
-   Each Eloquent model should have a corresponding [Policy](https://laravel.com/docs/10.x/authorization#creating-policies) to handle authorization.
-   Each Eloquent model should have a corresponding [Resource](https://laravel.com/docs/10.x/eloquent-resources) to handle serialization.
-   All actions should be Logged.
-   Always use pagination on index pages.
-   The following Eloquent protections are enabled by default
    -   Prevent Lazy Loading to avoid N+1 queries
    -   Require a morph map when using polymorphic relations

## Security Vulnerabilities

If you discover a security vulnerability within Leconfe, please e-mail Leconfe via [support@openjournaltheme.com](mailto:support@openjournaltheme.com). All security vulnerabilities will be promptly addressed.

## Credits

WIP

## License

WIP
