<?php
/**
* mtwComposer
*
* @version $Id$
* @package Matware
* @subpackage mtwComposer
* @copyright Copyright 2004 - 2016 Matias Aguirre. All rights reserved.
* @license GNU General Public License version 2 or later.
* @author Matias Aguirre <maguirre@matware.com.ar>
* @link http://www.matware.com.ar
*/

defined('_JEXEC') or die;

use Joomla\Filesystem\File;
use Joomla\Http\Http;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StringOutput;
use Symfony\Component\Console\Input\ArrayInput;

use ComposerConsoleConsole\Helper;
use ComposerConsole\Controller\Phar;

/**
 * Ajaxgettask Webservice Controller
 *
 * @package     Joomla!
 * @subpackage  Composer
 * @since       1.0
 */
class mtwComposerControllerAjax extends JControllerLegacy
{
	/**
	 * @var		string	The context for persistent state.
	 * @since   1.0
	 */
	protected $context = 'com_mtwcomposer.ajax';


	protected $composer_data = array(
		'url' => 'https://getcomposer.org/composer.phar',
		'dir' => JPATH_ROOT,
		'bin' => JPATH_ROOT.'/media/com_mtwcomposer/phar/composer.phar',
		'json' => JPATH_ROOT.'/composer.json',
		'conf' => array(
			"minimum-stability" => "dev"
		)
	);

	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the model class name.
	 *
	 * @return  mtwComposerModel
	 * @since   1.0
	 */
	public function getModel($name = '', $prefix = 'mtwComposerModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Execute the controller.
	 *
	 * @return  void  Redirects the application
	 *
	 * @since   1.0
	 */
	public function execute($task = '')
	{
		$this->app = \JFactory::getApplication();
		$this->input = $this->app->input;

		$task = $this->input->get('task');

		if (!empty($task))
		{
			$this->$task();
		}

		$this->app->close();
	}

	function command()
	{
		set_time_limit(-1);

		jimport('joomla.application.component.helper');
		$this->options = JComponentHelper::getParams(JRequest::getVar('option'));

		$command = \JFactory::getApplication()->input->getValue('command');
		$explode = explode(' ', $command);
		$command = $explode[1];
		$command2 = isset($explode[2]) ? ' ' . $explode[2] : '';

		// Download composer.phar
		$this->downloadComposer();

		// Require composer bootstrap
		require_once "phar://{$this->composer_data['bin']}/src/bootstrap.php";

		if ($this->options->get('minimum-stability') == 'dev')
		{
			$conf_json = json_encode($this->composer_data['conf'],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
			$conf_json_merge = json_encode(array_merge(json_decode($conf_json, true), json_decode(file_get_contents($this->composer_data['json']), true)), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
			file_put_contents($this->composer_data['json'], $conf_json_merge);
		}

		// Use root directory
		chdir($this->composer_data['dir']);
		putenv("COMPOSER_HOME={$this->composer_data['dir']}");

		// Force to use php://output instead of php://stdout
		putenv("OSTYPE=OS400");

		// Get the application console instance
		$app = new \Composer\Console\Application();
		$factory = new \Composer\Factory();
		$output = $factory->createOutput();

		// Build commands and arguments array
		$array = array();
		$array['command'] = trim($command);

		if ($array['command'] == 'require' || $array['command'] == 'remove')
		{
			$array['packages'] = array(trim($command2));
		}

		if ($array['command'] == 'self-update')
		{
			$array[$command2] = true;
		}

		// Enable debug
		if ((int) $this->options->get('debug') == 1)
		{
			$array['-vvv'] = true;
		}

		// Set composer base root to Joomla! root
		$array['-d'] = JPATH_ROOT;

		// Get input
		$input = new \Symfony\Component\Console\Input\ArrayInput($array);

		// Set interactive to false
		//$input->setInteractive(false);

		// Run application
		$return = $app->run($input,$output);
	}

	/**
	 * Get status
	 *
	 * @return  void
	 */
	function status()
	{
    $output = array(
      'composer' => file_exists($this->composer_data['bin']),
      'composer_extracted' => file_exists(dirname(__DIR__) . '/extracted'),
      'installer' => file_exists(dirname(__DIR__) . '/includes/installer.php'),
    );
    header("Content-Type: text/json; charset=utf-8");
    echo json_encode($output);
	}

	/**
	 * Download composer installer
	 *
	 * @return  void
	 */
	 function downloadComposer()
	 {
		 if (!file_exists($this->composer_data['bin']))
		 {
			 copy($this->composer_data['url'], $this->composer_data['bin']);
		 }
	 }
}
