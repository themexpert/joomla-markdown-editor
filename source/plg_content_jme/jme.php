<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors.JME
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


/**
 * Joomla Markdown Editor Plugin
 *
 * @since  1.0
 */
class PlgContentJME extends JPlugin
{
	/**
	 * Plugin that loads module positions within content
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   object   &$article  The article object.  Note $article->text is also available
	 * @param   mixed    &$params   The article params
	 * @param   integer  $page      The 'page' number
	 *
	 * @return  mixed   true if there is an error. Void otherwise.
	 *
	 * @since   1.6
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		JLoader::register( 'Parsedown', JPATH_PLUGINS . '/editors/jme/Parsedown.php' );

		$Parsedown = new Parsedown();
		$article->text = $Parsedown->text($article->text);
		
		return;
	}

}
