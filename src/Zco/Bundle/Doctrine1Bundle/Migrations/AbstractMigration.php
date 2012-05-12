<?php

/**
 * Copyright 2012 Corrigraphie
 * 
 * This file is part of zCorrecteurs.fr.
 *
 * zCorrecteurs.fr is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * zCorrecteurs.fr is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with zCorrecteurs.fr. If not, see <http://www.gnu.org/licenses/>.
 *
 * Le code de ce fichier a été fortement inspiré par celui de Jonathan H. Wage 
 * <jonwage@gmail.com> développé pour Doctrine 2 et publié sous licence LGPL.
 */

namespace Zco\Bundle\Doctrine1Bundle\Migrations;

use Zco\Bundle\Doctrine1Bundle\Migrations\Configuration\Configuration;
use Zco\Bundle\Doctrine1Bundle\Migrations\Exception\IrreversibleMigrationException;
use Zco\Bundle\Doctrine1Bundle\Migrations\Exception\AbortMigrationException;
use Zco\Bundle\Doctrine1Bundle\Migrations\Exception\SkipMigrationException;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Classe abstraite devant être étendue par les migrations.
 *
 * @author vincent1870 <vincent@zcorrecteurs.fr>
 */
abstract class AbstractMigration
{
	protected $connection;
	protected $dbh;
	protected $container;
	protected $version;

	/**
	 * Constructeur.
	 *
	 * @param Version $version Version concernée par la migration
	 */
	public function __construct(Version $version)
	{
		$this->connection	 = \Doctrine_Manager::connection();
		$this->dbh           = $this->connection->getDbh();
		$this->container     = $version->getConfiguration()->getContainer();
		$this->version	     = $version;
	}

	/**
	 * Définit un nom personnalisé pour la migration (par défaut AAAAMMJJHHMMSS).
	 *
	 * @return string
	 */
	public function getName()
	{
	}

	/**
	 * Met à jour la base de données vers une version plus récente.
	 *
	 * @param OutputInterface $output Pour afficher un retour à l'utilisateur
	 */
	abstract public function up(OutputInterface $output);
	
	/**
	 * Met à jour la base de données vers une version plus ancienne.
	 *
	 * @param OutputInterface $output Pour afficher un retour à l'utilisateur
	 */
	abstract public function down(OutputInterface $output);
	
	/**
	 * Ajoute une requête à exécuter lors de la migration.
	 *
	 * @param string $sql Requête SQL
	 * @param array $params Paramètres à remplacer dans la requête
	 * @param array $params Types des paramètres de la requête
	 */
	protected function addSql($sql, array $params = array(), array $types = array())
	{
		$this->version->addSql($sql, $params, $types);
	}
	
	/**
	 * Lance une exception indiquant une migration non réversible.
	 *
	 * @param  string $message Message d'explication
	 * @throws IrreversibleMigrationException
	 */
	protected function throwIrreversibleMigrationException($message = '')
	{
		$message = strlen($message) ? $message : 'This migration is irreversible and cannot be reverted.';
		throw new IrreversibleMigrationException($message);
	}

	/**
	 * Annule la migration si une condition s'avère valide.
	 *
	 * @param  bool $condition Condition à évaluer
	 * @param  string $message Message d'explication
	 * @throws AbortMigrationException Si la condition est vraie
	 */
	public function abortIf($condition, $message = '')
	{
		$message = strlen($message) ? $message : 'Unknown Reason';
		if ($condition === true)
		{
			throw new AbortMigrationException($message);
		}
	}

	/**
	 * Saute la migration si une condition s'avère valide.
	 *
	 * @param  bool $condition Condition à évaluer
	 * @param  string $message Message d'explication
	 * @throws SkipMigrationException Si la condition est vraie
	 */
	public function skipIf($condition, $message = '')
	{
		$message = strlen($message) ? $message : 'Unknown Reason';
		if ($condition === true)
		{
			throw new SkipMigrationException($message);
		}
	}

	/**
	 * Méthode appelée juste avant l'exécution de la migration de la base de 
	 * données vers une version plus récente. Cela peut servir à effectuer des
	 * vérifications et éventuellement annuler ou sauter la migration courante.
	 * 
	 * @param OutputInterface $output Pour afficher un retour à l'utilisateur
	 */
	public function preUp(OutputInterface $output)
	{
	}

	/**
	 * Méthode appelée juste après l'exécution de la migration de la base de 
	 * données vers une version plus récente.
	 * 
	 * @param OutputInterface $output Pour afficher un retour à l'utilisateur
	 */
	public function postUp(OutputInterface $output)
	{
	}

	/**
	 * Méthode appelée juste avant l'exécution de la migration de la base de 
	 * données vers une version plus ancienne. Cela peut servir à effectuer des
	 * vérifications et éventuellement annuler la migration courante ou indiquer
	 * qu'elle n'était pas réversible.
	 * 
	 * @param OutputInterface $output Pour afficher un retour à l'utilisateur
	 */
	public function preDown(OutputInterface $output)
	{
	}

	/**
	 * Méthode appelée juste après l'exécution de la migration de la base de 
	 * données vers une version plus ancienne.
	 * 
	 * @param OutputInterface $output Pour afficher un retour à l'utilisateur
	 */
	public function postDown(OutputInterface $output)
	{
	}
}