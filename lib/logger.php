<?php

// ── Dev log accumulator ───────────────────────────────────────────────────────
// Collects SQL queries and view renders in chronological order so the shutdown
// hook can print them in a Rails-style block (development only).

class DevLog {
    public static array $entries = [];
    public static float $viewMs  = 0.0;

    public static function query(string $sql, float $ms): void {
        self::$entries[] = ['type' => 'query', 'sql' => $sql, 'ms' => round($ms, 1)];
    }

    public static function render(string $view, float $ms): void {
        self::$entries[] = ['type' => 'render', 'view' => $view, 'ms' => round($ms, 1)];
        self::$viewMs += $ms;
    }
}

// ── PDO wrappers ──────────────────────────────────────────────────────────────

// Extends PDOStatement to time and count every execute() call.
// PDO instantiates this automatically via ATTR_STATEMENT_CLASS.
class LoggingPDOStatement extends PDOStatement {
    public static int   $count = 0;
    public static float $ms    = 0.0;

    protected function __construct() {}

    public function execute(?array $params = null): bool {
        $t      = microtime(true);
        $result = parent::execute($params);
        $elapsed = (microtime(true) - $t) * 1000;
        self::$ms    += $elapsed;
        self::$count++;
        if (defined('DEV_LOGGING') && DEV_LOGGING) {
            DevLog::query($this->queryString, $elapsed);
        }
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
        $elapsed = (microtime(true) - $t) * 1000;
        LoggingPDOStatement::$ms    += $elapsed;
        LoggingPDOStatement::$count++;
        if (defined('DEV_LOGGING') && DEV_LOGGING) {
            DevLog::query($query, $elapsed);
        }
        return $result;
    }

    public function exec(string $statement): int|false {
        $t      = microtime(true);
        $result = parent::exec($statement);
        $elapsed = (microtime(true) - $t) * 1000;
        LoggingPDOStatement::$ms    += $elapsed;
        LoggingPDOStatement::$count++;
        if (defined('DEV_LOGGING') && DEV_LOGGING) {
            DevLog::query($statement, $elapsed);
        }
        return $result;
    }
}

// ── Request log ───────────────────────────────────────────────────────────────
// Call once at the top of index.php.
//
// Development — Rails-style verbose block:
//
//   Started GET "/" for 127.0.0.1 at 2026-03-15 10:23:45
//     Post Load (1.2ms)  SELECT * FROM posts ORDER BY created_at DESC LIMIT 10
//     Rendered blog/index (2.3ms)
//     Rendered shared/_header (0.4ms)
//   Completed 200 OK in 42ms (Views: 38.5ms | DB: 1.5ms)
//
// Production — single Lograge-style summary line:
//
//   GET  /blog              200  42.3ms  |  db: 2 queries (5.1ms)

function start_request_log(): void {
    define('REQUEST_START_TIME', microtime(true));
    define('DEV_LOGGING', ($_ENV['APP_ENV'] ?? 'development') !== 'production');

    register_shutdown_function(function () {
        $method   = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
        $path     = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $status   = http_response_code() ?: 200;
        $duration = round((microtime(true) - REQUEST_START_TIME) * 1000, 1);
        $queries  = LoggingPDOStatement::$count;
        $db_ms    = round(LoggingPDOStatement::$ms, 1);

        if (DEV_LOGGING) {
            $ip   = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            $lines   = [];
            $lines[] = "\033[1mStarted $method \"$path\" for $ip\033[0m";

            foreach (DevLog::$entries as $e) {
                if ($e['type'] === 'query') {
                    $label = _dev_log_label($e['sql']);
                    $sql   = preg_replace('/\s+/', ' ', trim($e['sql']));
                    $lines[] = "  \033[36m$label ({$e['ms']}ms)\033[0m  $sql";
                } else {
                    $lines[] = "  \033[32mRendered {$e['view']} ({$e['ms']}ms)\033[0m";
                }
            }

            $view_ms     = round(DevLog::$viewMs, 1);
            $status_text = _dev_log_status_text($status);
            $summary     = "\033[1mCompleted $status $status_text in {$duration}ms";
            $parts       = [];
            if ($view_ms > 0) $parts[] = "Views: {$view_ms}ms";
            if ($queries > 0) $parts[] = "DB: {$db_ms}ms";
            if ($parts) $summary .= ' (' . implode(' | ', $parts) . ')';
            $summary .= "\033[0m";
            $lines[] = $summary;

            file_put_contents('php://stderr', implode("\n", $lines) . "\n\n\n");
        } else {
            // Lograge-style single line for production
            $db_part = $queries > 0
                ? '  |  db: ' . $queries . ' ' . ($queries === 1 ? 'query' : 'queries') . ' (' . $db_ms . 'ms)'
                : '';
            file_put_contents('php://stderr', sprintf('%-6s %-40s %d  %sms%s' . "\n",
                $method,
                $path,
                $status,
                $duration,
                $db_part
            ));
        }
    });
}

// ── Helpers (prefixed to avoid polluting global namespace) ────────────────────

function _dev_log_label(string $sql): string {
    $trimmed = ltrim($sql);
    if (stripos($trimmed, 'SELECT') === 0)      { $action = 'Load';    }
    elseif (stripos($trimmed, 'INSERT') === 0)  { $action = 'Create';  }
    elseif (stripos($trimmed, 'UPDATE') === 0)  { $action = 'Update';  }
    elseif (stripos($trimmed, 'DELETE') === 0)  { $action = 'Destroy'; }
    else                                         { return 'SQL';        }

    if (preg_match('/\bFROM\s+`?(\w+)`?/i', $sql, $m) ||
        preg_match('/\bINTO\s+`?(\w+)`?\s/i', $sql, $m) ||
        preg_match('/^\s*UPDATE\s+`?(\w+)`?/i', $sql, $m)) {
        return ucfirst(_dev_log_singularize($m[1])) . ' ' . $action;
    }

    return "SQL $action";
}

function _dev_log_singularize(string $word): string {
    if (str_ends_with($word, 'ies'))  return substr($word, 0, -3) . 'y';
    if (str_ends_with($word, 'sses') ||
        str_ends_with($word, 'xes')  ||
        str_ends_with($word, 'ches')) return substr($word, 0, -2);
    if (str_ends_with($word, 's') && !str_ends_with($word, 'ss')) return substr($word, 0, -1);
    return $word;
}

function _dev_log_status_text(int $code): string {
    return match ($code) {
        200 => 'OK',         201 => 'Created',           204 => 'No Content',
        301 => 'Moved Permanently', 302 => 'Found',      303 => 'See Other',
        304 => 'Not Modified',
        400 => 'Bad Request', 401 => 'Unauthorized',     403 => 'Forbidden',
        404 => 'Not Found',   405 => 'Method Not Allowed',
        422 => 'Unprocessable Entity', 429 => 'Too Many Requests',
        500 => 'Internal Server Error',
        default => '',
    };
}
