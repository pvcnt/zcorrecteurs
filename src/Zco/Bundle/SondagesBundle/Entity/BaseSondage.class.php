<?php

/**
 * BaseSondage
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $utilisateur_id
 * @property string $nom
 * @property string $description
 * @property timestamp $date_debut
 * @property timestamp $date_fin
 * @property integer $nb_questions
 * @property boolean $ouvert
 * @property Utilisateur $Utilisateur
 * @property Doctrine_Collection $Questions
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseSondage extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('zcov2_sondages');
        $this->hasColumn('utilisateur_id', 'integer', 11, array(
             'type' => 'integer',
             'length' => '11',
             ));
        $this->hasColumn('nom', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('description', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('date_debut', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('date_fin', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('nb_questions', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('ouvert', 'boolean', null, array(
             'type' => 'boolean',
             ));

        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Utilisateur', array(
             'local' => 'utilisateur_id',
             'foreign' => 'utilisateur_id'));

        $this->hasMany('SondageQuestion as Questions', array(
             'local' => 'id',
             'foreign' => 'sondage_id'));
    }
}