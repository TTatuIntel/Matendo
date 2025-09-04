<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InputSanitizationMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Sanitize all input data
        $this->sanitizeInputs($request);

        return $next($request);
    }

    /**
     * Sanitize all request inputs
     */
    protected function sanitizeInputs(Request $request): void
    {
        // Get all input data
        $input = $request->all();
        
        // Sanitize the input data
        $sanitized = $this->recursiveSanitize($input);
        
        // Replace the input data with sanitized versions
        $request->replace($sanitized);
    }

    /**
     * Recursively sanitize data
     */
    protected function recursiveSanitize($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->recursiveSanitize($value);
            }
            return $data;
        }

        if (is_string($data)) {
            return $this->sanitizeString($data);
        }

        return $data;
    }

    /**
     * Sanitize a string value
     */
    protected function sanitizeString(string $value): string
    {
        // Remove null bytes
        $value = str_replace(chr(0), '', $value);
        
        // Handle different types of content
        if ($this->isPotentialHtml($value)) {
            return $this->sanitizeHtml($value);
        }

        if ($this->isPotentialSql($value)) {
            return $this->sanitizeSql($value);
        }

        if ($this->isPotentialScript($value)) {
            return $this->sanitizeScript($value);
        }

        // General sanitization
        return $this->generalSanitize($value);
    }

    /**
     * Check if value might contain HTML
     */
    protected function isPotentialHtml(string $value): bool
    {
        return strpos($value, '<') !== false && strpos($value, '>') !== false;
    }

    /**
     * Check if value might contain SQL
     */
    protected function isPotentialSql(string $value): bool
    {
        $sqlKeywords = [
            'select', 'insert', 'update', 'delete', 'drop', 'create',
            'alter', 'truncate', 'union', 'exec', 'execute'
        ];

        $lowerValue = strtolower($value);
        foreach ($sqlKeywords as $keyword) {
            if (strpos($lowerValue, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if value might contain scripts
     */
    protected function isPotentialScript(string $value): bool
    {
        $scriptPatterns = [
            'javascript:', 'vbscript:', 'data:', 'onload=', 'onerror=', 
            'onclick=', 'onmouseover=', 'onfocus=', '<script'
        ];

        $lowerValue = strtolower($value);
        foreach ($scriptPatterns as $pattern) {
            if (strpos($lowerValue, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sanitize HTML content
     */
    protected function sanitizeHtml(string $value): string
    {
        // Define allowed tags and attributes
        $allowedTags = [
            'p', 'br', 'strong', 'em', 'u', 'i', 'b', 'ul', 'ol', 'li',
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote'
        ];

        $allowedAttributes = ['class', 'id'];

        // Remove dangerous tags and attributes
        $value = $this->removeDangerousTags($value);
        $value = $this->removeDangerousAttributes($value);

        // Strip tags not in allowed list
        $allowedTagsString = '<' . implode('><', $allowedTags) . '>';
        $value = strip_tags($value, $allowedTagsString);

        return $value;
    }

    /**
     * Sanitize potential SQL content
     */
    protected function sanitizeSql(string $value): string
    {
        // Remove common SQL injection patterns
        $patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\bEXEC\b|\bEXECUTE\b)/i',
            '/(\bxp_cmdshell\b)/i',
            '/(\bsp_executesql\b)/i',
            '/(;|\||&)/i', // Remove command separators
        ];

        foreach ($patterns as $pattern) {
            $value = preg_replace($pattern, '', $value);
        }

        return $value;
    }

    /**
     * Sanitize script content
     */
    protected function sanitizeScript(string $value): string
    {
        // Remove JavaScript protocols and event handlers
        $patterns = [
            '/javascript:/i',
            '/vbscript:/i',
            '/data:/i',
            '/on\w+\s*=/i', // Remove event handlers like onclick=, onload=
            '/<script[^>]*>.*?<\/script>/is',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/<object[^>]*>.*?<\/object>/is',
            '/<embed[^>]*>.*?<\/embed>/is',
            '/<applet[^>]*>.*?<\/applet>/is',
        ];

        foreach ($patterns as $pattern) {
            $value = preg_replace($pattern, '', $value);
        }

        return $value;
    }

    /**
     * General sanitization for all strings
     */
    protected function generalSanitize(string $value): string
    {
        // Trim whitespace
        $value = trim($value);

        // Remove control characters except tab, newline, and carriage return
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);

        // Normalize line endings
        $value = preg_replace('/\r\n?/', "\n", $value);

        // Remove excessive whitespace
        $value = preg_replace('/\s{3,}/', '  ', $value);

        return $value;
    }

    /**
     * Remove dangerous HTML tags
     */
    protected function removeDangerousTags(string $value): string
    {
        $dangerousTags = [
            'script', 'iframe', 'object', 'embed', 'applet', 'form',
            'input', 'textarea', 'button', 'select', 'option',
            'link', 'style', 'meta', 'base', 'frame', 'frameset'
        ];

        foreach ($dangerousTags as $tag) {
            $value = preg_replace('/<' . $tag . '[^>]*>.*?<\/' . $tag . '>/is', '', $value);
            $value = preg_replace('/<' . $tag . '[^>]*\/?>/is', '', $value);
        }

        return $value;
    }

    /**
     * Remove dangerous HTML attributes
     */
    protected function removeDangerousAttributes(string $value): string
    {
        $dangerousAttributes = [
            'onload', 'onerror', 'onclick', 'onmouseover', 'onmouseout',
            'onmousemove', 'onmousedown', 'onmouseup', 'onfocus', 'onblur',
            'onchange', 'onsubmit', 'onreset', 'onkeydown', 'onkeyup',
            'onkeypress', 'style', 'expression'
        ];

        foreach ($dangerousAttributes as $attr) {
            $value = preg_replace('/' . $attr . '\s*=\s*["\'][^"\']*["\']/i', '', $value);
            $value = preg_replace('/' . $attr . '\s*=\s*[^\s>]*/i', '', $value);
        }

        return $value;
    }
}
