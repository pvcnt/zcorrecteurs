<?php

namespace Zco\Bundle\CoreBundle\DoctrineBehavior;

class ValidableTemplate extends \Doctrine_Template
{
	protected $_options = array(
		'prefix' => 'validation_',
	);
	
	public function setTableDefinition()
	{
		$this->hasColumn($this->_options['prefix'].'propdate', 'timestamp', null, array('notnull' => false));
		$this->hasColumn($this->_options['prefix'].'valdate', 'timestamp', null, array('notnull' => false));
		$this->hasColumn($this->_options['prefix'].'propreason', 'string', null, array('notnull' => true));
		$this->hasColumn($this->_options['prefix'].'valreason', 'string', null, array('notnull' => true));
		$this->hasColumn($this->_options['prefix'].'propuser_id', 'integer', null, array('notnull' => false));
		$this->hasColumn($this->_options['prefix'].'valuser_id', 'integer', null, array('notnull' => false));
		$this->hasColumn($this->_options['prefix'].'validated', 'boolean', null, array('notnull' => false));

		//$this->addListener(new ValidableListener());
	}
	
	public function setUp()
	{
		$this->hasOne('ProposingUser', array(
			'class'   => 'Utilisateur',
			'local'   => $this->_options['prefix'].'propuser_id',
			'foreign' => 'id',
		));
		$this->hasOne('ValidatingUser', array(
			'class'   => 'Utilisateur',
			'local'   => $this->_options['prefix'].'valuser_id',
			'foreign' => 'id',
		));
	}
	
	public function isRequestValidated()
	{
		$fieldName = $this->_options['prefix'].'validated';
		
		return $this->getInvoker()->$fieldName === true;
	}
	
	public function getRequestProposingUser()
	{
		return $this->getInvoker()->ProposingUser;
	}
	
	public function getRequestValidatingUser()
	{
		return $this->getInvoker()->ValidatingUser;
	}
	
	public function sendRequest($userId, $reason)
	{
		$invoker = $this->getInvoker();
		$invoker->set($this->_options['prefix'].'propdate', new \Doctrine_Expression('NOW()'));
		$invoker->set($this->_options['prefix'].'propreason', $reason);
		$invoker->set($this->_options['prefix'].'propuser_id', $userId);
		$invoker->save();
	}
	
	public function validateRequest($userId, $reason)
	{
		$invoker = $this->getInvoker();
		$invoker->set($this->_options['prefix'].'valdate', new \Doctrine_Expression('NOW()'));
		$invoker->set($this->_options['prefix'].'valreason', $reason);
		$invoker->set($this->_options['prefix'].'valuser_id', $userId);
		$invoker->set($this->_options['prefix'].'response', true);
		$invoker->save();
	}
	
	public function rejectRequest($userId, $reason)
	{
		$invoker = $this->getInvoker();
		$invoker->set($this->_options['prefix'].'valdate', new \Doctrine_Expression('NOW()'));
		$invoker->set($this->_options['prefix'].'valreason', $reason);
		$invoker->set($this->_options['prefix'].'valuser_id', $userId);
		$invoker->set($this->_options['prefix'].'response', false);
		$invoker->save();
	}
	
	public function getWaitingRequestsQueryTableProxy()
	{
		return \Doctrine_Query::create()
			->select('t.*')
			->from($this->getTable()->getTableName().' t')
			->where('t.'.$this->_options['prefix'].'propdate IS NOT NULL')
			->andWhere('t.'.$this->_options['prefix'].'validated IS NULL');
	}
}