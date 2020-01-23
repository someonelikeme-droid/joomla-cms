<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;

FormHelper::loadFieldClass('list');

/**
 * Field to load a list of all users that have logged actions
 * @since  3.9.0
 */
class JFormFieldLogCreator extends JFormFieldList
{
	/**
	 * The form field type.
	 * @var    string
	 * @since  3.9.0
	 */
	protected $type = 'LogCreator';

	/**
	 * Cached array of the category items.
	 * @var    array
	 * @since  3.9.0
	 */
	protected static $options = array();

	/**
	 * Method to get the options to populate list
	 * @return  array  The field option objects.
	 * @since   3.9.0
	 */
	protected function getOptions()
	{
		// Accepted modifiers
		$hash = md5($this->element);

		if (!isset(static::$options[$hash]))
		{
			static::$options[$hash] = parent::getOptions();
			$options = array();
			$db = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('u.id', 'value'))
				->select($db->quoteName('u.username', 'text'))
				->from($db->quoteName('#__users', 'u'))
				->join('INNER', $db->quoteName('#__action_logs', 'c') . ' ON ' . $db->quoteName('c.user_id') . ' = ' . $db->quoteName('u.id'))
				->group($db->quoteName('u.id'))
				->group($db->quoteName('u.username'))
				->order($db->quoteName('u.username'));
			$db->setQuery($query);

			if ($options = $db->loadObjectList())
			{
				static::$options[$hash] = array_merge(static::$options[$hash], $options);
			}
		}
		return static::$options[$hash];
	}
}
