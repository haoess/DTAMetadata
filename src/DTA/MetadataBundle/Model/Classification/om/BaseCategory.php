<?php

namespace DTA\MetadataBundle\Model\Classification\om;

use \BaseObject;
use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use DTA\MetadataBundle\Model\Classification\Category;
use DTA\MetadataBundle\Model\Classification\CategoryPeer;
use DTA\MetadataBundle\Model\Classification\CategoryQuery;
use DTA\MetadataBundle\Model\Classification\Categorytype;
use DTA\MetadataBundle\Model\Classification\CategorytypeQuery;
use DTA\MetadataBundle\Model\Data\Publication;
use DTA\MetadataBundle\Model\Data\PublicationQuery;
use DTA\MetadataBundle\Model\Master\CategoryPublication;
use DTA\MetadataBundle\Model\Master\CategoryPublicationQuery;

abstract class BaseCategory extends BaseObject implements Persistent, \DTA\MetadataBundle\Model\table_row_view\TableRowViewInterface
{
    /**
     * Peer class name
     */
    const PEER = 'DTA\\MetadataBundle\\Model\\Classification\\CategoryPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CategoryPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinit loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the description field.
     * @var        string
     */
    protected $description;

    /**
     * The value for the categorytype_id field.
     * @var        int
     */
    protected $categorytype_id;

    /**
     * @var        Categorytype
     */
    protected $aCategorytype;

    /**
     * @var        PropelObjectCollection|CategoryPublication[] Collection to store aggregation of CategoryPublication objects.
     */
    protected $collCategoryPublications;
    protected $collCategoryPublicationsPartial;

    /**
     * @var        PropelObjectCollection|Publication[] Collection to store aggregation of Publication objects.
     */
    protected $collPublications;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    // table_row_view behavior
    public static $tableRowViewCaptions = array('Id', 'Name', 'Description', 'CategorytypeId', );	public   $tableRowViewAccessors = array('Id'=>'Id', 'Name'=>'Name', 'Description'=>'Description', 'CategorytypeId'=>'CategorytypeId', );
    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $publicationsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $categoryPublicationsScheduledForDeletion = null;

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the [description] column value.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the [categorytype_id] column value.
     *
     * @return int
     */
    public function getCategorytypeId()
    {
        return $this->categorytype_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return Category The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = CategoryPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return Category The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = CategoryPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Set the value of [description] column.
     *
     * @param string $v new value
     * @return Category The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description = $v;
            $this->modifiedColumns[] = CategoryPeer::DESCRIPTION;
        }


