<?php
/**
 * Unit Tests for DatabaseUtils Class
 * 
 * Tests the insert() method functionality including:
 * - Valid data insertion
 * - Error handling for invalid inputs
 * - Parameter binding and SQL injection prevention
 */

require_once __DIR__ . '/DatabaseUtils.php';

class DatabaseUtilsTest {
    private $dbUtils;
    private $testTableName = 'test_insert_table';
    
    public function __construct() {
        $this->dbUtils = new DatabaseUtils();
    }
    
    /**
     * Setup test environment
     */
    public function setUp(): void {
        echo "Setting up test environment...\n";
        
        // Create a test table
        $conn = $this->dbUtils->getConnection();
        $createTableSQL = "
            CREATE TABLE IF NOT EXISTS {$this->testTableName} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                age INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $conn->exec($createTableSQL);
        
        // Clean the table
        $conn->exec("TRUNCATE TABLE {$this->testTableName}");
        
        echo "Test table created and cleaned.\n";
    }
    
    /**
     * Teardown test environment
     */
    public function tearDown(): void {
        echo "Cleaning up test environment...\n";
        
        // Drop test table
        $conn = $this->dbUtils->getConnection();
        $conn->exec("DROP TABLE IF EXISTS {$this->testTableName}");
        
        echo "Test table dropped.\n";
    }
    
    /**
     * Test: Insert with valid data
     */
    public function testInsertValidData(): void {
        echo "\n--- Test: Insert Valid Data ---\n";
        
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'age' => 30
        ];
        
        $result = $this->dbUtils->insert($this->testTableName, $data);
        
        $this->assertTrue($result['success'], "Insert should succeed");
        $this->assertNotNull($result['id'], "Should return generated ID");
        $this->assertEquals(1, $result['affected'], "Should affect 1 row");
        
