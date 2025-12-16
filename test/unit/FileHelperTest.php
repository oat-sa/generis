<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 31 Milk St # 960789 Boston, MA 02196 USA.
 *
 * Copyright (c) 2008-2025 (original work) Open Assessment Technologies SA;
 */

use PHPUnit\Framework\TestCase;

class FileHelperTest extends TestCase
{
    protected function setUp(): void
    {
    }

    public function testRemoveFile()
    {
        $basedir = $this->mkdir(sys_get_temp_dir());
        $this->assertTrue(is_dir($basedir));
        $file01 = tempnam($basedir, 'testdir');
        $file02 = tempnam($basedir, 'testdir');

        $subDir1 = $this->mkdir($basedir);

        $subDir2 = $this->mkdir($basedir);
        $file21 = tempnam($subDir2, 'testdir');
        $subDir21 = $this->mkdir($subDir2);
        $file211 = tempnam($subDir21, 'testdir');
        $subDir22 = $this->mkdir($subDir2);
        $this->assertTrue(helpers_File::remove($basedir));
        $this->assertFalse(is_dir($basedir));
    }

    private function mkdir($basePath)
    {
        $file = tempnam($basePath, 'dir');
        $this->assertTrue(unlink($file));
        $this->assertTrue(mkdir($file));
        return $file;
    }


    /**
     * @todo fix problematic test case
     * why does this case try to read files 'ExpressionFactoryTest.php', 'ExpressionTest.php',
     * 'OperationFactoryTest.php', 'OperationTest.php', 'TermFactoryTest.php', 'TermTest.php'?
     *
     *
     * @dataProvider scandirDataProvider
     *
     * @param string $toScan Directory path to be scanned
     * @param array $expectedResult The expected return value of helpers_File::scanDir().
     * @param boolean $recursive Value of the 'recursive' option.
     * @param boolean $absolute Value of the 'absolute' option.
     */
    public function testScandir($toScan, $expectedResult, $recursive = false, $absolute = false)
    {
        $result = helpers_File::scanDir(
            $toScan,
            ['recursive' => $recursive, 'absolute' => $absolute, 'only' => helpers_File::SCAN_FILE]
        );
        $this->assertEquals(count($expectedResult), count($result));
        // The order might vary depending on the file system implementation...
        foreach ($expectedResult as $expected) {
            $this->assertTrue(in_array($expected, $result));
        }
    }

    public function scandirDataProvider()
    {
        $ds = DIRECTORY_SEPARATOR;

        return [
            [dirname(__FILE__) . $ds . '..' . $ds . 'samples' . $ds . 'scandir', ['1', '2']],
        ];
    }

    /**
     * @dataProvider containsFileTypeProvider
     *
     * @param string $toScan The directory to be scanned.
     * @param string|array $types The types to check for e.g. 'php', 'js', ...
     * @param boolean $recursive Whether or not to check recursively in the directory.
     * @param boolean $expectedResult The expected result of the containsFileType helper method.
     */
    public function testContainsFileType($toScan, $types, $recursive, $expectedResult)
    {
        $this->assertSame($expectedResult, helpers_File::containsFileType($toScan, $types, $recursive));
    }

    public function containsFileTypeProvider()
    {
        return [
            [dirname(__FILE__), 'php', true, true],
            [dirname(__FILE__), 'php', false, true],
            [dirname(__FILE__), 'js', true, false],
            [dirname(__FILE__), 'js', false, false],
            [dirname(__FILE__) . '/..', 'rdf', false, false],
            [dirname(__FILE__) . '/..', 'rdf', true, true],

            // edge cases.
            // - unexisting directory.
            [dirname(__FILE__) . '/foo', 'php', false, false],
            [dirname(__FILE__) . '/foo', 'php', true, false],
            // - scan a file.
            [dirname(__FILE__) . '/../index.php', 'php', false, false],
            [dirname(__FILE__) . '/../index.php', 'php', true, false],
        ];
    }

    public function testUrlToPath()
    {
        $path = DIRECTORY_SEPARATOR . 'style' . DIRECTORY_SEPARATOR . 'custom' . DIRECTORY_SEPARATOR
            . 'tao-user-styles.css';

        $urls = [
            '/style/custom/tao-user-styles.css',
            'http://ex.com/style/custom/tao-user-styles.css',
            'https://ex.com/style/custom/tao-user-styles.css',
            'file://c/style/custom/tao-user-styles.css',
            'file://D:/style/custom/tao-user-styles.css',
        ];

        foreach ($urls as $url) {
            $this->assertEquals(helpers_File::urlToPath($url), $path);
        }
    }

    /**
     * Test isPathInsideDirectory prevents path traversal attacks
     */
    public function testIsPathInsideDirectoryValidPaths()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $subDir = $baseDir . DIRECTORY_SEPARATOR . 'subdir';
        mkdir($subDir, 0755, true);

