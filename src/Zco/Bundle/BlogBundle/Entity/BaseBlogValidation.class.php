<?php

/**
 * BaseBlogValidation
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $valid_id
 * @property integer $valid_id_billet
 * @property integer $valid_id_version
 * @property integer $valid_id_utilisateur
 * @property timestamp $valid_date
 * @property integer $valid_ip
 * @property string $valid_commentaire
 * @property integer $valid_decision
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseBlogValidation extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('zcov2_blog_validation');
        $this->hasColumn('valid_id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('valid_id_billet', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('valid_id_version', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('valid_id_utilisateur', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('valid_date', 'timestamp', 25, array(
             'type' => 'timestamp',
             'length' => '25',
             ));
        $this->hasColumn('valid_ip', 'integer', 4, array(
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('valid_commentaire', 'string', null, array(
             'type' => 'string',
             'length' => '',
             ));
        $this->hasColumn('valid_decision', 'integer', 1, array(
             'type' => 'integer',
             'length' => '1',
             ));

        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}