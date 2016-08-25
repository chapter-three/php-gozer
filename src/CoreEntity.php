<?php

namespace Gozer\Core;

use \phpDocumentor\Reflection\DocBlock;

/**
 * Class EntityBase
 * 
 * Base class for all Doctrine entities.
 */
class CoreEntity implements \JsonSerializable {
	
	protected $props;
	
	/**
	 * Constructor. Initializes the props property.
	 * 
	 * EntityBase constructor.
	 */
	public function __construct() {
		$this->initProps();
	}
	
	/**
	 * Implemented for JsonSerializable interface.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		$vars = get_object_vars($this);
		
		// Remove the $props property
		if (key_exists('props', $vars)) {
			unset($vars['props']);
		}
		
		// Format DateTime objects
		foreach ($vars as &$var) {
			if (is_a($var, 'DateTime')) {
				$var = $var->format('m/d/Y');
			}
		}
		
		return $vars;
	}
	
	/**
	 * Returns the type for the given property name.
	 * 
	 * @param $propName
	 *
	 * @return string
	 */
	protected function getPropertyType($propName) {
		$col = $this->props[$propName]['Column'];
		$start = strpos($col, 'type="') + 6;
		$end = strpos($col, '"', $start);
		$type = substr($col, $start, $end - $start);
		return $type;
	}
	
	/**
	 * Sets a property on an entity. Useful for dynamic property names.
	 * 
	 * @param $name
	 * @param $value
	 */
	public function setEntityProperty($name, $value) {
		$funcName = 'set' . ucfirst($name);
		
		if (method_exists($this, $funcName)) {
			if (is_string($value)) {
				$value = trim($value);
			}
			$this->{$funcName}($value);
		}
	}
	
	/**
	 * Parses the docblock @ lines for all the properties of this instance and 
	 * stores them in the $props property.
	 */
	public function initProps() {
		if ($this->props == null) {
			// Get import mapping and cache the result in $this->props
			$rc = new \ReflectionClass(get_class($this));
			$props = $rc->getProperties();
			foreach ($props as $prop) {
				$docBlock = new DocBlock($prop);
				$tags = $docBlock->getTags();
				foreach ($tags as $tag) {
					$this->props[$prop->getName()][$tag->getName()] = $tag->getContent();
				}
			}
		}
	}
	
	/**
	 * Helper function to determine quickly if a record exists.
	 *
	 * @param $em
	 * @param $entityName
	 * @param $field
	 * @param $value
	 *
	 * @return bool
	 */
	public function entityExists($em, $entityName, $field, $value) {
		$dql = "SELECT 1 FROM $entityName t WHERE t.$field = :value";
		$query = $em->createQuery($dql);
		$query->setParameter('value', $value);
		
		$res = $query->getResult();
		return !empty($res);
	}
	
	/**
	 * @param $em \Doctrine\ORM\EntityManager
	 * @param $entityName string
	 * @param $field string
	 * @param $filters array Simple 'and equals' filtering
	 *
	 * @return array
	 */
	public static function selectDistinct($em, $entityName, $field, $filters = array()) {
		$qb = $em->createQueryBuilder();
		$qb->select("e.$field")->distinct()
			->from($entityName, 'e')
			->where("e.$field != ''");
		
		foreach ($filters as $filterBy => $values) {
			if (is_array($values)) {
				$sql = '';
				foreach ($values as $value) {
					$sql .= "e.$filterBy = '$value' OR ";
				}
				$sql = rtrim($sql, ' OR ');
				$qb->andWhere($sql);
			}
			else {
				$qb->andWhere("e.$filterBy = '$values'");
			}
		}
		
		$types = $qb->getQuery()->getArrayResult();
		return $types;
	}
}