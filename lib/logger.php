<?php

// Extends PDOStatement to time and count every execute() call.
// PDO instantiates this automatically via ATTR_STATEMENT_CLASS.
class LoggingPDOStatement extends PDOStatement {
    public static int   $count = 0;
    public static float $ms    = 0.0;

    protected function __construct() {}

    public function execute(?array $params = null): bool {
        $t      = microtime(true);
        $result = parent::execute($params);
        self::$ms    += (microtime(true) - $t) * 1000;
        self::$count++;
        return $result;
    }
}

// Wraps PDO so query() and exec() are also counted.
// prepare()->execute() is handled by LoggingPDOStatement above.
class LoggingPDO extends PDO {
    public function __construct(string $dsn, string $user, string $pass, array $opts = []) {
        parent::__construct($dsn, $user, $pass, $opts);
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, ['LoggingPDOStatement']);
    }

    public function query(string $query, mixed ...$args): PDOStatement|false {
        $t      = microtime(true);
        $result = parent::query($query, ...$args);
        LoggingPDOStatement::$ms    += (microtime(true) - $t) * 1000;
        LoggingPDOStatement::$count++;
        return $result;
    }

    public function exec(string $statement): int|false {
        $t      = microtime(true);
        $result = parent::exec($statement);
        LoggingPDOStatement::$ms    += (microtime(true) - $t) * 1000;
        LoggingPDOStatement::$count++;
        return $result;
    }
}

// Call once at the top of index.php. Registers a shutdown hook that writes
// a single log line after the response is sent — similar to Rails/Lograge:
//
//   GET  /blog              200  42.3ms  |  db: 2 queries (5.1ms)
//   POST /contact           302   8.7ms  |  db: 1 query  (2.3ms)
//   GET  /api/v1/posts      200  11.2ms  |  db: 1 query  (1.8ms)
//   GET  /assets/app.css    200   0.4ms

function start_request_log(): void {
    define('REQUEST_START_TIME', microtime(true));

    register_shutdown_function(function () {
        $method   = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
        $path     = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $status   = http_response_code() ?: 200;
        $duration = round((microtime(true) - REQUEST_START_TIME) * 1000, 1);
        $queries  = LoggingPDOStatement::$count;
        $db_ms    = round(LoggingPDOStatement::$ms, 1);

        $db_part = $queries > 0
            ? '  |  db: ' . $queries . ' ' . ($queries === 1 ? 'query' : 'queries') . ' (' . $db_ms . 'ms)'
            : '';

        error_log(sprintf('%-6s %-40s %d  %sms%s',
            $method,
            $path,
            $status,
            $duration,
            $db_part
        ));
    });
}