        $file = $subDir . DIRECTORY_SEPARATOR . 'test.txt';
        file_put_contents($file, 'test content');

        try {
            // Valid paths inside directory
            $this->assertTrue(
                helpers_File::isPathInsideDirectory($subDir, $baseDir),
                'Subdirectory should be inside base directory'
            );

            $this->assertTrue(
                helpers_File::isPathInsideDirectory($file, $baseDir),
                'File in subdirectory should be inside base directory'
            );

            $this->assertTrue(
                helpers_File::isPathInsideDirectory($baseDir, $baseDir),
                'Directory should be inside itself'
            );
        } finally {
            helpers_File::remove($baseDir);
        }
    }

    /**
     * Test isPathInsideDirectory prevents path traversal attacks
     */
    public function testIsPathInsideDirectoryPathTraversalAttacks()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $subDir = $baseDir . DIRECTORY_SEPARATOR . 'subdir';
        mkdir($subDir, 0755, true);

        try {
            // Path traversal attempts using ../
            $traversalPath = $subDir . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'
                . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'passwd';

            $this->assertFalse(
                helpers_File::isPathInsideDirectory($traversalPath, $baseDir),
                'Path traversal with ../ should be rejected'
            );

            // Path completely outside
            $outsidePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'outside_' . uniqid();
            mkdir($outsidePath, 0755, true);

            $this->assertFalse(
                helpers_File::isPathInsideDirectory($outsidePath, $baseDir),
                'Path outside base directory should be rejected'
            );

            helpers_File::remove($outsidePath);

            // Parent directory should not be inside child
            $this->assertFalse(
                helpers_File::isPathInsideDirectory($baseDir, $subDir),
                'Parent directory should not be inside child directory'
            );
        } finally {
            helpers_File::remove($baseDir);
        }
    }

    /**
     * Test isPathInsideDirectory with non-existent paths
     */
    public function testIsPathInsideDirectoryNonExistentPaths()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        try {
            // Non-existent file
            $nonExistent = $baseDir . DIRECTORY_SEPARATOR . 'nonexistent.txt';
            $this->assertFalse(
                helpers_File::isPathInsideDirectory($nonExistent, $baseDir),
                'Non-existent path should return false'
            );

            // Non-existent directory
            $nonExistentDir = '/tmp/nonexistent_dir_' . uniqid();
            $this->assertFalse(
                helpers_File::isPathInsideDirectory($baseDir, $nonExistentDir),
                'Non-existent base directory should return false'
            );
        } finally {
            helpers_File::remove($baseDir);
        }
    }

    /**
     * Test isPathInsideDirectory with symlinks
     */
    public function testIsPathInsideDirectoryWithSymlinks()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->markTestSkipped('Symlink test skipped on Windows');
        }

        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $targetDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_target_' . uniqid();
        mkdir($targetDir, 0755, true);

        $symlinkPath = $baseDir . DIRECTORY_SEPARATOR . 'symlink';

        try {
            // Create symlink pointing outside base directory
            symlink($targetDir, $symlinkPath);

            // Symlink should resolve to target, which is outside baseDir
            $this->assertFalse(
                helpers_File::isPathInsideDirectory($symlinkPath, $baseDir),
                'Symlink pointing outside should be rejected'
            );
        } finally {
            if (is_link($symlinkPath)) {
                unlink($symlinkPath);
            }
            helpers_File::remove($baseDir);
            helpers_File::remove($targetDir);
        }
    }

    /**
     * Test copySafe with local file copy
     */
    public function testCopySafeLocalFile()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_copy_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $sourceFile = $baseDir . DIRECTORY_SEPARATOR . 'source.txt';
        $destFile = $baseDir . DIRECTORY_SEPARATOR . 'dest.txt';
        $content = 'Test content for copy ' . uniqid();

        file_put_contents($sourceFile, $content);

        try {
            $result = helpers_File::copySafe($sourceFile, $destFile);

            $this->assertTrue($result, 'copySafe should return true for successful copy');
            $this->assertFileExists($destFile, 'Destination file should exist');
            $this->assertEquals(
                $content,
                file_get_contents($destFile),
                'Destination file should have same content as source'
            );
        } finally {
            helpers_File::remove($baseDir);
        }
    }

    /**
     * Test copySafe with local directory copy
     */
    public function testCopySafeLocalDirectory()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_copy_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $sourceDir = $baseDir . DIRECTORY_SEPARATOR . 'source_dir';
        $destDir = $baseDir . DIRECTORY_SEPARATOR . 'dest_dir';
        mkdir($sourceDir, 0755, true);

        $sourceFile = $sourceDir . DIRECTORY_SEPARATOR . 'file.txt';
        $content = 'Directory copy test ' . uniqid();
        file_put_contents($sourceFile, $content);

        try {
            $result = helpers_File::copySafe($sourceDir, $destDir);

            $this->assertTrue($result, 'copySafe should return true for successful directory copy');
            $this->assertDirectoryExists($destDir, 'Destination directory should exist');
            $this->assertFileExists(
                $destDir . DIRECTORY_SEPARATOR . 'file.txt',
                'File in destination directory should exist'
            );
            $this->assertEquals(
                $content,
                file_get_contents($destDir . DIRECTORY_SEPARATOR . 'file.txt'),
                'Copied file should have same content'
            );
        } finally {
            helpers_File::remove($baseDir);
        }
    }

    /**
     * Test copySafe creates destination directory if needed
     */
    public function testCopySafeCreatesDestinationDirectory()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_copy_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $sourceFile = $baseDir . DIRECTORY_SEPARATOR . 'source.txt';
        $destFile = $baseDir . DIRECTORY_SEPARATOR . 'nested' . DIRECTORY_SEPARATOR . 'path'
            . DIRECTORY_SEPARATOR . 'dest.txt';
        $content = 'Test nested directory creation ' . uniqid();

        file_put_contents($sourceFile, $content);

        try {
            $result = helpers_File::copySafe($sourceFile, $destFile);

            $this->assertTrue($result, 'copySafe should return true');
            $this->assertFileExists($destFile, 'Destination file should exist in nested directory');
            $this->assertEquals(
                $content,
                file_get_contents($destFile),
                'Copied file should have same content'
            );
        } finally {
            helpers_File::remove($baseDir);
        }
    }

    /**
     * Test copySafe with empty file
     */
    public function testCopySafeEmptyFile()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_copy_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $sourceFile = $baseDir . DIRECTORY_SEPARATOR . 'empty.txt';
        $destFile = $baseDir . DIRECTORY_SEPARATOR . 'empty_dest.txt';

        touch($sourceFile);

        try {
            $result = helpers_File::copySafe($sourceFile, $destFile);

            $this->assertTrue($result, 'copySafe should return true for empty file');
            $this->assertFileExists($destFile, 'Destination file should exist');
            $this->assertEquals(0, filesize($destFile), 'Destination file should be empty');
        } finally {
            helpers_File::remove($baseDir);
        }
    }

    /**
     * Test copySafe with non-existent source
     */
    public function testCopySafeNonExistentSource()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_copy_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $sourceFile = $baseDir . DIRECTORY_SEPARATOR . 'nonexistent.txt';
        $destFile = $baseDir . DIRECTORY_SEPARATOR . 'dest.txt';

        try {
            $result = helpers_File::copySafe($sourceFile, $destFile);

            $this->assertFalse($result, 'copySafe should return false for non-existent source');
            $this->assertFileDoesNotExist($destFile, 'Destination file should not be created');
        } finally {
            helpers_File::remove($baseDir);
        }
    }

    /**
     * Test copySafe with large file
     */
    public function testCopySafeLargeFile()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_copy_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $sourceFile = $baseDir . DIRECTORY_SEPARATOR . 'large.txt';
        $destFile = $baseDir . DIRECTORY_SEPARATOR . 'large_dest.txt';

        // Create a 1MB file
        $content = str_repeat('A', 1024 * 1024);
        file_put_contents($sourceFile, $content);

        try {
            $result = helpers_File::copySafe($sourceFile, $destFile);

            $this->assertTrue($result, 'copySafe should handle large files');
            $this->assertFileExists($destFile, 'Destination file should exist');
            $this->assertEquals(
                filesize($sourceFile),
                filesize($destFile),
                'Destination file should have same size as source'
            );
        } finally {
            helpers_File::remove($baseDir);
        }
    }

    /**
     * Test copySafe with php:// stream wrapper (memory stream)
     */
    public function testCopySafeWithStreamWrapper()
    {
        $baseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tao_copy_test_' . uniqid();
        mkdir($baseDir, 0755, true);

        $content = 'Stream wrapper test ' . uniqid();
        $destFile = $baseDir . DIRECTORY_SEPARATOR . 'from_stream.txt';

        // Create memory stream
        $sourceStream = 'php://memory';
        $handle = fopen($sourceStream, 'r+');
        fwrite($handle, $content);
        rewind($handle);
        fclose($handle);

        // Note: php://memory doesn't persist, so we'll use a temp file instead for this test
        $sourceFile = 'php://temp';
        $tempHandle = fopen($sourceFile, 'r+');
        fwrite($tempHandle, $content);
        rewind($tempHandle);
        fclose($tempHandle);

        try {
            // For this test, we'll verify the stream path detection works
            // by testing with a data:// URI which should trigger stream copy
            $dataUri = 'data://text/plain;base64,' . base64_encode($content);
            $result = helpers_File::copySafe($dataUri, $destFile);

            $this->assertTrue($result, 'copySafe should handle stream wrappers');
            $this->assertFileExists($destFile, 'Destination file should exist');
            $this->assertEquals(
                $content,
                file_get_contents($destFile),
                'Content should be copied correctly from stream'
            );
        } finally {
            helpers_File::remove($baseDir);
        }
    }
}
