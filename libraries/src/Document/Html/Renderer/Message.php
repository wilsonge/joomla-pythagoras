<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Document
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\CMS\Document\Html\Renderer;

defined('JPATH_PLATFORM') or die;

use JFactory;
use JLog;
use Joomla\CMS\Document\Renderer as JDocumentRenderer;
use Joomla\CMS\Layout\Helper as JLayoutHelper;

/**
 * JDocument system message renderer
 *
 * @since  11.1
 */
class Message extends JDocumentRenderer
{
	/**
	 * Renders the error stack and returns the results as a string
	 *
	 * @param   string  $name     Not used.
	 * @param   array   $params   Associative array of values
	 * @param   string  $content  Not used.
	 *
	 * @return  string  The output of the script
	 *
	 * @since   11.1
	 */
	public function render($name, $params = array(), $content = null)
	{
		$msgList = $this->getData();
		$displayData = array(
			'msgList' => $msgList,
			'name' => $name,
			'params' => $params,
			'content' => $content
		);

		$app = JFactory::getApplication();
		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/message.php';

		if (file_exists($chromePath))
		{
			include_once $chromePath;
		}

		if (function_exists('renderMessage'))
		{
			JLog::add('renderMessage() is deprecated. Override system message rendering with layouts instead.', JLog::WARNING, 'deprecated');

			return renderMessage($msgList);
		}

		return JLayoutHelper::render('joomla.system.message', $displayData);
	}

	/**
	 * Get and prepare system message data for output
	 *
	 * @return  array  An array contains system message
	 *
	 * @since   12.2
	 */
	private function getData()
	{
		// Initialise variables.
		$lists = array();

		// Get the message queue
		$messages = JFactory::getApplication()->getMessageQueue();

		// Build the sorted message list
		if (is_array($messages) && !empty($messages))
		{
			foreach ($messages as $msg)
			{
				if (isset($msg['type']) && isset($msg['message']))
				{
					$lists[$msg['type']][] = $msg['message'];
				}
			}
		}

		return $lists;
	}
}