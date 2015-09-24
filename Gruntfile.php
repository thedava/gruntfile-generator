<?php

/**
 * This class generates a Gruntfile.js from PHP configs
 *
 * @author  thedava
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
    protected $oldGruntfileContent = array();

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
        $this->gruntfilePath       = $gruntfilePath;
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
        return (file_exists($this->gruntfilePath)) ? file($this->gruntfilePath) : array();
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

        $diff     = new Text_Diff('auto', array($this->oldGruntfileContent, $currentGruntfileContent));
        $renderer = new Text_Diff_Renderer_unified();

        $content = array();
        foreach (explode("\n", $renderer->render($diff)) as $line) {
            $line = rtrim($line);

            if (strlen($line) > 0) {
                if ($line[0] == '+') {
                    $line = $this->colorGreen($line);
                }
                elseif ($line[0] == '-') {
                    $line = $this->colorRed($line);
                }
            }

            $content[] = $line;
        }

        return implode(PHP_EOL, $content).PHP_EOL;
    }

    /**
     * Returns the given config file as validated array
     *
     * @param string $configFilePath
     *
     * @return array
     * @throws Exception
     */
    protected function parseConfigs($configFilePath) {
        $config = include $configFilePath;
        if (!is_array($config)) {
            throw new Exception('The config file must return an array!', E_USER_ERROR);
        }

        $imports = array();
        if (isset($config[static::CONFIG_IMPORTS]) && is_array($config[static::CONFIG_IMPORTS])) {
            $imports = $config[static::CONFIG_IMPORTS];
        }

        $tasks = array();
        if (isset($config[static::CONFIG_TASKS]) && is_array($config[static::CONFIG_TASKS])) {
            $tasks = $config[static::CONFIG_TASKS];
        }

        $targets = array();
        if (isset($config[static::CONFIG_TARGETS]) && is_array($config[static::CONFIG_TARGETS])) {
            $targets = $config[static::CONFIG_TARGETS];
        }

        return array(
            static::CONFIG_IMPORTS => $imports,
            static::CONFIG_TASKS   => $tasks,
            static::CONFIG_TARGETS => $targets,
        );
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
        $mustache = new Mustache_Engine(
            array(
                'escape' => function ($str) {
                    // No escaping
                    return $str;
                }
            )
        );

        $tasks = array();
        foreach ($taskConfig as $taskName => $taskCommands) {
            $tasks[] = array(
                'task_name'     => $taskName,
                'task_commands' => json_encode($taskCommands),
            );
        }

        if (defined('JSON_PRETTY_PRINT')) {
            $options = JSON_PRETTY_PRINT;
        }

        $vars = array(
            'imports'       => $imports,
            'target_config' => json_encode($targetConfig, $options),
            'tasks'         => $tasks,
        );

        return $mustache->render($template, $vars);
    }

    /**
     * Generates a Gruntfile from the given config
     *
     * @param string $configFilePath
     *
     * @return bool
     * @throws Exception
     */
    public function generate($configFilePath) {
        $config = $this->parseConfigs($configFilePath);

        $gruntfile = $this->getGeneratedGruntfile(
            $config[static::CONFIG_TARGETS],
            $config[static::CONFIG_IMPORTS],
            $config[static::CONFIG_TASKS]
        );

        return file_put_contents($this->gruntfilePath, $gruntfile) !== false;
    }

    /**
     * Merges the given files or configs into a single config
     *
     * @param array $configFiles Either a list of file paths or a list of configs (as array)
     *
     * @return array
     * @throws Exception
     */
    public static function mergeConfigs(array $configFiles) {
        $gruntConfig = array(
            static::CONFIG_TASKS   => array(),
            static::CONFIG_TARGETS => array(),
            static::CONFIG_IMPORTS => array(),
        );
        foreach ($configFiles as $file) {
            if (is_array($file)) {
                $config = $file;
            }
            else {
                $config = include $file;
                if (!is_array($config)) {
                    throw new Exception('The given config file did not return an array! File: '.$file, E_USER_ERROR);
                }
            }

            $gruntConfig = array_merge_recursive($gruntConfig, $config);
        }

        return $gruntConfig;
    }
}
