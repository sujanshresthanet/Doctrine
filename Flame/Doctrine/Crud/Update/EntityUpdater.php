<?php
/**
 * Class EntityUpdater
 *
 * @author: Jiří Šifalda <sifalda.jiri@gmail.com>
 * @date: 14.09.13
 */
namespace Flame\Doctrine\Crud\Update;

use Flame\Doctrine\Entity;
use Flame\Doctrine\Values\IDataSet;
use Nette\Object;
use Flame\Doctrine\EntityDao;

class EntityUpdater extends Object implements IEntityUpdater
{

	/** @var bool  */
	private $flush = true;

	/** @var  EntityDao */
	private $dao;

	/**
	 * @param EntityDao $dao
	 */
	function __construct(EntityDao $dao)
	{
		$this->dao = $dao;
	}

	/**
	 * @param bool $flush
	 * @return $this
	 */
	public function setFlush($flush)
	{
		$this->flush = $flush;
		return $this;
	}

	/**
	 * @param IDataSet $values
	 * @param Entity|int $entity
	 * @return Entity
	 */
	public function update(IDataSet $values, $entity)
	{
		if(!$entity instanceof Entity) {
			$entity = $this->dao->find((int) $entity);
		}

		$this->beforeUpdate($entity, $values);

		$_values = $values->getEditableValues();
		foreach ($_values as $key => $value) {
			$entity->$key = $value;
		}

		$this->afterUpdate($entity, $values);

		$this->save($entity);
		return $entity;
	}

	/**
	 * @param Entity $entity
	 */
	protected function save(Entity $entity)
	{
		$this->dao->add($entity);

		if($this->flush === true) {
			$this->dao->save();
		}
	}

	/**
	 * @param Entity $entity
	 * @param IDataSet $values
	 */
	protected function beforeUpdate(Entity $entity, IDataSet $values) {}

	/**
	 * @param Entity $entity
	 * @param IDataSet $values
	 */
	protected function afterUpdate(Entity $entity, IDataSet $values) {}
}