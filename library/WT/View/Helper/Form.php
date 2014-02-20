<?php
//declare(encoding = 'UTF-8');

/**
 * Class providing form helper methods.
 *
 * @category Library
 * @author   Rico Sonntag <mail@ricosonntag.de>
 */
class WT_View_Helper_Form
{
	/**
	 * Create a set of radio buttons for a form.
	 *
	 * @param string $name     The ID for the form element
	 * @param array  $values   Array of value=>display items
	 * @param string $selected The currently selected item (if any)
	 *
	 * @return string Radio button HTML
	 */
	public function radioButtons($name, $values, $selected)
	{
		$html = '';

		foreach ($values as $key => $value) {
			$uniqueId = $name . ((int) (microtime() * 1000000));

			$html .= '<input type="radio" name="' . $name . '" id="'
				. $uniqueId . '" value="' . WT_Filter::escapeHtml($key) . '"';

			// Beware PHP array keys are cast to integers!  Cast them back
			if (((string) $key) === ((string) $selected)) {
				$html .= ' checked="checked"';
			}

			$html .= '><label for="' . $uniqueId . '">'
				. WT_Filter::escapeHtml($value) . '</label>';
		}

		return $html;
	}

	/**
	 * Print an edit control for a Yes/No field.
	 *
	 * @param string $name     The ID for the form element
	 * @param string $selected The currently selected item (if any)
	 *
	 * @return string HTML
	 */
	public function editFieldYesNo($name, $selected = false)
	{
		return $this->radioButtons(
			$name,
			array(
				false => WT_I18N::translate('no'),
				true  => WT_I18N::translate('yes')
			),
			$selected
		);
	}

	/**
	 * Create help link.
	 *
	 * @param string $helpTopic Help topic
	 * @param string $module    Module name
	 *
	 * @return string HTML
	 */
	public function helpLink($helpTopic, $module = '')
	{
		return <<<HTML
<span class="icon-help" onclick="helpDialog('{$helpTopic}', '{$module}'); return false;">&nbsp;</span>
HTML;
	}


}
?>
