<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/Post.php';
require_once __DIR__ . '/../app/models/User.php';
Model::setDb($pdo);

// ── Admin user ────────────────────────────────────────────────────────────────
if (empty(User::all())) {
    $email    = $_ENV['ADMIN_EMAIL']    ?? 'admin@example.com';
    $password = $_ENV['ADMIN_PASSWORD'] ?? 'password';
    $user = new User(['email' => $email, 'role' => 'admin']);
    $user->setPassword($password);
    $user->save();
    echo "Admin user created: $email (change the password after first sign in)\n";
}

// ── Posts ─────────────────────────────────────────────────────────────────────
if (empty(Post::all())) {
    $posts = [
        ['Getting Started with PHP', 'PHP is a widely-used open source scripting language suited for web development. It is fast to learn and runs on the server side.'],
        ['Understanding Variables', 'Variables in PHP start with a dollar sign. They can hold strings, integers, floats, booleans, arrays, and more.'],
        ['Working with Arrays', 'Arrays are one of the most useful data types in PHP. You can create indexed arrays, associative arrays, and even multidimensional arrays.'],
        ['Control Flow in PHP', 'PHP supports if/else, switch, while, for, and foreach statements. These let you control the flow of your program based on conditions.'],
        ['Functions and Scope', 'Functions let you reuse blocks of code. Variables inside a function are scoped locally unless declared global.'],
        ['Connecting to a Database', 'PHP uses PDO (PHP Data Objects) to connect to databases like MySQL. It provides a consistent interface and supports prepared statements.'],
        ['What Are Prepared Statements?', 'Prepared statements protect your app from SQL injection by separating SQL code from user data. Always use them when handling user input.'],
        ['Building HTML Forms', 'HTML forms send data to your server via GET or POST. In PHP you can read that data from the $_GET and $_POST superglobals.'],
        ['Sessions and Cookies', 'Sessions let you store user data across requests on the server. Cookies store small pieces of data in the browser.'],
        ['Introduction to MVC', 'MVC stands for Model-View-Controller. It is a pattern for organising code that separates data logic, display, and user input handling.'],
        ['Understanding HTTP Methods', 'HTTP defines methods like GET, POST, PUT, PATCH, and DELETE. Each has a specific purpose when communicating between a client and server.'],
        ['What is REST?', 'REST is an architectural style for building APIs. It uses standard HTTP methods and treats everything as a resource identified by a URL.'],
        ['PHP String Functions', 'PHP has a rich set of built-in string functions including strlen, str_replace, substr, strtolower, and explode to name just a few.'],
        ['Working with Dates in PHP', 'The date() and strtotime() functions make it easy to format and manipulate dates in PHP. The DateTime class offers an object-oriented approach.'],
        ['Error Handling in PHP', 'PHP provides try/catch blocks for exception handling. You can throw custom exceptions and catch them to handle errors gracefully.'],
        ['File Uploads in PHP', 'PHP handles file uploads through the $_FILES superglobal. Always validate file type and size before moving an upload to its final location.'],
        ['Sending Email with PHP', 'The mail() function sends basic emails from PHP. For production use, a library like PHPMailer gives you SMTP support and better reliability.'],
        ['Understanding JSON', 'JSON is the standard format for exchanging data in web APIs. PHP can encode arrays to JSON with json_encode and decode with json_decode.'],
        ['Namespaces in PHP', 'Namespaces help organise code and avoid naming conflicts. They are especially important in larger projects and when using Composer packages.'],
        ['Autoloading with Composer', 'Composer is the standard dependency manager for PHP. It also provides autoloading so you do not need to manually require every class file.'],
        ['What is Middleware?', 'Middleware sits between the request and the response in a web application. It can handle authentication, logging, and input validation.'],
        ['Introduction to Tailwind CSS', 'Tailwind CSS is a utility-first CSS framework. Instead of writing custom CSS, you compose styles directly in your HTML using small utility classes.'],
        ['How Browsers Render Pages', 'When a browser receives HTML it builds a DOM tree, then applies CSS to create a render tree, and finally paints pixels to the screen.'],
        ['Understanding the DOM', 'The Document Object Model is a tree representation of an HTML page. JavaScript can read and modify the DOM to create dynamic interfaces.'],
        ['JavaScript Fetch API', 'The Fetch API allows JavaScript to make HTTP requests from the browser. It returns promises and replaces the older XMLHttpRequest approach.'],
        ['Async and Await in JavaScript', 'Async functions let you write asynchronous code that reads like synchronous code. The await keyword pauses execution until a promise resolves.'],
        ['Introduction to Docker', 'Docker packages applications into containers that include everything needed to run them. This ensures consistent environments across development and production.'],
        ['Docker Compose Basics', 'Docker Compose lets you define multi-container applications in a single YAML file. You can start all services with a single docker compose up command.'],
        ['What is a UUID?', 'A UUID is a 128-bit identifier that is practically guaranteed to be unique without a central authority. Version 4 UUIDs are randomly generated.'],
        ['Database Indexing', 'Indexes speed up database queries by allowing MySQL to find rows without scanning the entire table. They come at the cost of slightly slower writes.'],
        ['Primary and Foreign Keys', 'A primary key uniquely identifies each row in a table. A foreign key links a row in one table to a row in another, enforcing referential integrity.'],
        ['SQL Joins Explained', 'SQL joins combine rows from two or more tables. INNER JOIN returns matching rows, LEFT JOIN includes all rows from the left table, and so on.'],
        ['Database Migrations', 'Migrations are version-controlled scripts that make incremental changes to a database schema. They let a team keep their databases in sync.'],
        ['What is an ORM?', 'An Object-Relational Mapper lets you interact with a database using objects instead of writing raw SQL. Popular PHP ORMs include Eloquent and Doctrine.'],
        ['HTTP Status Codes', 'HTTP status codes tell the client what happened on the server. 200 means OK, 201 Created, 301 Redirect, 404 Not Found, 422 Validation Error, 500 Server Error.'],
        ['What is CORS?', 'Cross-Origin Resource Sharing controls which websites can make requests to your API. Browsers enforce this policy to protect users from malicious scripts.'],
        ['Introduction to Git', 'Git is a distributed version control system. It tracks changes to files over time and lets multiple developers collaborate on the same codebase.'],
        ['Git Branching Strategies', 'Branches let you work on features in isolation. Common strategies include feature branches, Git Flow, and trunk-based development.'],
        ['What is CI/CD?', 'Continuous Integration and Continuous Deployment automate testing and deploying code. Pushing to a branch can trigger tests and deploy to production automatically.'],
        ['Environment Variables', 'Environment variables keep sensitive config like database passwords out of your code. They are set on the server and read at runtime.'],
        ['Introduction to APIs', 'An API is a contract between two pieces of software. It defines the requests that can be made, the data that will be returned, and the format to use.'],
        ['JSON:API Specification', 'JSON:API is a specification for building APIs in JSON. It standardises how resources, relationships, errors, and pagination are represented.'],
        ['Pagination in APIs', 'Pagination splits large result sets into pages. JSON:API uses page-based pagination with links for first, last, prev, and next pages.'],
        ['API Versioning', 'Versioning your API allows you to make breaking changes without affecting existing clients. A common approach is to include the version in the URL path.'],
        ['What is Swagger?', 'Swagger is a set of tools built around the OpenAPI specification. It lets you describe your API in a YAML or JSON file and generates interactive documentation.'],
        ['OpenAPI Specification', 'OpenAPI is a standard for describing RESTful APIs. It defines endpoints, request bodies, response schemas, and authentication in a machine-readable format.'],
        ['Introduction to Hotwire', 'Hotwire is an approach to building web applications that sends HTML over the wire instead of JSON. It includes Turbo and Stimulus.'],
        ['How Turbo Drive Works', 'Turbo Drive intercepts link clicks and form submissions and replaces the page body via fetch. This gives a SPA-like feel without writing JavaScript.'],
        ['Turbo Frames', 'Turbo Frames scope updates to a section of the page. Only the matching frame in the server response is used, leaving the rest of the page unchanged.'],
        ['Turbo Streams', 'Turbo Streams allow the server to send fine-grained updates to multiple parts of the page at once using a set of action tags like append, replace, and remove.'],
        ['What is phpMyAdmin?', 'phpMyAdmin is a web-based interface for managing MySQL databases. It lets you run queries, browse tables, and manage users without using the command line.'],
        ['MySQL Data Types', 'MySQL supports many data types including INT, VARCHAR, TEXT, BOOLEAN, DATE, TIMESTAMP, and CHAR. Choosing the right type improves performance and storage.'],
        ['Soft Deletes', 'Soft deletes mark a record as deleted without actually removing it from the database. This is useful for auditing and allows records to be restored.'],
        ['Database Transactions', 'Transactions group multiple SQL statements into a single unit of work. If one statement fails, the whole transaction is rolled back to maintain consistency.'],
        ['Caching Strategies', 'Caching stores the results of expensive operations so they can be reused. Common strategies include full-page caching, query caching, and object caching.'],
        ['What is Redis?', 'Redis is an in-memory data store often used for caching and session storage. It supports strings, hashes, lists, sets, and sorted sets.'],
        ['Rate Limiting APIs', 'Rate limiting prevents abuse by restricting how many requests a client can make in a time window. It protects your server and ensures fair usage.'],
        ['Authentication vs Authorisation', 'Authentication verifies who you are. Authorisation determines what you are allowed to do. Both are essential layers of security in any web application.'],
        ['Introduction to JWT', 'JSON Web Tokens are a compact way to transmit information between parties as a signed JSON object. They are commonly used for API authentication.'],
        ['Hashing Passwords', 'Never store plain text passwords. PHP provides password_hash() and password_verify() to securely hash and verify passwords using bcrypt.'],
        ['Cross-Site Scripting (XSS)', 'XSS attacks inject malicious scripts into web pages viewed by other users. Always escape output with htmlspecialchars() before rendering user content.'],
        ['SQL Injection', 'SQL injection is one of the most common vulnerabilities. An attacker can manipulate queries by injecting SQL through user input. Always use prepared statements.'],
        ['CSRF Protection', 'Cross-Site Request Forgery tricks users into submitting requests they did not intend to make. CSRF tokens added to forms prevent these attacks.'],
        ['Input Validation', 'Always validate data coming into your application. Check types, lengths, and formats. Never trust user input, even from authenticated users.'],
        ['Content Security Policy', 'A Content Security Policy header tells the browser which resources are allowed to load. It is a powerful defence against XSS attacks.'],
        ['HTTPS and TLS', 'HTTPS encrypts traffic between the client and server using TLS. It prevents eavesdropping and tampering. All production sites should use HTTPS.'],
        ['What is a CDN?', 'A Content Delivery Network serves static assets from servers close to the user. This reduces latency and improves page load times globally.'],
        ['Web Performance Basics', 'Fast websites retain users and rank better in search engines. Key techniques include minifying assets, lazy loading images, and reducing HTTP requests.'],
        ['Responsive Design', 'Responsive design makes websites look good on any screen size. CSS media queries and flexible grid layouts are the foundation of responsive design.'],
        ['Accessibility in Web Development', 'Accessible websites work for everyone including users with disabilities. Use semantic HTML, alt text for images, and ensure sufficient colour contrast.'],
        ['SEO Basics for Developers', 'Good HTML structure helps search engines understand your content. Use semantic tags, descriptive title elements, and meta descriptions on every page.'],
        ['What is a Webhook?', 'A webhook is an HTTP callback that fires when an event occurs. Instead of polling for updates, a remote service pushes data to your application.'],
        ['Building a Contact Form', 'A contact form collects user messages and stores or emails them. Always validate input server-side, use CSRF protection, and confirm submission to the user.'],
        ['Email Deliverability', 'Getting emails into the inbox requires proper DNS records like SPF, DKIM, and DMARC. Using a transactional email service improves deliverability.'],
        ['Working with Time Zones', 'Always store dates in UTC and convert to the user\'s local time zone for display. PHP\'s DateTimeZone class and the date_default_timezone_set function help with this.'],
        ['Logging in PHP', 'Logging records events in your application for debugging and monitoring. PHP\'s error_log function and libraries like Monolog write to files or external services.'],
        ['Debugging with Xdebug', 'Xdebug is a PHP extension that provides step debugging, stack traces, and profiling. It integrates with IDEs like VS Code and PhpStorm.'],
        ['Unit Testing in PHP', 'PHPUnit is the standard testing framework for PHP. Unit tests verify individual functions in isolation to catch bugs before they reach production.'],
        ['Test-Driven Development', 'In TDD you write a failing test before writing the code to make it pass. This leads to better designed, more testable code.'],
        ['Code Reviews', 'Code reviews catch bugs, spread knowledge, and improve code quality. A good review focuses on logic, readability, and potential edge cases.'],
        ['Semantic Versioning', 'Semantic versioning uses MAJOR.MINOR.PATCH numbers. A major bump means breaking changes, minor means new features, and patch means bug fixes.'],
        ['Writing Good Commit Messages', 'A good commit message explains why a change was made, not just what changed. Use the imperative mood and keep the subject line under 72 characters.'],
        ['Introduction to Linux', 'Linux is the most common operating system for web servers. Basic commands like ls, cd, grep, cat, and chmod are essential knowledge for every developer.'],
        ['SSH and Remote Servers', 'SSH lets you securely connect to remote servers over an encrypted connection. You can use key-based authentication for better security than passwords.'],
        ['What is Nginx?', 'Nginx is a high-performance web server and reverse proxy. It is commonly used to serve PHP applications through PHP-FPM and to handle SSL termination.'],
        ['PHP-FPM', 'PHP-FPM is a FastCGI process manager for PHP. It handles PHP requests efficiently and is the standard way to run PHP behind Nginx in production.'],
        ['Deploying PHP Applications', 'Deploying a PHP app involves uploading files, running migrations, and restarting the server. Tools like Deployer automate these steps.'],
        ['Load Balancing', 'A load balancer distributes incoming requests across multiple servers. This improves availability and allows horizontal scaling as traffic grows.'],
        ['Monitoring and Alerting', 'Monitoring tracks the health of your application. Set up alerts for high error rates, slow responses, and server resource exhaustion.'],
        ['Documentation as Code', 'Keeping documentation close to the code ensures it stays up to date. Tools like OpenAPI let you describe APIs in a file that also generates documentation.'],
        ['Clean Code Principles', 'Clean code is readable, simple, and easy to change. Follow principles like DRY (Don\'t Repeat Yourself), single responsibility, and meaningful naming.'],
        ['SOLID Principles', 'SOLID is a set of five design principles for object-oriented code. They help create software that is easier to maintain, extend, and understand.'],
    ];

    foreach ($posts as [$title, $body]) {
        (new Post(['title' => $title, 'body' => $body]))->save();
    }

    echo "Seeded " . count($posts) . " posts.\n";
}
