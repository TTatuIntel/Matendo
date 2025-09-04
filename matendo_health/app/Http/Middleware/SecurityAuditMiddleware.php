<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SecurityAuditMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pre-request security checks
        $this->performPreRequestChecks($request);

        // Log request for audit trail
        $this->logRequest($request);

        $response = $next($request);

        // Post-request security checks
        $this->performPostRequestChecks($request, $response);

        return $response;
    }

    /**
     * Perform security checks before processing request
     */
    protected function performPreRequestChecks(Request $request): void
    {
        // Check for suspicious IP addresses
        $this->checkSuspiciousIp($request);

        // Check for suspicious patterns in request
        $this->checkSuspiciousPatterns($request);

        // Check for excessive requests from single IP
        $this->checkRateLimit($request);

        // Check for common attack patterns
        $this->checkAttackPatterns($request);

        // Validate user agent
        $this->validateUserAgent($request);
    }

    /**
     * Perform security checks after processing request
     */
    protected function performPostRequestChecks(Request $request, Response $response): void
    {
        // Log failed authentication attempts
        if ($response->getStatusCode() === 401) {
            $this->logFailedAuthentication($request);
        }

        // Log unauthorized access attempts
        if ($response->getStatusCode() === 403) {
            $this->logUnauthorizedAccess($request);
        }

        // Check for potential data leakage in response
        $this->checkResponseForSensitiveData($response);
    }

    /**
     * Check if IP address is suspicious or blocked
     */
    protected function checkSuspiciousIp(Request $request): void
    {
        $ip = $request->ip();
        
        // Check if IP is in blocklist
        $blockedIps = Cache::remember('blocked_ips', 3600, function () {
            return DB::table('blocked_ips')
                ->where('expires_at', '>', now())
                ->orWhereNull('expires_at')
                ->pluck('ip_address')
                ->toArray();
        });

        if (in_array($ip, $blockedIps)) {
            Log::channel('security')->warning('Blocked IP attempted access', [
                'ip' => $ip,
                'url' => $request->url(),
                'user_agent' => $request->userAgent(),
            ]);
            
            abort(403, 'Access denied');
        }

        // Check for known malicious IP ranges (example implementation)
        $this->checkMaliciousIpRanges($ip);
    }

    /**
     * Check for suspicious patterns in the request
     */
    protected function checkSuspiciousPatterns(Request $request): void
    {
        $suspiciousPatterns = [
            // SQL Injection patterns
            'union\s+select',
            'drop\s+table',
            'delete\s+from',
            'insert\s+into',
            'update\s+.*set',
            'exec\s*\(',
            'sp_executesql',
            'xp_cmdshell',
            
            // XSS patterns
            '<script[^>]*>',
            'javascript:',
            'vbscript:',
            'onload\s*=',
            'onerror\s*=',
            'onclick\s*=',
            
            // Path traversal
            '\.\./.*/',
            '\.\.\\\\',
            '/etc/passwd',
            '/etc/shadow',
            'windows/system32',
            
            // Command injection
            ';\s*cat\s+',
            ';\s*ls\s+',
            ';\s*ps\s+',
            ';\s*wget\s+',
            ';\s*curl\s+',
            'cmd\.exe',
            'powershell',
        ];

        $requestContent = strtolower($request->getContent());
        $queryString = strtolower($request->getQueryString());
        $allInput = strtolower(json_encode($request->all()));

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match('/' . $pattern . '/i', $requestContent) ||
                preg_match('/' . $pattern . '/i', $queryString) ||
                preg_match('/' . $pattern . '/i', $allInput)) {
                
                Log::channel('security')->alert('Suspicious pattern detected', [
                    'pattern' => $pattern,
                    'ip' => $request->ip(),
                    'url' => $request->url(),
                    'method' => $request->method(),
                    'user_agent' => $request->userAgent(),
                    'user_id' => auth()->id(),
                    'request_content' => $requestContent,
                ]);

                // Optionally block the request
                if ($this->shouldBlockSuspiciousPattern($pattern)) {
                    abort(400, 'Malicious request detected');
                }
            }
        }
    }

    /**
     * Check rate limiting per IP
     */
    protected function checkRateLimit(Request $request): void
    {
        $ip = $request->ip();
        $key = "rate_limit:{$ip}";
        
        $requests = Cache::get($key, 0);
        $maxRequests = config('security.rate_limit.max_requests', 100);
        $window = config('security.rate_limit.window', 3600); // 1 hour

        if ($requests >= $maxRequests) {
            Log::channel('security')->warning('Rate limit exceeded', [
                'ip' => $ip,
                'requests' => $requests,
                'max_requests' => $maxRequests,
                'url' => $request->url(),
            ]);
            
            abort(429, 'Too Many Requests');
        }

        Cache::put($key, $requests + 1, $window);
    }

    /**
     * Check for common attack patterns
     */
    protected function checkAttackPatterns(Request $request): void
    {
        $userAgent = strtolower($request->userAgent() ?? '');
        $suspiciousUserAgents = [
            'sqlmap',
            'nikto',
            'nessus',
            'openvas',
            'masscan',
            'nmap',
            'dirb',
            'dirbuster',
            'gobuster',
            'burpsuite',
        ];

        foreach ($suspiciousUserAgents as $suspicious) {
            if (strpos($userAgent, $suspicious) !== false) {
                Log::channel('security')->alert('Security scanner detected', [
                    'user_agent' => $request->userAgent(),
                    'ip' => $request->ip(),
                    'url' => $request->url(),
                    'scanner_type' => $suspicious,
                ]);
                
                // Block known security scanners
                abort(403, 'Access denied');
            }
        }
    }

    /**
     * Validate user agent
     */
    protected function validateUserAgent(Request $request): void
    {
        $userAgent = $request->userAgent();
        
        // Check for empty or suspicious user agents
        if (empty($userAgent) || strlen($userAgent) < 10) {
            Log::channel('security')->info('Suspicious user agent', [
                'user_agent' => $userAgent,
                'ip' => $request->ip(),
                'url' => $request->url(),
            ]);
        }
    }

    /**
     * Log the request for audit purposes
     */
    protected function logRequest(Request $request): void
    {
        // Only log requests that might be interesting from security perspective
        if ($this->shouldLogRequest($request)) {
            Log::channel('audit')->info('Request logged', [
                'method' => $request->method(),
                'url' => $request->url(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'route' => $request->route() ? $request->route()->getName() : null,
                'parameters' => $request->route() ? $request->route()->parameters() : [],
            ]);
        }
    }

    /**
     * Log failed authentication attempts
     */
    protected function logFailedAuthentication(Request $request): void
    {
        $ip = $request->ip();
        $key = "failed_auth:{$ip}";
        
        $attempts = Cache::get($key, 0) + 1;
        Cache::put($key, $attempts, 3600); // 1 hour

        Log::channel('security')->warning('Failed authentication attempt', [
            'ip' => $ip,
            'url' => $request->url(),
            'user_agent' => $request->userAgent(),
            'attempt_count' => $attempts,
        ]);

        // Auto-block IP after too many failed attempts
        if ($attempts >= config('security.max_failed_attempts', 10)) {
            $this->blockIpAddress($ip, 'Too many failed authentication attempts');
        }
    }

    /**
     * Log unauthorized access attempts
     */
    protected function logUnauthorizedAccess(Request $request): void
    {
        Log::channel('security')->warning('Unauthorized access attempt', [
            'ip' => $request->ip(),
            'url' => $request->url(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'attempted_route' => $request->route() ? $request->route()->getName() : null,
        ]);
    }

    /**
     * Check response for sensitive data leakage
     */
    protected function checkResponseForSensitiveData(Response $response): void
    {
        if ($response->headers->get('Content-Type') === 'application/json') {
            $content = $response->getContent();
            
            $sensitivePatterns = [
                'password',
                'secret',
                'token',
                'api_key',
                'private_key',
                'ssn',
                'social_security',
            ];

            foreach ($sensitivePatterns as $pattern) {
                if (preg_match('/["\']' . $pattern . '["\']/', $content)) {
                    Log::channel('security')->warning('Potential sensitive data in response', [
                        'pattern' => $pattern,
                        'url' => request()->url(),
                        'user_id' => auth()->id(),
                    ]);
                }
            }
        }
    }

    /**
     * Check if IP is in known malicious ranges
     */
    protected function checkMaliciousIpRanges(string $ip): void
    {
        // This is a simplified example - in production, you'd check against 
        // threat intelligence feeds or services like AbuseIPDB
        $maliciousRanges = [
            // Add known malicious IP ranges here
        ];

        foreach ($maliciousRanges as $range) {
            if ($this->ipInRange($ip, $range)) {
                Log::channel('security')->alert('Malicious IP range detected', [
                    'ip' => $ip,
                    'range' => $range,
                ]);
                
                abort(403, 'Access denied');
            }
        }
    }

    /**
     * Check if suspicious pattern should block the request
     */
    protected function shouldBlockSuspiciousPattern(string $pattern): bool
    {
        $highRiskPatterns = [
            'union\s+select',
            'drop\s+table',
            'delete\s+from',
            'xp_cmdshell',
            'cmd\.exe',
            'powershell',
        ];

        return in_array($pattern, $highRiskPatterns);
    }

    /**
     * Determine if request should be logged
     */
    protected function shouldLogRequest(Request $request): bool
    {
        // Log admin requests, auth requests, and API requests
        $pathsToLog = [
            'admin/',
            'api/',
            'auth/',
            'login',
            'register',
        ];

        $path = $request->path();
        foreach ($pathsToLog as $logPath) {
            if (strpos($path, $logPath) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Block an IP address
     */
    protected function blockIpAddress(string $ip, string $reason): void
    {
        DB::table('blocked_ips')->updateOrInsert(
            ['ip_address' => $ip],
            [
                'reason' => $reason,
                'blocked_at' => now(),
                'expires_at' => now()->addHours(24), // Block for 24 hours
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        Log::channel('security')->alert('IP address blocked', [
            'ip' => $ip,
            'reason' => $reason,
        ]);

        // Clear cache to ensure block takes effect immediately
        Cache::forget('blocked_ips');
    }

    /**
     * Check if an IP is within a given range
     */
    protected function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        list($range, $netmask) = explode('/', $range, 2);
        $rangeDecimal = ip2long($range);
        $ipDecimal = ip2long($ip);
        $wildcardDecimal = pow(2, (32 - $netmask)) - 1;
        $netmaskDecimal = ~ $wildcardDecimal;

        return (($ipDecimal & $netmaskDecimal) == ($rangeDecimal & $netmaskDecimal));
    }
}
