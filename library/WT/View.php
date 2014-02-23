<?php
//declare(encoding = 'UTF-8');

/**
 * Simple view class.
 *
 * @category Library
 * @author   Rico Sonntag <mail@ricosonntag.de>
 */
class WT_View
{
	/**
	 * File name of view to render.
	 *
	 * @var string
	 */
	protected $fileName;

	/**
	 * Template directory.
	 *
	 * @var string
	 */
	protected $templateDir = null;

	/**
	 * Data assigned to the view.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Constructor.
	 *
	 * @param string $fileName File name of view
	 *
	 * @return void
	 */
	public function __construct($fileName)
	{
		$this->fileName = $fileName;
	}

	/**
	 * Set template directory.
	 *
	 * @param string $dir Directory name.
	 *
	 * @return self
	 */
	public function setTemplateDir($dir)
	{
		$this->templateDir
			= rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		return $this;
	}

	/**
	 * Escape given string.
	 *
	 * @param string $str String to escape
	 *
	 * @return string
	 */
	public function escape($str)
	{
		return WT_Filter::escapeHtml($str);
	}

	/**
	 * Translate given string.
	 *
	 * @param string $str String to translate
	 *
	 * @return string
	 *
	 * @see WT_I18N::translate
	 */
	public function translate($str)
	{
		return $this->escape(WT_I18N::translate($str));
	}

	/**
	 * Translate a tag, for an (optional) record
	 *
	 * @param string $str Tag name
	 *
	 * @return string
	 *
	 * @see WT_Gedcom_Tag::getLabel
	 */
	public function translateTag($str)
	{
		return WT_Gedcom_Tag::getLabel($str);
	}

	/**
	 * Magic getter method to get value of an assigned variable.
	 *
	 * @param string $name Name of variable to get value
	 *
	 * @return mixed|false FALSE if variable is not assigned
	 */
	public function __get($name)
	{
		if (isset($this->data[$name])) {
			return $this->data[$name];
		}

		return false;
	}

	/**
	 * Magic setter method to assign data to the view.
	 *
	 * @param string $name  Name of variable to assign value to
	 * @param mixed  $value Value of variable
	 *
	 * @return void
	 */
	public function __set($name, $value)
	{
		$this->data[$name] = $value;
	}

	/**
	 * Render view.
	 *
	 * @return void
	 */
	public function render()
	{
		ob_start();

		include $this->templateDir . $this->fileName;

		$rendered = ob_get_clean();

		echo $rendered;
	}

	/**
	 * Render a view partial.
	 *
	 * @param string $fileName Name of partial to render
	 * @param array  $data     Data to assign to the view partial
	 *
	 * @return void
	 */
	public function partial($fileName, array $data = array())
	{
		$partialView = new WT_View($fileName);
		$partialView->setTemplateDir($this->templateDir);

		foreach ($data as $key => $value) {
			$partialView->$key = $value;
		}

		$partialView->render();
	}

	/**
	 * Return class representation as string. Auto renders an
	 * assigned child view.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->render();
	}
}
?>
