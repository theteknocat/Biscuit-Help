<?php
/**
 * Help module - provides a system for viewing help pages from general Biscuit usage to module-specific help contextual to the page being viewed
 *
 * @package Modules
 * @subpackage Help
 * @author Peter Epp
 * @copyright Copyright (c) 2009 Peter Epp (http://teknocat.org)
 * @license GNU Lesser General Public License (http://www.gnu.org/licenses/lgpl.html)
 * @version 1.0 $Id: controller.php 14283 2011-09-16 18:55:43Z teknocat $
 */
class HelpManager extends AbstractModuleController {
	/**
	 * List of names of modules and/or extensions that have help for the current page
	 *
	 * @var string
	 */
	protected $_help_names = array();
	/**
	 * Render a help page - for a specific module/extension if provided, otherwise general help
	 *
	 * @return void
	 * @author Peter Epp
	 */
	protected function action_index() {
		// Ensure Fancybox is available for anyone who wants to use it on their help page:
		LibraryLoader::load('JqueryFancybox');
		$this->register_js('footer', 'ui-tabs-enabler.js');
		$this->register_css(array('filename' => 'biscuit-help.css', 'media' => 'screen'));
		if (!empty($this->params['module_or_extension_name'])) {
			$help_file = AkInflector::underscore($this->params['module_or_extension_name']).'/views/biscuit-help.php';
			if (Crumbs::file_exists_in_load_path($help_file)) {
				$this->title(sprintf(__('%s Help'), ucwords(AkInflector::humanize(AkInflector::underscore($this->params['module_or_extension_name'])))));
				$help = Crumbs::capture_include($help_file, array('Biscuit' => $this->Biscuit));
			} else {
				$this->title(__('Help Not Found'));
				Response::http_status(404);
				$help = Crumbs::capture_include('help/views/help-not-found.php');
			}
		} else {
			$other_help_items = array();
			if (empty($this->params['module_or_extension_name'])) {
				// If we're on the general help page, find other help items to build an in-page menu
				Event::fire('build_help_menu', $this);
				if (!empty($this->_help_names)) {
					sort($this->_help_names);
					foreach ($this->_help_names as $module_or_extension_name) {
						$human_name = ucwords(AkInflector::humanize(AkInflector::underscore($module_or_extension_name)));
						$other_help_items[$human_name] = $this->url().'/'.$module_or_extension_name;
					}
				}
			}
			$help = Crumbs::capture_include('help/views/general-help.php', array('other_help_items' => $other_help_items));
			$this->title(SITE_TITLE.' Help');
		}
		$this->set_view_var('help', $help);
		$this->render();
	}
	/**
	 * Allow URLs to help page with module or extension name
	 *
	 * @return void
	 * @author Peter Epp
	 */
	public static function uri_mapping_rules() {
		return array(
			'/(?P<page_slug>user-help)\/(?P<module_or_extension_name>.+)$/'
		);
	}
	/**
	 * Add help menu items when building admin menu
	 *
	 * @param string $caller 
	 * @return void
	 * @author Peter Epp
	 */
	protected function act_on_build_admin_menu($caller) {
		if (!$this->user_can_index()) {
			// If the user does not have access to help, just skip
			return;
		}
		$menu_items['General Help'] = array(
			'url' => $this->url(),
			'ui-icon' => 'ui-icon-help'
		);
		// Let installed modules add their help. This way only modules installed on the current page add to the help menu, making it contextual
		if (empty($this->_help_names)) {
			Event::fire('build_help_menu', $this);
		}
		if (!empty($this->_help_names)) {
			sort($this->_help_names);
			foreach ($this->_help_names as $module_or_extension_name) {
				$human_name = ucwords(AkInflector::humanize(AkInflector::underscore($module_or_extension_name)));
				$menu_items[$human_name] = array(
					'url' => $this->url().'/'.$module_or_extension_name,
					'ui-icon' => 'ui-icon-help'
				);
			}
		}
		$caller->add_admin_menu_items('Help',$menu_items);
	}
	/**
	 * Add the name of a module or extension to the list of things that have help so they can be added to the help menu
	 *
	 * @param string $module_or_extension_name 
	 * @return void
	 * @author Peter Epp
	 */
	public function add_help_for($module_or_extension_name) {
		$this->_help_names[] = $module_or_extension_name;
	}
	/**
	 * Add breadcrumb based on the current action if it is not "index" and the current module is primary.
	 *
	 * @param Navigation $Navigation 
	 * @return void
	 * @author Peter Epp
	 */
	protected function act_on_build_breadcrumbs($Navigation) {
		if ($this->action() == 'index' && $this->is_primary() && !empty($this->params['module_or_extension_name'])) {
			$Navigation->add_breadcrumb($this->url().'/'.$this->params['module_or_extension_name'], ucwords(AkInflector::humanize(AkInflector::underscore($this->params['module_or_extension_name']))));
		}
	}
	/**
	 * Installus
	 *
	 * @param string $module_id 
	 * @return void
	 * @author Peter Epp
	 */
	public static function install_migration($module_id) {
		$my_page = DB::fetch_one("SELECT `id` FROM `page_index` WHERE `slug` = 'user-help'");
		if (!$my_page) {
			DB::query("INSERT INTO `page_index` SET `parent` = 9999999, `slug` = 'user-help', `title` = 'General Help', `access_level` = 0");
			// Ensure clean install:
			DB::query("DELETE FROM `module_pages` WHERE `page_name` = 'user-help'");
			DB::query("INSERT INTO `module_pages` (`module_id`, `page_name`, `is_primary`) VALUES ({$module_id}, 'user-help', 1), ({$module_id}, '*', 0)");
			// Make sure all other installed modules are installed as secondary on the help page:
			$installed_module_ids = DB::fetch("SELECT `id` FROM `modules` WHERE `installed` = 1 AND `id` != {$module_id}");
			if (!empty($installed_module_ids)) {
				$query = "INSERT INTO `module_pages` (`module_id`, `page_name`, `is_primary`) VALUES ";
				$insert_values = array();
				foreach ($installed_module_ids as $other_id) {
					$insert_values[] = "({$other_id}, 'user-help', 0)";
				}
				$query .= implode(', ', $insert_values);
				DB::query($query);
			}
		}
		Permissions::add(__CLASS__, array('index' => 99));
	}
	/**
	 * Destallus
	 *
	 * @param string $module_id 
	 * @return void
	 * @author Peter Epp
	 */
	public static function uninstall_migration($module_id) {
		DB::query("DELETE FROM `page_index` WHERE `slug` = 'user-help'");
		DB::query("DELETE FROM `module_pages` WHERE `page_name` = 'user-help'");
		Permissions::remove(__CLASS__);
	}
}
