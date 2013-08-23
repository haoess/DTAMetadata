<?php

namespace DTA\MetadataBundle\Model\Master\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'publication_publicationgroup' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.DTA.MetadataBundle.Model.Master.map
 */
class PublicationPublicationgroupTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.DTA.MetadataBundle.Model.Master.map.PublicationPublicationgroupTableMap';

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
        $this->setName('publication_publicationgroup');
        $this->setPhpName('PublicationPublicationgroup');
        $this->setClassname('DTA\\MetadataBundle\\Model\\Master\\PublicationPublicationgroup');
        $this->setPackage('src.DTA.MetadataBundle.Model.Master');
        $this->setUseIdGenerator(false);
        $this->setIsCrossRef(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addForeignPrimaryKey('publicationgroup_id', 'PublicationgroupId', 'INTEGER' , 'publicationgroup', 'id', true, null, null);
        $this->addForeignPrimaryKey('publication_id', 'PublicationId', 'INTEGER' , 'publication', 'id', true, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Publicationgroup', 'DTA\\MetadataBundle\\Model\\Workflow\\Publicationgroup', RelationMap::MANY_TO_ONE, array('publicationgroup_id' => 'id', ), null, null);
        $this->addRelation('Publication', 'DTA\\MetadataBundle\\Model\\Data\\Publication', RelationMap::MANY_TO_ONE, array('publication_id' => 'id', ), null, null);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'table_row_view' =>  array (
  'Id' => 'id',
  'PublicationgroupId' => 'publicationgroup_id',
  'PublicationId' => 'publication_id',
),
        );
    } // getBehaviors()

} // PublicationPublicationgroupTableMap