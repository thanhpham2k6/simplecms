<?php
class Plugin {
    private $active_plugins = [];
    
    public function __construct() {
        $this->loadActivePlugins();
    }
    
    private function loadActivePlugins() {
        $plugin_file = ROOT_PATH . '/plugins/active_plugins.json';
        if(file_exists($plugin_file)) {
            $this->active_plugins = json_decode(file_get_contents($plugin_file), true) ?: [];
        }
    }
    
    public function activate($plugin_name) {
        if(!in_array($plugin_name, $this->active_plugins)) {
            $this->active_plugins[] = $plugin_name;
            $this->saveActivePlugins();
            
            // Chạy activation hook nếu có
            $plugin_file = PLUGINS_PATH . "/$plugin_name/plugin.php";
            if(file_exists($plugin_file)) {
                include_once $plugin_file;
                if(function_exists($plugin_name . '_activate')) {
                    call_user_func($plugin_name . '_activate');
                }
            }
        }
    }
    
    public function deactivate($plugin_name) {
        $key = array_search($plugin_name, $this->active_plugins);
        if($key !== false) {
            unset($this->active_plugins[$key]);
            $this->saveActivePlugins();
            
            // Chạy deactivation hook nếu có
            $plugin_file = PLUGINS_PATH . "/$plugin_name/plugin.php";
            if(file_exists($plugin_file)) {
                include_once $plugin_file;
                if(function_exists($plugin_name . '_deactivate')) {
                    call_user_func($plugin_name . '_deactivate');
                }
            }
        }
    }
    
    private function saveActivePlugins() {
        $plugin_file = ROOT_PATH . '/plugins/active_plugins.json';
        file_put_contents($plugin_file, json_encode($this->active_plugins));
    }
    
    public function getAll() {
        $plugins = [];
        $dirs = glob(PLUGINS_PATH . '/*', GLOB_ONLYDIR);
        
        foreach($dirs as $dir) {
            $plugin_file = $dir . '/plugin.php';
            if(file_exists($plugin_file)) {
                $plugin_name = basename($dir);
                $plugins[] = [
                    'name' => $plugin_name,
                    'active' => in_array($plugin_name, $this->active_plugins),
                    'path' => $plugin_file
                ];
            }
        }
        
        return $plugins;
    }
    
    public function loadPlugins() {
        foreach($this->active_plugins as $plugin_name) {
            $plugin_file = PLUGINS_PATH . "/$plugin_name/plugin.php";
            if(file_exists($plugin_file)) {
                include_once $plugin_file;
            }
        }
    }
}
