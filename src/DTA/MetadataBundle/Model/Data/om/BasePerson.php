<?php

namespace DTA\MetadataBundle\Model\Data\om;

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
use DTA\MetadataBundle\Model\Data\Person;
use DTA\MetadataBundle\Model\Data\PersonPeer;
use DTA\MetadataBundle\Model\Data\PersonQuery;
use DTA\MetadataBundle\Model\Data\Personalname;
use DTA\MetadataBundle\Model\Data\PersonalnameQuery;
use DTA\MetadataBundle\Model\Master\PersonPublication;
use DTA\MetadataBundle\Model\Master\PersonPublicationQuery;
use DTA\MetadataBundle\Model\Master\PersonWork;
use DTA\MetadataBundle\Model\Master\PersonWorkQuery;

abstract class BasePerson extends BaseObject implements Persistent, \DTA\MetadataBundle\Model\table_row_view\TableRowViewInterface
{
    /**
     * Peer class name
     */
    const PEER = 'DTA\\MetadataBundle\\Model\\Data\\PersonPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PersonPeer
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
     * The value for the gnd field.
     * @var        string
     */
    protected $gnd;

    /**
     * @var        PropelObjectCollection|Personalname[] Collection to store aggregation of Personalname objects.
     */
    protected $collPersonalnames;
    protected $collPersonalnamesPartial;

    /**
     * @var        PropelObjectCollection|PersonPublication[] Collection to store aggregation of PersonPublication objects.
     */
    protected $collPersonPublications;
    protected $collPersonPublicationsPartial;

