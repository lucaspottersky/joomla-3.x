<?php
defined('_JEXEC') or die;
require_once( JPATH_COMPONENT .'/helpers/models.php' );

class AgendaDirigentesModelAutoridade extends JModelItem
{
	
	/**
	 * Model context string.
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_context = 'com_agendadirigentes.autoridade';


	protected function populateState()
	{
		$app = JFactory::getApplication();

		$id	= $app->input->getInt('id');
		$this->setState('autoridade.id', $id);

		AgendadirigentesModels::setParamBeforeSetState( 'dia', 'DataBanco', date('Y-m-d') );
		$params	= $app->getParams();

		// Load the parameters.
		$this->setState('params', $params);
	}

	public function getItem($id = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id))
			{
				$id = $this->getState('autoridade.id');
			}

			$db = $this->_db;
			$query = $db->getQuery(true);
			$query->select(
						$db->quoteName('dir.id') . ', '.
						$db->quoteName('dir.name', 'dir_name') . ', '.
						$db->quoteName('dir.interino') . ', '.
						$db->quoteName('dir.state') . ', '.
						$db->quoteName('car.name', 'car_name')
					)
					->from(
						$db->quoteName('#__agendadirigentes_dirigentes', 'dir')
					)
					->join('INNER',
						$db->quoteName('#__agendadirigentes_cargos', 'car')
						. ' ON dir.cargo_id = car.id'
					)
					->where(
						$db->quoteName('dir.id') . ' = ' . $id
					);

			$query->where(
				$db->quoteName('dir.state') . ' IN (1,2)'
			);	

			$db->setQuery( (string)$query );
			$this->_item = $db->loadObject();

			if ($error = $db->getError())
			{
				$this->setError($error);
			}			
			
		}
		return $this->_item;
	}

	public function getCompromissos()
	{
		$compromissosModel = $this->getInstance('compromissos', 'AgendaDirigentesModel');
		return $compromissosModel->getItems();
	}

}