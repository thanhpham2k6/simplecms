<?php
class Theme {
    private $active_theme;
    
    public function __construct() {
        $this->active_theme = $this->getActiveTheme();
    }
    
    public function getActiveTheme() {
        $theme_file = ROOT_PATH . '/themes/active_theme.txt';
        if(file_exists($theme_file)) {
            return trim(file_get_contents($theme_file));
        }
        return 'default';
    }
    
    public function setActiveTheme($theme_name) {
        $theme_file = ROOT_PATH . '/themes/active_theme.txt';
        file_put_contents($theme_file, $theme_name);
        $this->active_theme = $theme_name;
    }
    
    public function getAll() {
        $themes = [];
        $dirs = glob(THEMES_PATH . '/*', GLOB_ONLYDIR);
        
        foreach($dirs as $dir) {
            $theme_name = basename($dir);
            $themes[] = [
                'name' => $theme_name,
                'active' => ($theme_name === $this->active_theme),
                'path' => $dir
            ];
        }
        
        return $themes;
    }
    
    public function getTemplatePath($template) {
        $theme_path = THEMES_PATH . '/' . $this->active_theme . '/' . $template;
        if(file_exists($theme_path)) {
            return $theme_path;
        }
        return THEMES_PATH . '/default/' . $template;
    }
}