        return $this;
    } // setDescription()

    /**
     * Set the value of [categorytype_id] column.
     *
     * @param int $v new value
     * @return Category The current object (for fluent API support)
     */
    public function setCategorytypeId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->categorytype_id !== $v) {
            $this->categorytype_id = $v;
            $this->modifiedColumns[] = CategoryPeer::CATEGORYTYPE_ID;
        }

        if ($this->aCategorytype !== null && $this->aCategorytype->getId() !== $v) {
            $this->aCategorytype = null;
        }


        return $this;
    } // setCategorytypeId()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->name = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->description = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->categorytype_id = ($row[$startcol + 3] !== null) ? (int) $row[$startcol + 3] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 4; // 4 = CategoryPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Category object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aCategorytype !== null && $this->categorytype_id !== $this->aCategorytype->getId()) {
            $this->aCategorytype = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CategoryPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = CategoryPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCategorytype = null;
            $this->collCategoryPublications = null;

            $this->collPublications = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CategoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = CategoryQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(CategoryPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                CategoryPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCategorytype !== null) {
                if ($this->aCategorytype->isModified() || $this->aCategorytype->isNew()) {
                    $affectedRows += $this->aCategorytype->save($con);
                }
                $this->setCategorytype($this->aCategorytype);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            if ($this->publicationsScheduledForDeletion !== null) {
                if (!$this->publicationsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->publicationsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    CategoryPublicationQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->publicationsScheduledForDeletion = null;
                }

                foreach ($this->getPublications() as $publication) {
                    if ($publication->isModified()) {
                        $publication->save($con);
                    }
                }
            } elseif ($this->collPublications) {
                foreach ($this->collPublications as $publication) {
                    if ($publication->isModified()) {
                        $publication->save($con);
                    }
                }
            }

            if ($this->categoryPublicationsScheduledForDeletion !== null) {
                if (!$this->categoryPublicationsScheduledForDeletion->isEmpty()) {
                    CategoryPublicationQuery::create()
                        ->filterByPrimaryKeys($this->categoryPublicationsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->categoryPublicationsScheduledForDeletion = null;
                }
            }

            if ($this->collCategoryPublications !== null) {
                foreach ($this->collCategoryPublications as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = CategoryPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . CategoryPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('category_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(CategoryPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(CategoryPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '"name"';
        }
        if ($this->isColumnModified(CategoryPeer::DESCRIPTION)) {
            $modifiedColumns[':p' . $index++]  = '"description"';
        }
        if ($this->isColumnModified(CategoryPeer::CATEGORYTYPE_ID)) {
            $modifiedColumns[':p' . $index++]  = '"categorytype_id"';
        }

        $sql = sprintf(
            'INSERT INTO "category" (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '"id"':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '"name"':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case '"description"':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);
                        break;
                    case '"categorytype_id"':
                        $stmt->bindValue($identifier, $this->categorytype_id, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objets otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCategorytype !== null) {
                if (!$this->aCategorytype->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aCategorytype->getValidationFailures());
                }
            }


            if (($retval = CategoryPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collCategoryPublications !== null) {
                    foreach ($this->collCategoryPublications as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }


            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = CategoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getName();
                break;
            case 2:
                return $this->getDescription();
                break;
            case 3:
                return $this->getCategorytypeId();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['Category'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Category'][$this->getPrimaryKey()] = true;
        $keys = CategoryPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
            $keys[2] => $this->getDescription(),
            $keys[3] => $this->getCategorytypeId(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->aCategorytype) {
                $result['Categorytype'] = $this->aCategorytype->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collCategoryPublications) {
                $result['CategoryPublications'] = $this->collCategoryPublications->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = CategoryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setName($value);
                break;
            case 2:
                $this->setDescription($value);
                break;
            case 3:
                $this->setCategorytypeId($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = CategoryPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setDescription($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setCategorytypeId($arr[$keys[3]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CategoryPeer::DATABASE_NAME);

        if ($this->isColumnModified(CategoryPeer::ID)) $criteria->add(CategoryPeer::ID, $this->id);
        if ($this->isColumnModified(CategoryPeer::NAME)) $criteria->add(CategoryPeer::NAME, $this->name);
        if ($this->isColumnModified(CategoryPeer::DESCRIPTION)) $criteria->add(CategoryPeer::DESCRIPTION, $this->description);
        if ($this->isColumnModified(CategoryPeer::CATEGORYTYPE_ID)) $criteria->add(CategoryPeer::CATEGORYTYPE_ID, $this->categorytype_id);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(CategoryPeer::DATABASE_NAME);
        $criteria->add(CategoryPeer::ID, $this->id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of Category (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());
        $copyObj->setDescription($this->getDescription());
        $copyObj->setCategorytypeId($this->getCategorytypeId());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getCategoryPublications() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addCategoryPublication($relObj->copy($deepCopy));
                }
            }

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return Category Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return CategoryPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CategoryPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a Categorytype object.
     *
     * @param             Categorytype $v
     * @return Category The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCategorytype(Categorytype $v = null)
    {
        if ($v === null) {
            $this->setCategorytypeId(NULL);
        } else {
            $this->setCategorytypeId($v->getId());
        }

        $this->aCategorytype = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the Categorytype object, it will not be re-added.
        if ($v !== null) {
            $v->addCategory($this);
        }


        return $this;
    }


    /**
     * Get the associated Categorytype object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return Categorytype The associated Categorytype object.
     * @throws PropelException
     */
    public function getCategorytype(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aCategorytype === null && ($this->categorytype_id !== null) && $doQuery) {
            $this->aCategorytype = CategorytypeQuery::create()->findPk($this->categorytype_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCategorytype->addCategories($this);
             */
        }

        return $this->aCategorytype;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('CategoryPublication' == $relationName) {
            $this->initCategoryPublications();
        }
    }

    /**
     * Clears out the collCategoryPublications collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Category The current object (for fluent API support)
     * @see        addCategoryPublications()
     */
    public function clearCategoryPublications()
    {
        $this->collCategoryPublications = null; // important to set this to null since that means it is uninitialized
        $this->collCategoryPublicationsPartial = null;

        return $this;
    }

    /**
     * reset is the collCategoryPublications collection loaded partially
     *
     * @return void
     */
    public function resetPartialCategoryPublications($v = true)
    {
        $this->collCategoryPublicationsPartial = $v;
    }

    /**
     * Initializes the collCategoryPublications collection.
     *
     * By default this just sets the collCategoryPublications collection to an empty array (like clearcollCategoryPublications());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initCategoryPublications($overrideExisting = true)
    {
        if (null !== $this->collCategoryPublications && !$overrideExisting) {
            return;
        }
        $this->collCategoryPublications = new PropelObjectCollection();
        $this->collCategoryPublications->setModel('CategoryPublication');
    }

    /**
     * Gets an array of CategoryPublication objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Category is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|CategoryPublication[] List of CategoryPublication objects
     * @throws PropelException
     */
    public function getCategoryPublications($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collCategoryPublicationsPartial && !$this->isNew();
        if (null === $this->collCategoryPublications || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collCategoryPublications) {
                // return empty collection
                $this->initCategoryPublications();
            } else {
                $collCategoryPublications = CategoryPublicationQuery::create(null, $criteria)
                    ->filterByCategory($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collCategoryPublicationsPartial && count($collCategoryPublications)) {
                      $this->initCategoryPublications(false);

                      foreach($collCategoryPublications as $obj) {
                        if (false == $this->collCategoryPublications->contains($obj)) {
                          $this->collCategoryPublications->append($obj);
                        }
                      }

                      $this->collCategoryPublicationsPartial = true;
                    }

                    $collCategoryPublications->getInternalIterator()->rewind();
                    return $collCategoryPublications;
                }

                if($partial && $this->collCategoryPublications) {
                    foreach($this->collCategoryPublications as $obj) {
                        if($obj->isNew()) {
                            $collCategoryPublications[] = $obj;
                        }
                    }
                }

                $this->collCategoryPublications = $collCategoryPublications;
                $this->collCategoryPublicationsPartial = false;
            }
        }

        return $this->collCategoryPublications;
    }

    /**
     * Sets a collection of CategoryPublication objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $categoryPublications A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Category The current object (for fluent API support)
     */
    public function setCategoryPublications(PropelCollection $categoryPublications, PropelPDO $con = null)
    {
        $categoryPublicationsToDelete = $this->getCategoryPublications(new Criteria(), $con)->diff($categoryPublications);

        $this->categoryPublicationsScheduledForDeletion = unserialize(serialize($categoryPublicationsToDelete));

        foreach ($categoryPublicationsToDelete as $categoryPublicationRemoved) {
            $categoryPublicationRemoved->setCategory(null);
        }

        $this->collCategoryPublications = null;
        foreach ($categoryPublications as $categoryPublication) {
            $this->addCategoryPublication($categoryPublication);
        }

        $this->collCategoryPublications = $categoryPublications;
        $this->collCategoryPublicationsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related CategoryPublication objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related CategoryPublication objects.
     * @throws PropelException
     */
    public function countCategoryPublications(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collCategoryPublicationsPartial && !$this->isNew();
        if (null === $this->collCategoryPublications || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collCategoryPublications) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getCategoryPublications());
            }
            $query = CategoryPublicationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByCategory($this)
                ->count($con);
        }

        return count($this->collCategoryPublications);
    }

    /**
     * Method called to associate a CategoryPublication object to this object
     * through the CategoryPublication foreign key attribute.
     *
     * @param    CategoryPublication $l CategoryPublication
     * @return Category The current object (for fluent API support)
     */
    public function addCategoryPublication(CategoryPublication $l)
    {
        if ($this->collCategoryPublications === null) {
            $this->initCategoryPublications();
            $this->collCategoryPublicationsPartial = true;
        }
        if (!in_array($l, $this->collCategoryPublications->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddCategoryPublication($l);
        }

        return $this;
    }

    /**
     * @param	CategoryPublication $categoryPublication The categoryPublication object to add.
     */
    protected function doAddCategoryPublication($categoryPublication)
    {
        $this->collCategoryPublications[]= $categoryPublication;
        $categoryPublication->setCategory($this);
    }

    /**
     * @param	CategoryPublication $categoryPublication The categoryPublication object to remove.
     * @return Category The current object (for fluent API support)
     */
    public function removeCategoryPublication($categoryPublication)
    {
        if ($this->getCategoryPublications()->contains($categoryPublication)) {
            $this->collCategoryPublications->remove($this->collCategoryPublications->search($categoryPublication));
            if (null === $this->categoryPublicationsScheduledForDeletion) {
                $this->categoryPublicationsScheduledForDeletion = clone $this->collCategoryPublications;
                $this->categoryPublicationsScheduledForDeletion->clear();
            }
            $this->categoryPublicationsScheduledForDeletion[]= clone $categoryPublication;
            $categoryPublication->setCategory(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Category is new, it will return
     * an empty collection; or if this Category has previously
     * been saved, it will retrieve related CategoryPublications from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Category.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|CategoryPublication[] List of CategoryPublication objects
     */
    public function getCategoryPublicationsJoinPublication($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = CategoryPublicationQuery::create(null, $criteria);
        $query->joinWith('Publication', $join_behavior);

        return $this->getCategoryPublications($query, $con);
    }

    /**
     * Clears out the collPublications collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Category The current object (for fluent API support)
     * @see        addPublications()
     */
    public function clearPublications()
    {
        $this->collPublications = null; // important to set this to null since that means it is uninitialized
        $this->collPublicationsPartial = null;

        return $this;
    }

    /**
     * Initializes the collPublications collection.
     *
     * By default this just sets the collPublications collection to an empty collection (like clearPublications());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initPublications()
    {
        $this->collPublications = new PropelObjectCollection();
        $this->collPublications->setModel('Publication');
    }

    /**
     * Gets a collection of Publication objects related by a many-to-many relationship
     * to the current object by way of the category_publication cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Category is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Publication[] List of Publication objects
     */
    public function getPublications($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collPublications || null !== $criteria) {
            if ($this->isNew() && null === $this->collPublications) {
                // return empty collection
                $this->initPublications();
            } else {
                $collPublications = PublicationQuery::create(null, $criteria)
                    ->filterByCategory($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collPublications;
                }
                $this->collPublications = $collPublications;
            }
        }

        return $this->collPublications;
    }

    /**
     * Sets a collection of Publication objects related by a many-to-many relationship
     * to the current object by way of the category_publication cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $publications A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Category The current object (for fluent API support)
     */
    public function setPublications(PropelCollection $publications, PropelPDO $con = null)
    {
        $this->clearPublications();
        $currentPublications = $this->getPublications();

        $this->publicationsScheduledForDeletion = $currentPublications->diff($publications);

        foreach ($publications as $publication) {
            if (!$currentPublications->contains($publication)) {
                $this->doAddPublication($publication);
            }
        }

        $this->collPublications = $publications;

        return $this;
    }

    /**
     * Gets the number of Publication objects related by a many-to-many relationship
     * to the current object by way of the category_publication cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Publication objects
     */
    public function countPublications($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collPublications || null !== $criteria) {
            if ($this->isNew() && null === $this->collPublications) {
                return 0;
            } else {
                $query = PublicationQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByCategory($this)
                    ->count($con);
            }
        } else {
            return count($this->collPublications);
        }
    }

    /**
     * Associate a Publication object to this object
     * through the category_publication cross reference table.
     *
     * @param  Publication $publication The CategoryPublication object to relate
     * @return Category The current object (for fluent API support)
     */
    public function addPublication(Publication $publication)
    {
        if ($this->collPublications === null) {
            $this->initPublications();
        }
        if (!$this->collPublications->contains($publication)) { // only add it if the **same** object is not already associated
            $this->doAddPublication($publication);

            $this->collPublications[]= $publication;
        }

        return $this;
    }

    /**
     * @param	Publication $publication The publication object to add.
     */
    protected function doAddPublication($publication)
    {
        $categoryPublication = new CategoryPublication();
        $categoryPublication->setPublication($publication);
        $this->addCategoryPublication($categoryPublication);
    }

    /**
     * Remove a Publication object to this object
     * through the category_publication cross reference table.
     *
     * @param Publication $publication The CategoryPublication object to relate
     * @return Category The current object (for fluent API support)
     */
    public function removePublication(Publication $publication)
    {
        if ($this->getPublications()->contains($publication)) {
            $this->collPublications->remove($this->collPublications->search($publication));
            if (null === $this->publicationsScheduledForDeletion) {
                $this->publicationsScheduledForDeletion = clone $this->collPublications;
                $this->publicationsScheduledForDeletion->clear();
            }
            $this->publicationsScheduledForDeletion[]= $publication;
        }

        return $this;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->name = null;
        $this->description = null;
        $this->categorytype_id = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volumne/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->collCategoryPublications) {
                foreach ($this->collCategoryPublications as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPublications) {
                foreach ($this->collPublications as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->aCategorytype instanceof Persistent) {
              $this->aCategorytype->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collCategoryPublications instanceof PropelCollection) {
            $this->collCategoryPublications->clearIterator();
        }
        $this->collCategoryPublications = null;
        if ($this->collPublications instanceof PropelCollection) {
            $this->collPublications->clearIterator();
        }
        $this->collPublications = null;
        $this->aCategorytype = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(CategoryPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

    // table_row_view behavior
    /**
     * To specify which columns are to be visible in the user display
     * (In the view that lists all database records of a class as a table)
     */
    public static function getTableViewColumnNames(){
        $rc = new \ReflectionClass(get_called_class());
        return $rc->getStaticPropertyValue("tableRowViewCaptions");
    }

    /**
     * To access the data using the specified column names.
     * @param string columnName
     */
    public function getAttributeByTableViewColumName($columnName){

        $accessor = $this->tableRowViewAccessors[$columnName];

        // don't use propel standard getters for user defined accessors
        // or for representative selector functions
        if(!strncmp($accessor, "accessor:", strlen("accessor:"))){
            $accessor = substr($accessor, strlen("accessor:"));
            return call_user_func(array($this, $accessor));
        } else {
            $result = $this->getByName($accessor, \BasePeer::TYPE_PHPNAME);
            if( is_a($result, 'DateTime') )
                $result = $result->format('d/m/Y');
            return $result;
        }
    }


}
