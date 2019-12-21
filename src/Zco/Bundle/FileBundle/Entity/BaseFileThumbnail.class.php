<?php

/**
 * BaseFileThumbnail
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $file_id
 * @property integer $width
 * @property integer $height
 * @property integer $size
 * @property string $path
 * @property File $File
 * @property Doctrine_Collection $FileUsage
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseFileThumbnail extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('file_thumbnail');
        $this->hasColumn('id', 'integer', 11, array(
             'type' => 'integer',
             'autoincrement' => true,
             'primary' => true,
             'length' => '11',
             ));
        $this->hasColumn('file_id', 'integer', 11, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '11',
             ));
        $this->hasColumn('width', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('height', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => false,
             'length' => '4',
             ));
        $this->hasColumn('size', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('path', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));

        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('File', array(
             'local' => 'file_id',
             'foreign' => 'id'));

        $this->hasMany('FileUsage', array(
             'local' => 'id',
             'foreign' => 'thumbnail_id'));
    }
}