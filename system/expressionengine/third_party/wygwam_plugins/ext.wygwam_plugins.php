<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Wygwam_plugins_ext
{
    var $name           = 'Wygwam Custom Plugins';
    var $version        = '1.2';
    var $description    = '';
    var $docs_url       = '';
    var $settings_exist = 'n';
	
    private static $_included_resources = array();

    private $_hooks = array(
        'wygwam_config',
    );

    public function activate_extension()
    {
        foreach ($this->_hooks as $hook)
        {
            ee()->db->insert('extensions', array(
                'class'    => get_class($this),
                'method'   => $hook,
                'hook'     => $hook,
                'settings' => '',
                'priority' => 10,
                'version'  => $this->version,
                'enabled'  => 'y'
            ));
        }
    }

    public function update_extension($current = NULL)
    {
        return FALSE;
    }

    public function disable_extension()
    {
        ee()->db->where('class', get_class($this))->delete('extensions');
    }

    public function wygwam_config($config, $settings)
    {
        if (($last_call = ee()->extensions->last_call) !== FALSE)
        {
            $config = $last_call;
        }

		// Find plugins
        $extraPlugins = $this->_include_resources($config['extraPlugins']);
		
        // Add our plugins to CKEditor
		if (!empty($extraPlugins))
		{
			if (!empty($config['extraPlugins'])) $config['extraPlugins'] .= ',';
			$config['extraPlugins'] .= implode(',', $extraPlugins);
		}

        return $config;
    }

    private function _include_resources($plugins)
    {
		$defaultPlugins = array('wygwam','readmore');
		$extraPlugins = array_diff(explode(',', $plugins), $defaultPlugins);
	
		$loadedPlugins = array();

		$plugin_url = URL_THIRD_THEMES.'wygwam_plugins/';
		$plugin_path = PATH_THIRD_THEMES.'wygwam_plugins/';
		
		// Scan directory for available plugins (activated via wygwam in extraPlugins settings)
		
		foreach ($extraPlugins as $plugin_name)
		{
			// Exclude any folders which begin with . or _
			if (is_file($plugin_path.$plugin_name.'/plugin.js') && (substr($plugin_name, 0, 1) != '.' && substr($plugin_name, 0, 1) != '_'))
			{
				// Is this the first time we've been called?
				if ( ! isset(self::$_included_resources[$plugin_name]))
				{

					// Tell CKEditor where to find our plugin
					ee()->cp->add_to_foot('<script type="text/javascript">CKEDITOR.plugins.addExternal("'.$plugin_name.'", "'.$plugin_url.$plugin_name.'/");</script>');
					
					// Don't do that again
					self::$_included_resources[$plugin_name] = TRUE;
				}			
				// Add our plugin
				$loadedPlugins[] = $plugin_name;
			}
		}
		
		// Scan directory for enabled plugins (activated by default)

		if (is_dir($plugin_path))
		{
			$enabled_plugins = array_diff(scandir($plugin_path), array('..', '.'));
			
			foreach ($enabled_plugins as $plugin_name)
			{
				// Exclude any folders which begin with . or _
				if (is_file($plugin_path.$plugin_name.'/plugin.js') && (substr($plugin_name, 0, 1) != '.' && substr($plugin_name, 0, 1) != '_'))
				{
					// Is this the first time we've been called?
					if ( ! isset(self::$_included_resources[$plugin_name]))
					{
						// Tell CKEditor where to find our plugin
						ee()->cp->add_to_foot('<script type="text/javascript">CKEDITOR.plugins.addExternal("'.$plugin_name.'", "'.$plugin_url.$plugin_name.'/");</script>');
						
						// Don't do that again
						self::$_included_resources[$plugin_name] = TRUE;
					}			
					// Add our plugin
					$loadedPlugins[] = $plugin_name;
				}
			}
		}

		return $loadedPlugins;

    }
	
}