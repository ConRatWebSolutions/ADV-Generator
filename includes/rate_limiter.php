<?php
/**
 * Rate Limiter for DSGVO ADV Project
 * Implements rate limiting to prevent spam and abuse
 */

require_once __DIR__ . '/../config/database.php';

class RateLimiter {
    private $db;
    private $maxRequests;
    private $timeWindow;
    private $blockDuration;
    
    public function __construct($maxRequests = 5, $timeWindow = 3600, $blockDuration = 3600) {
        $this->db = DatabaseConfig::getConnection();
        $this->maxRequests = $maxRequests;
        $this->timeWindow = $timeWindow;
        $this->blockDuration = $blockDuration;
    }
    
    /**
     * Check if request is allowed based on rate limiting
     * @param string $identifier IP address or user identifier
     * @return array Result with allowed status and remaining requests
     */
    public function checkRateLimit($identifier) {
        try {
            // Check if IP is currently blocked
            if ($this->isBlocked($identifier)) {
                return [
                    'allowed' => false,
                    'reason' => 'blocked',
                    'message' => 'Ihre IP-Adresse ist temporär gesperrt. Bitte versuchen Sie es später erneut.',
                    'retry_after' => $this->getBlockTimeRemaining($identifier)
                ];
            }
            
            // Get current request count
            $requestCount = $this->getRequestCount($identifier);
            
            if ($requestCount >= $this->maxRequests) {
                // Block the IP for the specified duration
                $this->blockIP($identifier);
                
                return [
                    'allowed' => false,
                    'reason' => 'rate_limit_exceeded',
                    'message' => 'Zu viele Anfragen. Bitte warten Sie ' . ($this->blockDuration / 60) . ' Minuten.',
                    'retry_after' => $this->blockDuration
                ];
            }
            
            // Record this request
            $this->recordRequest($identifier);
            
            return [
                'allowed' => true,
                'remaining' => $this->maxRequests - $requestCount - 1,
                'reset_time' => time() + $this->timeWindow
            ];
            
        } catch (Exception $e) {
            // Log error but don't block legitimate users
            DatabaseOperations::logOperation('error', 'Rate limiter error: ' . $e->getMessage(), [
                'identifier' => $identifier
            ]);
            
            return [
                'allowed' => true,
                'error' => 'Rate limiter temporarily unavailable'
            ];
        }
    }
    
    /**
     * Check if IP is currently blocked
     * @param string $identifier
     * @return bool
     */
    private function isBlocked($identifier) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM rate_limits 
                WHERE identifier = ? 
                AND block_until > NOW() 
                AND type = 'block'
            ");
            $stmt->execute([$identifier]);
            $result = $stmt->fetch();
            
            return $result['count'] > 0;
            
        } catch (Exception $e) {
            DatabaseOperations::logOperation('error', 'Failed to check block status: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get remaining block time
     * @param string $identifier
     * @return int
     */
    private function getBlockTimeRemaining($identifier) {
        try {
            $stmt = $this->db->prepare("
                SELECT block_until 
                FROM rate_limits 
                WHERE identifier = ? 
                AND block_until > NOW() 
                AND type = 'block'
                ORDER BY created_at DESC 
                LIMIT 1
            ");
            $stmt->execute([$identifier]);
            $result = $stmt->fetch();
            
            if ($result) {
                $blockUntil = strtotime($result['block_until']);
                return max(0, $blockUntil - time());
            }
            
            return 0;
            
        } catch (Exception $e) {
            DatabaseOperations::logOperation('error', 'Failed to get block time: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get current request count within time window
     * @param string $identifier
     * @return int
     */
    private function getRequestCount($identifier) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM rate_limits 
                WHERE identifier = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
                AND type = 'request'
            ");
            $stmt->execute([$identifier, $this->timeWindow]);
            $result = $stmt->fetch();
            
            return (int)$result['count'];
            
        } catch (Exception $e) {
            DatabaseOperations::logOperation('error', 'Failed to get request count: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Record a request
     * @param string $identifier
     */
    private function recordRequest($identifier) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO rate_limits (identifier, type, created_at) 
                VALUES (?, 'request', NOW())
            ");
            $stmt->execute([$identifier]);
            
        } catch (Exception $e) {
            DatabaseOperations::logOperation('error', 'Failed to record request: ' . $e->getMessage());
        }
    }
    
    /**
     * Block an IP address
     * @param string $identifier
     */
    private function blockIP($identifier) {
        try {
            $blockUntil = date('Y-m-d H:i:s', time() + $this->blockDuration);
            
            $stmt = $this->db->prepare("
                INSERT INTO rate_limits (identifier, type, block_until, created_at) 
                VALUES (?, 'block', ?, NOW())
            ");
            $stmt->execute([$identifier, $blockUntil]);
            
            DatabaseOperations::logOperation('warning', 'IP blocked due to rate limiting', [
                'identifier' => $identifier,
                'block_until' => $blockUntil
            ]);
            
        } catch (Exception $e) {
            DatabaseOperations::logOperation('error', 'Failed to block IP: ' . $e->getMessage());
        }
    }
    
    /**
     * Clean up old rate limit records
     */
    public function cleanup() {
        try {
            // Remove old request records (older than time window)
            $stmt = $this->db->prepare("
                DELETE FROM rate_limits 
                WHERE type = 'request' 
                AND created_at < DATE_SUB(NOW(), INTERVAL ? SECOND)
            ");
            $stmt->execute([$this->timeWindow * 2]);
            
            // Remove expired blocks
            $stmt = $this->db->prepare("
                DELETE FROM rate_limits 
                WHERE type = 'block' 
                AND block_until < NOW()
            ");
            $stmt->execute();
            
            DatabaseOperations::logOperation('info', 'Rate limiter cleanup completed');
            
        } catch (Exception $e) {
            DatabaseOperations::logOperation('error', 'Rate limiter cleanup failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Get rate limit statistics
     * @return array
     */
    public function getStats() {
        try {
            $stats = [];
            
            // Total requests in last hour
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM rate_limits 
                WHERE type = 'request' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute();
            $stats['requests_last_hour'] = $stmt->fetch()['count'];
            
            // Currently blocked IPs
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM rate_limits 
                WHERE type = 'block' 
                AND block_until > NOW()
            ");
            $stmt->execute();
            $stats['blocked_ips'] = $stmt->fetch()['count'];
            
            // Top requesters
            $stmt = $this->db->prepare("
                SELECT identifier, COUNT(*) as count 
                FROM rate_limits 
                WHERE type = 'request' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
                GROUP BY identifier 
                ORDER BY count DESC 
                LIMIT 10
            ");
            $stmt->execute();
            $stats['top_requesters'] = $stmt->fetchAll();
            
            return $stats;
            
        } catch (Exception $e) {
            DatabaseOperations::logOperation('error', 'Failed to get rate limit stats: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Manually unblock an IP
     * @param string $identifier
     * @return bool
     */
    public function unblockIP($identifier) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM rate_limits 
                WHERE identifier = ? 
                AND type = 'block'
            ");
            $stmt->execute([$identifier]);
            
            DatabaseOperations::logOperation('info', 'IP manually unblocked', [
                'identifier' => $identifier
            ]);
            
            return true;
            
        } catch (Exception $e) {
            DatabaseOperations::logOperation('error', 'Failed to unblock IP: ' . $e->getMessage());
            return false;
        }
    }
}
