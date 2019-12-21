<?php

/**
 * BaseDon
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $utilisateur_id
 * @property date $date
 * @property string $nom
 * @property Utilisateur $Utilisateur
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseDon extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('zcov2_dons');
        $this->hasColumn('utilisateur_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('date', 'date', null, array(
             'type' => 'date',
             ));
        $this->hasColumn('nom', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
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
    }
}