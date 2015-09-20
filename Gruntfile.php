<?php

/**
 * This class generates a Gruntfile.js from PHP configs
 *
 * @author thedava
 * @package gruntfile-generator
 */
class Gruntfile {
    /**
     * All tasks will be registered with grunt.registerTask
     *
     * @var string
     */
    const CONFIG_TASKS = 'tasks';

    /*
     * All targets will be added to grunt.initConfig
     *
     * @var string
     */
    const CONFIG_TARGETS = 'targets';

    /**
     * All imports will be registered with grunt.loadNpmTasks
     *
     * @var string
     */
    const CONFIG_IMPORTS = 'imports';

    /**
     * The location of the current Gruntfile.js
     *
     * @var string
     */
    protected $gruntfilePath;

    /**
     * The content of the gruntfile before the generator has started
     *
     * @var array
     */
    protected $oldGruntfileContent = [];

    /**
     * @var Console_Color2
     */
    protected $consoleColor = null;

    /**
     * Initializes the Gruntfile generator class
     *
     * @param string $gruntfilePath The path to your Gruntfile.js (Filename included)
     * @param bool   $showColor
     */
    public function __construct($gruntfilePath, $showColor = true) {
    	$this->gruntfilePath = $gruntfilePath;
        $this->oldGruntfileContent = $this->loadGruntfile();

        if ($showColor) {
            $this->consoleColor = new Console_Color2();
        }
    }

    /**
     * Loads the given gruntfile
     *
     * @return array
     */
    protected function loadGruntfile() {
        return (file_exists($this->gruntfilePath)) ? file($this->gruntfilePath) : [];
    }

    /**
     * Colors the given string with the given color (if coloring is available)
     *
     * @param string $colorCode
     * @param string $message
     *
     * @return string
     */
    protected function color($colorCode, $message) {
        if ($this->consoleColor !== null) {
            $message = $this->consoleColor->convert($colorCode.$message.'%n');
        }

        return $message;
    }

    /**
     * Colors the given message green if coloring is enabled
     *
     * @param string $message
     *
     * @return string
     */
    public function colorGreen($message) {
        return $this->color('%g', $message);
    }

    /**
     * Colors the given message red if coloring is enabled
     *
     * @param string $message
     *
     * @return string
     */
    public function colorRed($message) {
        return $this->color('%r', $message);
    }

    /**
     * Returns the diff from the old Gruntfile and the new one
     *
     * @return string
     */
    public function showDiff() {
        $currentGruntfileContent = $this->loadGruntfile();

        $diff     = new Text_Diff('auto', [$this->oldGruntfileContent, $currentGruntfileContent]);
        $renderer = new Text_Diff_Renderer_unified();

        $content = [];
        foreach (explode("\n", $renderer->render($diff)) as $line) {
            $line = rtrim($line);

            if (strlen($line) > 0) {
                if ($line[0] == '+') {
                    $line = $this->colorGreen($line);
                } elseif($line[0] == '-') {
                    $line = $this->colorRed($line);
                }
            }

            $content[] = $line;
        }

        return implode(PHP_EOL, $content).PHP_EOL;
    }

    /**
     * This method replaces the default mustache escaping
     *
     * @param string $str
     *
     * @return string
     */
    private function escapeMustache($str) {
        return $str;
    }

    protected function getImports(){

    }

    protected function getTargetConfig() {

    }

    protected function getTasks() {

    }

    /**
     * @param array $targetConfig
     * @param array $imports
     * @param array $taskConfig
     *
     * @return string
     */
    protected function getGeneratedGruntfile(array $targetConfig, array $imports, array $taskConfig) {
        $template = file_get_contents(__DIR__.'/assets/Gruntfile.js.mustache');
        $mustache = new Mustache_Engine(array('escape' => [$this, 'escapeMustache']));
        $vars = array(
            'imports' => $imports,
            'target_config' => $targetConfig,
            'tasks' => $taskConfig,
        );

        return $mustache->render($template, $vars);
    }
}
