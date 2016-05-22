<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Editors.JME

/**
 * Joomla Markdown Editor Plugin
 *
 * @since  1.0
 */
class PlgEditorJME extends JPlugin
{
	/**
	 * Method to handle the onInitEditor event.
	 *  - Initialises the Editor
	 *
	 * @return  string	JavaScript Initialization string
	 *
	 * @since 1.5
	 */
	public function onInit()
	{
		$txt =	"<script type=\"text/javascript\">
					function insertAtCursor(myField, myValue)
					{
						var jmeinstance = JMEditors.add(myField);
			  		jmeinstance.editor.doc.replaceSelection(myValue);
					}
				</script>";

		return $txt;
	}

	/**
	 * Copy editor content to form field.
	 *
	 * Not applicable in this editor.
	 *
	 * @return  void
	 */
	public function onSave()
	{
		return;
	}

	/**
	 * Get the editor content.
	 *
	 * @param   string  $id  The id of the editor field.
	 *
	 * @return  string
	 */
	public function onGetContent($id)
	{
		return "document.getElementById('$id').value;\n";
	}

	/**
	 * Set the editor content.
	 *
	 * @param   string  $id    The id of the editor field.
	 * @param   string  $html  The content to set.
	 *
	 * @return  string
	 */
	public function onSetContent($id, $html)
	{
		return "document.getElementById('$id').value = $html;\n";
	}

	/**
	 * Inserts html code into the editor
	 *
	 * @param   string  $id  The id of the editor field
	 *
	 * @return  boolean  returns true when complete
	 */
	public function onGetInsertMethod($id)
	{
		static $done = false;

		// Do this only once.
		if (!$done)
		{
			$doc = JFactory::getDocument();
			$js = "\tfunction jInsertEditorText(text, editor)
			{
				insertAtCursor(document.getElementById(editor), text);
			}";
			$doc->addScriptDeclaration($js);
		}

		return true;
	}

	/**
	 * Display the editor area.
	 *
	 * @param   string   $name     The control name.
	 * @param   string   $content  The contents of the text area.
	 * @param   string   $width    The width of the text area (px or %).
	 * @param   string   $height   The height of the text area (px or %).
	 * @param   integer  $col      The number of columns for the textarea.
	 * @param   integer  $row      The number of rows for the textarea.
	 * @param   boolean  $buttons  True and the editor buttons will be displayed.
	 * @param   string   $id       An optional ID for the textarea (note: since 1.6). If not supplied the name is used.
	 * @param   string   $asset    The object asset
	 * @param   object   $author   The author.
	 * @param   array    $params   Associative array of editor parameters.
	 *
	 * @return  string
	 */
	public function onDisplay($name, $content, $width, $height, $col, $row, $buttons = true,
		$id = null, $asset = null, $author = null, $params = array())
	{
		$height = '200px';
		if (empty($id))
		{
			$id = $name;
		}

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration("window.JMEAdmin=window.JMEAdmin||{};window.JMEAdmin.config={base_url_relative:'".JUri::root()."',param_sep:'='};window.JMEAdmin.uri_params={};");

		$doc->addScript(JUri::root(true) . '/media/jme/codemirror/codemirror.js');
		$doc->addStylesheet(JUri::root(true) . '/media/jme/codemirror/codemirror.css');

		$doc->addScript(JUri::root(true) . '/media/jme/js/jme.js');
		$doc->addScript(JUri::root(true) . '/media/jme/js/ajax.js');

		$doc->addStylesheet(JUri::root(true) . '/media/jme/css/jme.css');
		$doc->addStylesheet(JUri::root(true) . '/media/jme/icomon/style.css');

		$doc->addScriptDeclaration("jQuery(function() {JMEditors.add('#".$id."');});");
		
		// Only add "px" to width and height if they are not given as a percentage
		if (is_numeric($width))
		{
			$width .= 'px';
		}

		if (is_numeric($height))
		{
			$height .= 'px';
		}
		// $data = ['height'=>200, 'markdown'=> true, 'codemirror'=> ['lineNumbers'=>true]];
		$data = array(
			'height'	=> 350,
			'markdown'	=> true,
			'name'	=> $name
		);
		$buttons = $this->_displayButtons($id, $buttons, $asset, $author);
		$editor  = "<textarea data-jme=\"".htmlentities(json_encode($data))."\" name=\"$name\" id=\"$id\" cols=\"$col\" rows=\"$row\" style=\"width: $width; height: $height;\">$content</textarea>"
			. $buttons;

		return $editor;
	}

	/**
	 * Displays the editor buttons.
	 *
	 * @param   string  $name     The control name.
	 * @param   mixed   $buttons  [array with button objects | boolean true to display buttons]
	 * @param   string  $asset    The object asset
	 * @param   object  $author   The author.
	 *
	 * @return  string HTML
	 */
	public function _displayButtons($name, $buttons, $asset, $author)
	{
		$return = '';

		$args = array(
			'name'  => $name,
			'event' => 'onGetInsertMethod'
		);

		$results = (array) $this->update($args);

		if ($results)
		{
			foreach ($results as $result)
			{
				if (is_string($result) && trim($result))
				{
					$return .= $result;
				}
			}
		}

		if (is_array($buttons) || (is_bool($buttons) && $buttons))
		{
			$buttons = $this->_subject->getButtons($name, $buttons, $asset, $author);

			$return .= JLayoutHelper::render('joomla.editors.buttons', $buttons);
		}

		return $return;
	}
}
// editor plugins name fix as its wrong groupname or class conventions
class PlgEditorsJME extends JPlugin
{
	public function onAjaxJme()
	{
		$app = JFactory::getApplication();
		$value = $app->input->post->get('text', '', 'raw');
		$html = JHtml::_('content.prepare', $value);
		echo new JResponseJson(array(), $html);
		$app->close();
	}
}
