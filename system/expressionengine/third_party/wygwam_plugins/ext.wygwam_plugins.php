<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class Wygwam_plugins_ext
{
    var $name           = 'Wygwam Custom Plugins';
    var $version        = '1.0';
    var $description    = '';
    var $docs_url       = '';
    var $settings_exist = 'n';
	
	private static $_included_resources = FALSE;

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
        $extraPlugins = $this->_include_resources();
		
        // Add our plugins to CKEditor
		if (!empty($extraPlugins))
		{
			if (!empty($config['extraPlugins'])) $config['extraPlugins'] .= ',';
			$config['extraPlugins'] .= implode(',', $extraPlugins);
		}

        return $config;
    }

    private function _include_resources()
    {
	
		$extraPlugins = array();
	
		$plugin_url = URL_THIRD_THEMES.'wygwam_plugins/';
		$plugin_path = PATH_THIRD_THEMES.'wygwam_plugins/';

		// Scan directory for plugins
		$plugins = array_diff(scandir($plugin_path), array('..', '.'));
		
		// Is this the first time we've been called?
		if (!self::$_included_resources)
		{
			foreach ($plugins as $plugin_name)
			{
				if (is_dir($plugin_path.$plugin_name))
				{
					// Tell CKEditor where to find our plugin
					ee()->cp->add_to_foot('<script type="text/javascript">CKEDITOR.plugins.addExternal("'.$plugin_name.'", "'.$plugin_url.$plugin_name.'/");</script>');
					
					// Add our plugin
					$extraPlugins[] = $plugin_name;

					// Don't do that again
					self::$_included_resources = TRUE;
				}
			}
		}
			
		return $extraPlugins;

    }
	
}