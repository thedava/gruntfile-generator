<?php

class GruntfileTest extends PHPUnit_Framework_TestCase {
    /** @var string */
    protected $tempFile;

    public function setUp() {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'gruntgen_');
    }

    /**
     * Delete temp file if still exists
     */
    public function tearDown() {
        if (file_exists($this->tempFile)) {
            unlink($this->tempFile);
        }
    }

    /**
     * @param bool $hasFile
     * @param bool $hasColors
     *
     * @return Gruntfile
     */
    protected function getGruntfile($hasFile, $hasColors = true) {
        return new Gruntfile(($hasFile) ? $this->tempFile : 'Gruntfile.nope', $hasColors);
    }

    /**
     * @param string    $methodName
     * @param Gruntfile $gruntfile
     *
     * @return ReflectionMethod
     */
    protected function getMethod($methodName, Gruntfile $gruntfile) {
        $refObj    = new ReflectionObject($gruntfile);
        $refMethod = $refObj->getMethod($methodName);
        $refMethod->setAccessible(true);

        return $refMethod;
    }

    public function testLoadGruntfile_FileExists() {
        file_put_contents($this->tempFile, 'Hello World');
        $gruntfile = $this->getGruntfile(true);
        $result    = $this->getMethod('loadGruntfile', $gruntfile)->invoke($gruntfile);

        $this->assertInternalType('array', $result);
        $this->assertCount(1, $result);
        $this->assertEquals('Hello World', $result[0]);
    }

    public function testLoadGruntfile_FileNotExists() {
        $gruntfile = $this->getGruntfile(false);
        $result    = $this->getMethod('loadGruntfile', $gruntfile)->invoke($gruntfile);

        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    public function colorDataProvider() {
        return array(
            array(
                'color',
                array('%y', 'Hello World'),
                true,
                'Hello World'
            ),
            array(
                'color',
                array('%y', 'Hello World'),
                false,
                'Hello World'
            ),
            array(
                'colorGreen',
                array('Hello World'),
                true,
                'Hello World'
            ),
            array(
                'colorGreen',
                array('Hello World'),
                false,
                'Hello World'
            ),
            array(
                'colorRed',
                array('Hello World'),
                true,
                'Hello World'
            ),
            array(
                'colorRed',
                array('Hello World'),
                false,
                'Hello World'
            ),
        );
    }

    /**
     * @dataProvider colorDataProvider
     *
     * @param string $methodName
     * @param array  $invokeArguments
     * @param bool   $colorsEnabled
     * @param string $expectedResult
     */
    public function testColor($methodName, array $invokeArguments, $colorsEnabled, $expectedResult) {
        $gruntfile = $this->getGruntfile(true, $colorsEnabled);
        $result    = $this->getMethod($methodName, $gruntfile)->invokeArgs($gruntfile, $invokeArguments);

        $this->assertInternalType('string', $result);
        if ($colorsEnabled) {
            $this->assertNotEquals($expectedResult, $result);
            $this->assertContains($expectedResult, $result);
        }
        else {
            $this->assertEquals($expectedResult, $result);
        }
    }
}