    /**
     * @var        PropelObjectCollection|PersonWork[] Collection to store aggregation of PersonWork objects.
     */
    protected $collPersonWorks;
    protected $collPersonWorksPartial;

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
    public static $tableRowViewCaptions = array('id', 'erster name', 'namen gesamt', 'gnd', );
    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $personalnamesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $personPublicationsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $personWorksScheduledForDeletion = null;

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
     * Get the [gnd] column value.
     *
     * @return string
     */
    public function getGnd()
    {
        return $this->gnd;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return Person The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PersonPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [gnd] column.
     *
     * @param string $v new value
     * @return Person The current object (for fluent API support)
     */
    public function setGnd($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (string) $v;
        }

        if ($this->gnd !== $v) {
            $this->gnd = $v;
            $this->modifiedColumns[] = PersonPeer::GND;
        }


        return $this;
    } // setGnd()

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
            $this->gnd = ($row[$startcol + 1] !== null) ? (string) $row[$startcol + 1] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 2; // 2 = PersonPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Person object", $e);
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
            $con = Propel::getConnection(PersonPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = PersonPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collPersonalnames = null;

            $this->collPersonPublications = null;

            $this->collPersonWorks = null;

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
            $con = Propel::getConnection(PersonPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = PersonQuery::create()
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
            $con = Propel::getConnection(PersonPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                PersonPeer::addInstanceToPool($this);
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

            if ($this->personalnamesScheduledForDeletion !== null) {
                if (!$this->personalnamesScheduledForDeletion->isEmpty()) {
                    PersonalnameQuery::create()
                        ->filterByPrimaryKeys($this->personalnamesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->personalnamesScheduledForDeletion = null;
                }
            }

            if ($this->collPersonalnames !== null) {
                foreach ($this->collPersonalnames as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->personPublicationsScheduledForDeletion !== null) {
                if (!$this->personPublicationsScheduledForDeletion->isEmpty()) {
                    PersonPublicationQuery::create()
                        ->filterByPrimaryKeys($this->personPublicationsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->personPublicationsScheduledForDeletion = null;
                }
            }

            if ($this->collPersonPublications !== null) {
                foreach ($this->collPersonPublications as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->personWorksScheduledForDeletion !== null) {
                if (!$this->personWorksScheduledForDeletion->isEmpty()) {
                    PersonWorkQuery::create()
                        ->filterByPrimaryKeys($this->personWorksScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->personWorksScheduledForDeletion = null;
                }
            }

            if ($this->collPersonWorks !== null) {
                foreach ($this->collPersonWorks as $referrerFK) {
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

        $this->modifiedColumns[] = PersonPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PersonPeer::ID . ')');
        }
        if (null === $this->id) {
            try {
                $stmt = $con->query("SELECT nextval('person_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PersonPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '"id"';
        }
        if ($this->isColumnModified(PersonPeer::GND)) {
            $modifiedColumns[':p' . $index++]  = '"gnd"';
        }

        $sql = sprintf(
            'INSERT INTO "person" (%s) VALUES (%s)',
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
                    case '"gnd"':
                        $stmt->bindValue($identifier, $this->gnd, PDO::PARAM_STR);
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


            if (($retval = PersonPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collPersonalnames !== null) {
                    foreach ($this->collPersonalnames as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collPersonPublications !== null) {
                    foreach ($this->collPersonPublications as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collPersonWorks !== null) {
                    foreach ($this->collPersonWorks as $referrerFK) {
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
        $pos = PersonPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getGnd();
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
        if (isset($alreadyDumpedObjects['Person'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Person'][$this->getPrimaryKey()] = true;
        $keys = PersonPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getGnd(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collPersonalnames) {
                $result['Personalnames'] = $this->collPersonalnames->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPersonPublications) {
                $result['PersonPublications'] = $this->collPersonPublications->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collPersonWorks) {
                $result['PersonWorks'] = $this->collPersonWorks->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = PersonPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
                $this->setGnd($value);
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
        $keys = PersonPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setGnd($arr[$keys[1]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PersonPeer::DATABASE_NAME);

        if ($this->isColumnModified(PersonPeer::ID)) $criteria->add(PersonPeer::ID, $this->id);
        if ($this->isColumnModified(PersonPeer::GND)) $criteria->add(PersonPeer::GND, $this->gnd);

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
        $criteria = new Criteria(PersonPeer::DATABASE_NAME);
        $criteria->add(PersonPeer::ID, $this->id);

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
     * @param object $copyObj An object of Person (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setGnd($this->getGnd());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getPersonalnames() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPersonalname($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPersonPublications() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPersonPublication($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getPersonWorks() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addPersonWork($relObj->copy($deepCopy));
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
     * @return Person Clone of current object.
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
     * @return PersonPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PersonPeer();
        }

        return self::$peer;
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
        if ('Personalname' == $relationName) {
            $this->initPersonalnames();
        }
        if ('PersonPublication' == $relationName) {
            $this->initPersonPublications();
        }
        if ('PersonWork' == $relationName) {
            $this->initPersonWorks();
        }
    }

    /**
     * Clears out the collPersonalnames collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Person The current object (for fluent API support)
     * @see        addPersonalnames()
     */
    public function clearPersonalnames()
    {
        $this->collPersonalnames = null; // important to set this to null since that means it is uninitialized
        $this->collPersonalnamesPartial = null;

        return $this;
    }

    /**
     * reset is the collPersonalnames collection loaded partially
     *
     * @return void
     */
    public function resetPartialPersonalnames($v = true)
    {
        $this->collPersonalnamesPartial = $v;
    }

    /**
     * Initializes the collPersonalnames collection.
     *
     * By default this just sets the collPersonalnames collection to an empty array (like clearcollPersonalnames());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPersonalnames($overrideExisting = true)
    {
        if (null !== $this->collPersonalnames && !$overrideExisting) {
            return;
        }
        $this->collPersonalnames = new PropelObjectCollection();
        $this->collPersonalnames->setModel('Personalname');
    }

    /**
     * Gets an array of Personalname objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Person is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Personalname[] List of Personalname objects
     * @throws PropelException
     */
    public function getPersonalnames($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPersonalnamesPartial && !$this->isNew();
        if (null === $this->collPersonalnames || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPersonalnames) {
                // return empty collection
                $this->initPersonalnames();
            } else {
                $collPersonalnames = PersonalnameQuery::create(null, $criteria)
                    ->filterByPerson($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPersonalnamesPartial && count($collPersonalnames)) {
                      $this->initPersonalnames(false);

                      foreach($collPersonalnames as $obj) {
                        if (false == $this->collPersonalnames->contains($obj)) {
                          $this->collPersonalnames->append($obj);
                        }
                      }

                      $this->collPersonalnamesPartial = true;
                    }

                    $collPersonalnames->getInternalIterator()->rewind();
                    return $collPersonalnames;
                }

                if($partial && $this->collPersonalnames) {
                    foreach($this->collPersonalnames as $obj) {
                        if($obj->isNew()) {
                            $collPersonalnames[] = $obj;
                        }
                    }
                }

                $this->collPersonalnames = $collPersonalnames;
                $this->collPersonalnamesPartial = false;
            }
        }

        return $this->collPersonalnames;
    }

    /**
     * Sets a collection of Personalname objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $personalnames A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Person The current object (for fluent API support)
     */
    public function setPersonalnames(PropelCollection $personalnames, PropelPDO $con = null)
    {
        $personalnamesToDelete = $this->getPersonalnames(new Criteria(), $con)->diff($personalnames);

        $this->personalnamesScheduledForDeletion = unserialize(serialize($personalnamesToDelete));

        foreach ($personalnamesToDelete as $personalnameRemoved) {
            $personalnameRemoved->setPerson(null);
        }

        $this->collPersonalnames = null;
        foreach ($personalnames as $personalname) {
            $this->addPersonalname($personalname);
        }

        $this->collPersonalnames = $personalnames;
        $this->collPersonalnamesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Personalname objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Personalname objects.
     * @throws PropelException
     */
    public function countPersonalnames(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPersonalnamesPartial && !$this->isNew();
        if (null === $this->collPersonalnames || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPersonalnames) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getPersonalnames());
            }
            $query = PersonalnameQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPerson($this)
                ->count($con);
        }

        return count($this->collPersonalnames);
    }

    /**
     * Method called to associate a Personalname object to this object
     * through the Personalname foreign key attribute.
     *
     * @param    Personalname $l Personalname
     * @return Person The current object (for fluent API support)
     */
    public function addPersonalname(Personalname $l)
    {
        if ($this->collPersonalnames === null) {
            $this->initPersonalnames();
            $this->collPersonalnamesPartial = true;
        }
        if (!in_array($l, $this->collPersonalnames->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPersonalname($l);
        }

        return $this;
    }

    /**
     * @param	Personalname $personalname The personalname object to add.
     */
    protected function doAddPersonalname($personalname)
    {
        $this->collPersonalnames[]= $personalname;
        $personalname->setPerson($this);
    }

    /**
     * @param	Personalname $personalname The personalname object to remove.
     * @return Person The current object (for fluent API support)
     */
    public function removePersonalname($personalname)
    {
        if ($this->getPersonalnames()->contains($personalname)) {
            $this->collPersonalnames->remove($this->collPersonalnames->search($personalname));
            if (null === $this->personalnamesScheduledForDeletion) {
                $this->personalnamesScheduledForDeletion = clone $this->collPersonalnames;
                $this->personalnamesScheduledForDeletion->clear();
            }
            $this->personalnamesScheduledForDeletion[]= clone $personalname;
            $personalname->setPerson(null);
        }

        return $this;
    }

    /**
     * Clears out the collPersonPublications collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Person The current object (for fluent API support)
     * @see        addPersonPublications()
     */
    public function clearPersonPublications()
    {
        $this->collPersonPublications = null; // important to set this to null since that means it is uninitialized
        $this->collPersonPublicationsPartial = null;

        return $this;
    }

    /**
     * reset is the collPersonPublications collection loaded partially
     *
     * @return void
     */
    public function resetPartialPersonPublications($v = true)
    {
        $this->collPersonPublicationsPartial = $v;
    }

    /**
     * Initializes the collPersonPublications collection.
     *
     * By default this just sets the collPersonPublications collection to an empty array (like clearcollPersonPublications());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPersonPublications($overrideExisting = true)
    {
        if (null !== $this->collPersonPublications && !$overrideExisting) {
            return;
        }
        $this->collPersonPublications = new PropelObjectCollection();
        $this->collPersonPublications->setModel('PersonPublication');
    }

    /**
     * Gets an array of PersonPublication objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Person is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|PersonPublication[] List of PersonPublication objects
     * @throws PropelException
     */
    public function getPersonPublications($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPersonPublicationsPartial && !$this->isNew();
        if (null === $this->collPersonPublications || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPersonPublications) {
                // return empty collection
                $this->initPersonPublications();
            } else {
                $collPersonPublications = PersonPublicationQuery::create(null, $criteria)
                    ->filterByPerson($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPersonPublicationsPartial && count($collPersonPublications)) {
                      $this->initPersonPublications(false);

                      foreach($collPersonPublications as $obj) {
                        if (false == $this->collPersonPublications->contains($obj)) {
                          $this->collPersonPublications->append($obj);
                        }
                      }

                      $this->collPersonPublicationsPartial = true;
                    }

                    $collPersonPublications->getInternalIterator()->rewind();
                    return $collPersonPublications;
                }

                if($partial && $this->collPersonPublications) {
                    foreach($this->collPersonPublications as $obj) {
                        if($obj->isNew()) {
                            $collPersonPublications[] = $obj;
                        }
                    }
                }

                $this->collPersonPublications = $collPersonPublications;
                $this->collPersonPublicationsPartial = false;
            }
        }

        return $this->collPersonPublications;
    }

    /**
     * Sets a collection of PersonPublication objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $personPublications A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Person The current object (for fluent API support)
     */
    public function setPersonPublications(PropelCollection $personPublications, PropelPDO $con = null)
    {
        $personPublicationsToDelete = $this->getPersonPublications(new Criteria(), $con)->diff($personPublications);

        $this->personPublicationsScheduledForDeletion = unserialize(serialize($personPublicationsToDelete));

        foreach ($personPublicationsToDelete as $personPublicationRemoved) {
            $personPublicationRemoved->setPerson(null);
        }

        $this->collPersonPublications = null;
        foreach ($personPublications as $personPublication) {
            $this->addPersonPublication($personPublication);
        }

        $this->collPersonPublications = $personPublications;
        $this->collPersonPublicationsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PersonPublication objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related PersonPublication objects.
     * @throws PropelException
     */
    public function countPersonPublications(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPersonPublicationsPartial && !$this->isNew();
        if (null === $this->collPersonPublications || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPersonPublications) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getPersonPublications());
            }
            $query = PersonPublicationQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPerson($this)
                ->count($con);
        }

        return count($this->collPersonPublications);
    }

    /**
     * Method called to associate a PersonPublication object to this object
     * through the PersonPublication foreign key attribute.
     *
     * @param    PersonPublication $l PersonPublication
     * @return Person The current object (for fluent API support)
     */
    public function addPersonPublication(PersonPublication $l)
    {
        if ($this->collPersonPublications === null) {
            $this->initPersonPublications();
            $this->collPersonPublicationsPartial = true;
        }
        if (!in_array($l, $this->collPersonPublications->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPersonPublication($l);
        }

        return $this;
    }

    /**
     * @param	PersonPublication $personPublication The personPublication object to add.
     */
    protected function doAddPersonPublication($personPublication)
    {
        $this->collPersonPublications[]= $personPublication;
        $personPublication->setPerson($this);
    }

    /**
     * @param	PersonPublication $personPublication The personPublication object to remove.
     * @return Person The current object (for fluent API support)
     */
    public function removePersonPublication($personPublication)
    {
        if ($this->getPersonPublications()->contains($personPublication)) {
            $this->collPersonPublications->remove($this->collPersonPublications->search($personPublication));
            if (null === $this->personPublicationsScheduledForDeletion) {
                $this->personPublicationsScheduledForDeletion = clone $this->collPersonPublications;
                $this->personPublicationsScheduledForDeletion->clear();
            }
            $this->personPublicationsScheduledForDeletion[]= clone $personPublication;
            $personPublication->setPerson(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Person is new, it will return
     * an empty collection; or if this Person has previously
     * been saved, it will retrieve related PersonPublications from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Person.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|PersonPublication[] List of PersonPublication objects
     */
    public function getPersonPublicationsJoinPersonrole($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PersonPublicationQuery::create(null, $criteria);
        $query->joinWith('Personrole', $join_behavior);

        return $this->getPersonPublications($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Person is new, it will return
     * an empty collection; or if this Person has previously
     * been saved, it will retrieve related PersonPublications from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Person.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|PersonPublication[] List of PersonPublication objects
     */
    public function getPersonPublicationsJoinPublication($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PersonPublicationQuery::create(null, $criteria);
        $query->joinWith('Publication', $join_behavior);

        return $this->getPersonPublications($query, $con);
    }

    /**
     * Clears out the collPersonWorks collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Person The current object (for fluent API support)
     * @see        addPersonWorks()
     */
    public function clearPersonWorks()
    {
        $this->collPersonWorks = null; // important to set this to null since that means it is uninitialized
        $this->collPersonWorksPartial = null;

        return $this;
    }

    /**
     * reset is the collPersonWorks collection loaded partially
     *
     * @return void
     */
    public function resetPartialPersonWorks($v = true)
    {
        $this->collPersonWorksPartial = $v;
    }

    /**
     * Initializes the collPersonWorks collection.
     *
     * By default this just sets the collPersonWorks collection to an empty array (like clearcollPersonWorks());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initPersonWorks($overrideExisting = true)
    {
        if (null !== $this->collPersonWorks && !$overrideExisting) {
            return;
        }
        $this->collPersonWorks = new PropelObjectCollection();
        $this->collPersonWorks->setModel('PersonWork');
    }

    /**
     * Gets an array of PersonWork objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Person is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|PersonWork[] List of PersonWork objects
     * @throws PropelException
     */
    public function getPersonWorks($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collPersonWorksPartial && !$this->isNew();
        if (null === $this->collPersonWorks || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collPersonWorks) {
                // return empty collection
                $this->initPersonWorks();
            } else {
                $collPersonWorks = PersonWorkQuery::create(null, $criteria)
                    ->filterByPerson($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collPersonWorksPartial && count($collPersonWorks)) {
                      $this->initPersonWorks(false);

                      foreach($collPersonWorks as $obj) {
                        if (false == $this->collPersonWorks->contains($obj)) {
                          $this->collPersonWorks->append($obj);
                        }
                      }

                      $this->collPersonWorksPartial = true;
                    }

                    $collPersonWorks->getInternalIterator()->rewind();
                    return $collPersonWorks;
                }

                if($partial && $this->collPersonWorks) {
                    foreach($this->collPersonWorks as $obj) {
                        if($obj->isNew()) {
                            $collPersonWorks[] = $obj;
                        }
                    }
                }

                $this->collPersonWorks = $collPersonWorks;
                $this->collPersonWorksPartial = false;
            }
        }

        return $this->collPersonWorks;
    }

    /**
     * Sets a collection of PersonWork objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $personWorks A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Person The current object (for fluent API support)
     */
    public function setPersonWorks(PropelCollection $personWorks, PropelPDO $con = null)
    {
        $personWorksToDelete = $this->getPersonWorks(new Criteria(), $con)->diff($personWorks);

        $this->personWorksScheduledForDeletion = unserialize(serialize($personWorksToDelete));

        foreach ($personWorksToDelete as $personWorkRemoved) {
            $personWorkRemoved->setPerson(null);
        }

        $this->collPersonWorks = null;
        foreach ($personWorks as $personWork) {
            $this->addPersonWork($personWork);
        }

        $this->collPersonWorks = $personWorks;
        $this->collPersonWorksPartial = false;

        return $this;
    }

    /**
     * Returns the number of related PersonWork objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related PersonWork objects.
     * @throws PropelException
     */
    public function countPersonWorks(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collPersonWorksPartial && !$this->isNew();
        if (null === $this->collPersonWorks || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collPersonWorks) {
                return 0;
            }

            if($partial && !$criteria) {
                return count($this->getPersonWorks());
            }
            $query = PersonWorkQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPerson($this)
                ->count($con);
        }

        return count($this->collPersonWorks);
    }

    /**
     * Method called to associate a PersonWork object to this object
     * through the PersonWork foreign key attribute.
     *
     * @param    PersonWork $l PersonWork
     * @return Person The current object (for fluent API support)
     */
    public function addPersonWork(PersonWork $l)
    {
        if ($this->collPersonWorks === null) {
            $this->initPersonWorks();
            $this->collPersonWorksPartial = true;
        }
        if (!in_array($l, $this->collPersonWorks->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddPersonWork($l);
        }

        return $this;
    }

    /**
     * @param	PersonWork $personWork The personWork object to add.
     */
    protected function doAddPersonWork($personWork)
    {
        $this->collPersonWorks[]= $personWork;
        $personWork->setPerson($this);
    }

    /**
     * @param	PersonWork $personWork The personWork object to remove.
     * @return Person The current object (for fluent API support)
     */
    public function removePersonWork($personWork)
    {
        if ($this->getPersonWorks()->contains($personWork)) {
            $this->collPersonWorks->remove($this->collPersonWorks->search($personWork));
            if (null === $this->personWorksScheduledForDeletion) {
                $this->personWorksScheduledForDeletion = clone $this->collPersonWorks;
                $this->personWorksScheduledForDeletion->clear();
            }
            $this->personWorksScheduledForDeletion[]= clone $personWork;
            $personWork->setPerson(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Person is new, it will return
     * an empty collection; or if this Person has previously
     * been saved, it will retrieve related PersonWorks from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Person.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|PersonWork[] List of PersonWork objects
     */
    public function getPersonWorksJoinPersonrole($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PersonWorkQuery::create(null, $criteria);
        $query->joinWith('Personrole', $join_behavior);

        return $this->getPersonWorks($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Person is new, it will return
     * an empty collection; or if this Person has previously
     * been saved, it will retrieve related PersonWorks from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Person.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|PersonWork[] List of PersonWork objects
     */
    public function getPersonWorksJoinWork($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = PersonWorkQuery::create(null, $criteria);
        $query->joinWith('Work', $join_behavior);

        return $this->getPersonWorks($query, $con);
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->id = null;
        $this->gnd = null;
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
            if ($this->collPersonalnames) {
                foreach ($this->collPersonalnames as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPersonPublications) {
                foreach ($this->collPersonPublications as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collPersonWorks) {
                foreach ($this->collPersonWorks as $o) {
                    $o->clearAllReferences($deep);
                }
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        if ($this->collPersonalnames instanceof PropelCollection) {
            $this->collPersonalnames->clearIterator();
        }
        $this->collPersonalnames = null;
        if ($this->collPersonPublications instanceof PropelCollection) {
            $this->collPersonPublications->clearIterator();
        }
        $this->collPersonPublications = null;
        if ($this->collPersonWorks instanceof PropelCollection) {
            $this->collPersonWorks->clearIterator();
        }
        $this->collPersonWorks = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(PersonPeer::DEFAULT_STRING_FORMAT);
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

    /**
     * Selects one of many related entities
     */

    public function getRepresentativePersonalname(){

        if ($this->countPersonalnames() > 0) {

            $pn = $this->getPersonalnames();

            // sort by rank if available
            $rc = new \ReflectionClass(new Personalname());
            if ( $rc->hasMethod('getSortableRank')) {
                $pn->uasort(function($a, $b) {
                            return $a->getSortableRank() - $b->getSortableRank();
                        });
            }

            $pn = $pn->toKeyValue();
            return array_shift($pn);

        } else {
            return "-";
        }
    }
}