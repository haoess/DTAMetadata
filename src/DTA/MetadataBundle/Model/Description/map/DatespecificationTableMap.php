<?php

namespace DTA\MetadataBundle\Model\Description\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'dateSpecification' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Description.map
 */
class DatespecificationTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Description.map.DatespecificationTableMap';

    /**
     * Initialize the table attributes, columns and validators
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('dateSpecification');
        $this->setPhpName('Datespecification');
        $this->setClassname('DTA\\MetadataBundle\\Model\\Description\\Datespecification');
        $this->setPackage('src.Description');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('YEAR', 'Year', 'INTEGER', false, null, null);
        $this->addColumn('YEARISRECONSTRUCTED', 'Yearisreconstructed', 'BOOLEAN', false, 1, false);
        $this->addColumn('COMMENTS', 'Comments', 'LONGVARCHAR', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Publication', 'DTA\\MetadataBundle\\Model\\Publication\\Publication', RelationMap::ONE_TO_MANY, array('id' => 'dateSpecification_id', ), null, null, 'Publications');
        $this->addRelation('Work', 'DTA\\MetadataBundle\\Model\\Publication\\Work', RelationMap::ONE_TO_MANY, array('id' => 'dateSpecification_id', ), null, null, 'Works');
    } // buildRelations()

} // DatespecificationTableMap
