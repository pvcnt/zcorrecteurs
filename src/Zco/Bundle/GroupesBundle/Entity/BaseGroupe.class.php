<?php

/**
 * BaseGroupe
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $nom
 * @property string $logo
 * @property string $logo_feminin
 * @property string $class
 * @property boolean $sanction
 * @property boolean $team
 * @property boolean $secondary
 * @property Doctrine_Collection $SecondaryGroup
 * @property Doctrine_Collection $Utilisateurs
 * @property Doctrine_Collection $UserPunishment
 * @property Doctrine_Collection $Annonce
 * @property Doctrine_Collection $AnnonceGroupe
 * @property Doctrine_Collection $GroupeDroit
 * @property Doctrine_Collection $HistoriqueGroupe
 * @property Doctrine_Collection $Recrutement
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseGroupe extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('zcov2_groupes');
        $this->hasColumn('groupe_id as id', 'integer', 1, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '1',
             ));
        $this->hasColumn('groupe_nom as nom', 'string', 32, array(
             'type' => 'string',
             'length' => '32',
             ));
        $this->hasColumn('groupe_logo as logo', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('groupe_logo_feminin as logo_feminin', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('groupe_class as class', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('groupe_sanction as sanction', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('groupe_team as team', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('groupe_secondaire as secondary', 'boolean', null, array(
             'type' => 'boolean',
             ));

        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('SecondaryGroup', array(
             'local' => 'id',
             'foreign' => 'groupe_id'));

        $this->hasMany('Utilisateur as Utilisateurs', array(
             'local' => 'groupe_id',
             'foreign' => 'utilisateur_id_groupe'));

        $this->hasMany('UserPunishment', array(
             'local' => 'groupe_id',
             'foreign' => 'to_group_id'));

        $this->hasMany('Annonce', array(
             'refClass' => 'AnnonceGroupe',
             'local' => 'groupe_id',
             'foreign' => 'annonce_id'));

        $this->hasMany('AnnonceGroupe', array(
             'local' => 'id',
             'foreign' => 'groupe_id'));

        $this->hasMany('GroupeDroit', array(
             'local' => 'groupe_id',
             'foreign' => 'gd_id_groupe'));

        $this->hasMany('HistoriqueGroupe', array(
             'local' => 'groupe_id',
             'foreign' => 'ancien_groupe'));

        $this->hasMany('Recrutement', array(
             'local' => 'id',
             'foreign' => 'recrutement_id_groupe'));
    }
}