        echo "✓ Valid data inserted successfully\n";
        echo "  Generated ID: {$result['id']}\n";
    }
    
    /**
     * Test: Insert with empty table name
     */
    public function testInsertEmptyTableName(): void {
        echo "\n--- Test: Insert with Empty Table Name ---\n";
        
        $data = ['name' => 'Test', 'email' => 'test@example.com'];
        $result = $this->dbUtils->insert('', $data);
        
        $this->assertFalse($result['success'], "Insert should fail");
        $this->assertStringContainsString('Table name cannot be empty', $result['error']);
        
        echo "✓ Empty table name rejected correctly\n";
    }
    
    /**
     * Test: Insert with empty data array
     */
    public function testInsertEmptyData(): void {
        echo "\n--- Test: Insert with Empty Data ---\n";
        
        $result = $this->dbUtils->insert($this->testTableName, []);
        
        $this->assertFalse($result['success'], "Insert should fail");
        $this->assertStringContainsString('Data array cannot be empty', $result['error']);
        
        echo "✓ Empty data array rejected correctly\n";
    }
    
    /**
     * Test: Insert with duplicate unique key
     */
    public function testInsertDuplicateUniqueKey(): void {
        echo "\n--- Test: Insert with Duplicate Unique Key ---\n";
        
        // First insert
        $data1 = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'age' => 25
        ];
        $result1 = $this->dbUtils->insert($this->testTableName, $data1);
        $this->assertTrue($result1['success'], "First insert should succeed");
        
        // Second insert with same email
        $data2 = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'age' => 28
        ];
        $result2 = $this->dbUtils->insert($this->testTableName, $data2);
        
        $this->assertFalse($result2['success'], "Duplicate insert should fail");
        $this->assertStringContainsString('Duplicate', $result2['error']);
        
        echo "✓ Duplicate unique key rejected correctly\n";
    }
    
    /**
     * Test: Insert with missing required field
     */
    public function testInsertMissingRequiredField(): void {
        echo "\n--- Test: Insert with Missing Required Field ---\n";
        
        $data = [
            'name' => 'Bob Smith'
            // Missing required 'email' field
        ];
        
        $result = $this->dbUtils->insert($this->testTableName, $data);
        
        $this->assertFalse($result['success'], "Insert should fail");
        
        echo "✓ Missing required field rejected correctly\n";
    }
    
    /**
     * Test: Insert multiple records sequentially
     */
    public function testInsertMultipleRecords(): void {
        echo "\n--- Test: Insert Multiple Records ---\n";
        
        $records = [
            ['name' => 'Alice', 'email' => 'alice@example.com', 'age' => 22],
            ['name' => 'Bob', 'email' => 'bob@example.com', 'age' => 35],
            ['name' => 'Charlie', 'email' => 'charlie@example.com', 'age' => 28]
        ];
        
        $insertedIds = [];
        foreach ($records as $record) {
            $result = $this->dbUtils->insert($this->testTableName, $record);
            $this->assertTrue($result['success'], "Each insert should succeed");
            $insertedIds[] = $result['id'];
        }
        
        $this->assertEquals(3, count($insertedIds), "Should insert 3 records");
        
        echo "✓ Multiple records inserted successfully\n";
        echo "  Inserted IDs: " . implode(', ', $insertedIds) . "\n";
    }
    
    /**
     * Test: SQL injection prevention
     */
    public function testSQLInjectionPrevention(): void {
        echo "\n--- Test: SQL Injection Prevention ---\n";
        
        $data = [
            'name' => "'; DROP TABLE users; --",
            'email' => 'hacker@example.com',
            'age' => 99
        ];
        
        $result = $this->dbUtils->insert($this->testTableName, $data);
        
        $this->assertTrue($result['success'], "Insert should succeed with escaped data");
        
        // Verify table still exists
        $conn = $this->dbUtils->getConnection();
        $stmt = $conn->query("SHOW TABLES LIKE '{$this->testTableName}'");
        $tableExists = $stmt->rowCount() > 0;
        
        $this->assertTrue($tableExists, "Test table should still exist");
        
        echo "✓ SQL injection attempt safely handled\n";
    }
    
    /**
     * Helper: Assert true
     */
    private function assertTrue($condition, $message = '') {
        if (!$condition) {
            throw new Exception("Assertion failed: $message");
        }
    }
    
    /**
     * Helper: Assert false
     */
    private function assertFalse($condition, $message = '') {
        if ($condition) {
            throw new Exception("Assertion failed: $message");
        }
    }
    
    /**
     * Helper: Assert not null
     */
    private function assertNotNull($value, $message = '') {
        if ($value === null) {
            throw new Exception("Assertion failed: $message");
        }
    }
    
    /**
     * Helper: Assert equals
     */
    private function assertEquals($expected, $actual, $message = '') {
        if ($expected !== $actual) {
            throw new Exception("Assertion failed: $message (Expected: $expected, Actual: $actual)");
        }
    }
    
    /**
     * Helper: Assert string contains
     */
    private function assertStringContainsString($needle, $haystack, $message = '') {
        if (strpos($haystack, $needle) === false) {
            throw new Exception("Assertion failed: $message (String '$needle' not found in '$haystack')");
        }
    }
    
    /**
     * Run all tests
     */
    public function runAllTests(): void {
        $tests = [
            'testInsertValidData',
            'testInsertEmptyTableName',
            'testInsertEmptyData',
            'testInsertDuplicateUniqueKey',
            'testInsertMissingRequiredField',
            'testInsertMultipleRecords',
            'testSQLInjectionPrevention'
        ];
        
        $passed = 0;
        $failed = 0;
        
        echo "\n========================================\n";
        echo "Running DatabaseUtils Insert Tests\n";
        echo "========================================\n";
        
        $this->setUp();
        
        foreach ($tests as $test) {
            try {
                $this->$test();
                $passed++;
            } catch (Exception $e) {
                echo "✗ Test failed: {$test}\n";
                echo "  Error: {$e->getMessage()}\n";
                $failed++;
            }
        }
        
        $this->tearDown();
        
        echo "\n========================================\n";
        echo "Test Results\n";
        echo "========================================\n";
        echo "Passed: $passed\n";
        echo "Failed: $failed\n";
        echo "Total:  " . ($passed + $failed) . "\n";
        echo "========================================\n";
        
        if ($failed > 0) {
            exit(1);
        }
    }
}

// Run tests if executed directly
if (php_sapi_name() === 'cli') {
    $test = new DatabaseUtilsTest();
    $test->runAllTests();
}
?>
