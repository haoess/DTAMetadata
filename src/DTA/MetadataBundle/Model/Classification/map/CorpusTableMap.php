<?php

namespace DTA\MetadataBundle\Model\Classification\map;

use \RelationMap;
use \TableMap;


/**
 * This class defines the structure of the 'corpus' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.src.Classification.map
 */
class CorpusTableMap extends TableMap
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'src.Classification.map.CorpusTableMap';

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
        $this->setName('corpus');
        $this->setPhpName('Corpus');
        $this->setClassname('DTA\\MetadataBundle\\Model\\Classification\\Corpus');
        $this->setPackage('src.Classification');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('ID', 'Id', 'INTEGER', true, 10, null);
        $this->addColumn('NAME', 'Name', 'LONGVARCHAR', true, null, null);
        $this->addForeignKey('WRIT_ID', 'WritId', 'INTEGER', 'writ', 'ID', false, null, null);
        // validators
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Writ', 'DTA\\MetadataBundle\\Model\\Publication\\Writ', RelationMap::MANY_TO_ONE, array('writ_id' => 'id', ), null, null);
    } // buildRelations()

} // CorpusTableMap
