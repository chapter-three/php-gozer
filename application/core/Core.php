<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * Class Core
 */
abstract class Core 
{
	private $entityManager;

	/**
	 * Initializes Doctrine. Called by getEntityManager.
	 * 
	 * @throws \Doctrine\ORM\ORMException
	 */
	private function initDoctrine() 
	{
		$paths = array(DOCTRINE_ENTITIES_DIR);

		$isDevMode = false;
		if (ENV == 'dev') {
			$isDevMode = true;
		}

		// the connection configuration
		$dbParams = array(
			'driver'   => DOCTRINE_DB_DRIVER,
			'user'     => DOCTRINE_DB_USER,
			'password' => DOCTRINE_DB_PASSWORD,
			'dbname'   => DOCTRINE_DB_NAME,
		);

		$config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
		$this->entityManager = EntityManager::create($dbParams, $config);
	}

	/**
	 * Returns the Doctrine EntityManager object.
	 * Initializes the EntityManager if it is not already initialized.
	 *
	 * @return mixed
	 */
	protected function getEntityManager() {
		if ($this->entityManager == null) {
			$this->initDoctrine();
		}

		return $this->entityManager;
	}
}