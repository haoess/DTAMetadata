<?php

namespace DTA\MetadataBundle\Model\Workflow\om;

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
use DTA\MetadataBundle\Model\WritWritgroup;
use DTA\MetadataBundle\Model\WritWritgroupQuery;
use DTA\MetadataBundle\Model\Publication\Writ;
use DTA\MetadataBundle\Model\Publication\WritQuery;
use DTA\MetadataBundle\Model\Workflow\Task;
use DTA\MetadataBundle\Model\Workflow\TaskQuery;
use DTA\MetadataBundle\Model\Workflow\Writgroup;
use DTA\MetadataBundle\Model\Workflow\WritgroupPeer;
use DTA\MetadataBundle\Model\Workflow\WritgroupQuery;

abstract class BaseWritgroup extends BaseObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'DTA\\MetadataBundle\\Model\\Workflow\\WritgroupPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        WritgroupPeer
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
     * @var        PropelObjectCollection|Task[] Collection to store aggregation of Task objects.
     */
    protected $collTasks;
    protected $collTasksPartial;

    /**
     * @var        PropelObjectCollection|WritWritgroup[] Collection to store aggregation of WritWritgroup objects.
     */
    protected $collWritWritgroups;
    protected $collWritWritgroupsPartial;

    /**
     * @var        PropelObjectCollection|Writ[] Collection to store aggregation of Writ objects.
     */
    protected $collWrits;

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
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $writsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $tasksScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var		PropelObjectCollection
     */
    protected $writWritgroupsScheduledForDeletion = null;

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
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return Writgroup The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = WritgroupPeer::ID;
        }


        return $this;
    } // setId()

    /**
     * Set the value of [name] column.
     *
     * @param string $v new value
     * @return Writgroup The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = WritgroupPeer::NAME;
        }


        return $this;
    } // setName()

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
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);
            return $startcol + 2; // 2 = WritgroupPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating Writgroup object", $e);
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
            $con = Propel::getConnection(WritgroupPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = WritgroupPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collTasks = null;

            $this->collWritWritgroups = null;

            $this->collWrits = null;
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
            $con = Propel::getConnection(WritgroupPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = WritgroupQuery::create()
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
            $con = Propel::getConnection(WritgroupPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
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
                WritgroupPeer::addInstanceToPool($this);
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

            if ($this->writsScheduledForDeletion !== null) {
                if (!$this->writsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    $pk = $this->getPrimaryKey();
                    foreach ($this->writsScheduledForDeletion->getPrimaryKeys(false) as $remotePk) {
                        $pks[] = array($pk, $remotePk);
                    }
                    WritWritgroupQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);
                    $this->writsScheduledForDeletion = null;
                }

                foreach ($this->getWrits() as $writ) {
                    if ($writ->isModified()) {
                        $writ->save($con);
                    }
                }
            }

            if ($this->tasksScheduledForDeletion !== null) {
                if (!$this->tasksScheduledForDeletion->isEmpty()) {
                    foreach ($this->tasksScheduledForDeletion as $task) {
                        // need to save related object because we set the relation to null
                        $task->save($con);
                    }
                    $this->tasksScheduledForDeletion = null;
                }
            }

            if ($this->collTasks !== null) {
                foreach ($this->collTasks as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->writWritgroupsScheduledForDeletion !== null) {
                if (!$this->writWritgroupsScheduledForDeletion->isEmpty()) {
                    WritWritgroupQuery::create()
                        ->filterByPrimaryKeys($this->writWritgroupsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->writWritgroupsScheduledForDeletion = null;
                }
            }

            if ($this->collWritWritgroups !== null) {
                foreach ($this->collWritWritgroups as $referrerFK) {
                    if (!$referrerFK->isDeleted()) {
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

        $this->modifiedColumns[] = WritgroupPeer::ID;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . WritgroupPeer::ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(WritgroupPeer::ID)) {
            $modifiedColumns[':p' . $index++]  = '`ID`';
        }
        if ($this->isColumnModified(WritgroupPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = '`NAME`';
        }

        $sql = sprintf(
            'INSERT INTO `writGroup` (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case '`ID`':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case '`NAME`':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', $e);
        }
        $this->setId($pk);

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
        } else {
            $this->validationFailures = $res;

            return false;
        }
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


            if (($retval = WritgroupPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }


                if ($this->collTasks !== null) {
                    foreach ($this->collTasks as $referrerFK) {
                        if (!$referrerFK->validate($columns)) {
                            $failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
                        }
                    }
                }

                if ($this->collWritWritgroups !== null) {
                    foreach ($this->collWritWritgroups as $referrerFK) {
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
        $pos = WritgroupPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
        if (isset($alreadyDumpedObjects['Writgroup'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Writgroup'][$this->getPrimaryKey()] = true;
        $keys = WritgroupPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getName(),
        );
        if ($includeForeignObjects) {
            if (null !== $this->collTasks) {
                $result['Tasks'] = $this->collTasks->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collWritWritgroups) {
                $result['WritWritgroups'] = $this->collWritWritgroups->toArray(null, true, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
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
        $pos = WritgroupPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

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
        $keys = WritgroupPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setName($arr[$keys[1]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(WritgroupPeer::DATABASE_NAME);

        if ($this->isColumnModified(WritgroupPeer::ID)) $criteria->add(WritgroupPeer::ID, $this->id);
        if ($this->isColumnModified(WritgroupPeer::NAME)) $criteria->add(WritgroupPeer::NAME, $this->name);

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
        $criteria = new Criteria(WritgroupPeer::DATABASE_NAME);
        $criteria->add(WritgroupPeer::ID, $this->id);

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
     * @param object $copyObj An object of Writgroup (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setName($this->getName());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            foreach ($this->getTasks() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addTask($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getWritWritgroups() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addWritWritgroup($relObj->copy($deepCopy));
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
     * @return Writgroup Clone of current object.
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
     * @return WritgroupPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new WritgroupPeer();
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
        if ('Task' == $relationName) {
            $this->initTasks();
        }
        if ('WritWritgroup' == $relationName) {
            $this->initWritWritgroups();
        }
    }

    /**
     * Clears out the collTasks collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Writgroup The current object (for fluent API support)
     * @see        addTasks()
     */
    public function clearTasks()
    {
        $this->collTasks = null; // important to set this to null since that means it is uninitialized
        $this->collTasksPartial = null;

        return $this;
    }

    /**
     * reset is the collTasks collection loaded partially
     *
     * @return void
     */
    public function resetPartialTasks($v = true)
    {
        $this->collTasksPartial = $v;
    }

    /**
     * Initializes the collTasks collection.
     *
     * By default this just sets the collTasks collection to an empty array (like clearcollTasks());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initTasks($overrideExisting = true)
    {
        if (null !== $this->collTasks && !$overrideExisting) {
            return;
        }
        $this->collTasks = new PropelObjectCollection();
        $this->collTasks->setModel('Task');
    }

    /**
     * Gets an array of Task objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Writgroup is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|Task[] List of Task objects
     * @throws PropelException
     */
    public function getTasks($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collTasksPartial && !$this->isNew();
        if (null === $this->collTasks || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collTasks) {
                // return empty collection
                $this->initTasks();
            } else {
                $collTasks = TaskQuery::create(null, $criteria)
                    ->filterByWritgroup($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collTasksPartial && count($collTasks)) {
                      $this->initTasks(false);

                      foreach($collTasks as $obj) {
                        if (false == $this->collTasks->contains($obj)) {
                          $this->collTasks->append($obj);
                        }
                      }

                      $this->collTasksPartial = true;
                    }

                    return $collTasks;
                }

                if($partial && $this->collTasks) {
                    foreach($this->collTasks as $obj) {
                        if($obj->isNew()) {
                            $collTasks[] = $obj;
                        }
                    }
                }

                $this->collTasks = $collTasks;
                $this->collTasksPartial = false;
            }
        }

        return $this->collTasks;
    }

    /**
     * Sets a collection of Task objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $tasks A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Writgroup The current object (for fluent API support)
     */
    public function setTasks(PropelCollection $tasks, PropelPDO $con = null)
    {
        $this->tasksScheduledForDeletion = $this->getTasks(new Criteria(), $con)->diff($tasks);

        foreach ($this->tasksScheduledForDeletion as $taskRemoved) {
            $taskRemoved->setWritgroup(null);
        }

        $this->collTasks = null;
        foreach ($tasks as $task) {
            $this->addTask($task);
        }

        $this->collTasks = $tasks;
        $this->collTasksPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Task objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related Task objects.
     * @throws PropelException
     */
    public function countTasks(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collTasksPartial && !$this->isNew();
        if (null === $this->collTasks || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTasks) {
                return 0;
            } else {
                if($partial && !$criteria) {
                    return count($this->getTasks());
                }
                $query = TaskQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByWritgroup($this)
                    ->count($con);
            }
        } else {
            return count($this->collTasks);
        }
    }

    /**
     * Method called to associate a Task object to this object
     * through the Task foreign key attribute.
     *
     * @param    Task $l Task
     * @return Writgroup The current object (for fluent API support)
     */
    public function addTask(Task $l)
    {
        if ($this->collTasks === null) {
            $this->initTasks();
            $this->collTasksPartial = true;
        }
        if (!in_array($l, $this->collTasks->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddTask($l);
        }

        return $this;
    }

    /**
     * @param	Task $task The task object to add.
     */
    protected function doAddTask($task)
    {
        $this->collTasks[]= $task;
        $task->setWritgroup($this);
    }

    /**
     * @param	Task $task The task object to remove.
     * @return Writgroup The current object (for fluent API support)
     */
    public function removeTask($task)
    {
        if ($this->getTasks()->contains($task)) {
            $this->collTasks->remove($this->collTasks->search($task));
            if (null === $this->tasksScheduledForDeletion) {
                $this->tasksScheduledForDeletion = clone $this->collTasks;
                $this->tasksScheduledForDeletion->clear();
            }
            $this->tasksScheduledForDeletion[]= $task;
            $task->setWritgroup(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Writgroup is new, it will return
     * an empty collection; or if this Writgroup has previously
     * been saved, it will retrieve related Tasks from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Writgroup.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Task[] List of Task objects
     */
    public function getTasksJoinTasktype($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TaskQuery::create(null, $criteria);
        $query->joinWith('Tasktype', $join_behavior);

        return $this->getTasks($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Writgroup is new, it will return
     * an empty collection; or if this Writgroup has previously
     * been saved, it will retrieve related Tasks from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Writgroup.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Task[] List of Task objects
     */
    public function getTasksJoinWrit($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TaskQuery::create(null, $criteria);
        $query->joinWith('Writ', $join_behavior);

        return $this->getTasks($query, $con);
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Writgroup is new, it will return
     * an empty collection; or if this Writgroup has previously
     * been saved, it will retrieve related Tasks from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Writgroup.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|Task[] List of Task objects
     */
    public function getTasksJoinUser($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = TaskQuery::create(null, $criteria);
        $query->joinWith('User', $join_behavior);

        return $this->getTasks($query, $con);
    }

    /**
     * Clears out the collWritWritgroups collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Writgroup The current object (for fluent API support)
     * @see        addWritWritgroups()
     */
    public function clearWritWritgroups()
    {
        $this->collWritWritgroups = null; // important to set this to null since that means it is uninitialized
        $this->collWritWritgroupsPartial = null;

        return $this;
    }

    /**
     * reset is the collWritWritgroups collection loaded partially
     *
     * @return void
     */
    public function resetPartialWritWritgroups($v = true)
    {
        $this->collWritWritgroupsPartial = $v;
    }

    /**
     * Initializes the collWritWritgroups collection.
     *
     * By default this just sets the collWritWritgroups collection to an empty array (like clearcollWritWritgroups());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initWritWritgroups($overrideExisting = true)
    {
        if (null !== $this->collWritWritgroups && !$overrideExisting) {
            return;
        }
        $this->collWritWritgroups = new PropelObjectCollection();
        $this->collWritWritgroups->setModel('WritWritgroup');
    }

    /**
     * Gets an array of WritWritgroup objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Writgroup is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @return PropelObjectCollection|WritWritgroup[] List of WritWritgroup objects
     * @throws PropelException
     */
    public function getWritWritgroups($criteria = null, PropelPDO $con = null)
    {
        $partial = $this->collWritWritgroupsPartial && !$this->isNew();
        if (null === $this->collWritWritgroups || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collWritWritgroups) {
                // return empty collection
                $this->initWritWritgroups();
            } else {
                $collWritWritgroups = WritWritgroupQuery::create(null, $criteria)
                    ->filterByWritgroup($this)
                    ->find($con);
                if (null !== $criteria) {
                    if (false !== $this->collWritWritgroupsPartial && count($collWritWritgroups)) {
                      $this->initWritWritgroups(false);

                      foreach($collWritWritgroups as $obj) {
                        if (false == $this->collWritWritgroups->contains($obj)) {
                          $this->collWritWritgroups->append($obj);
                        }
                      }

                      $this->collWritWritgroupsPartial = true;
                    }

                    return $collWritWritgroups;
                }

                if($partial && $this->collWritWritgroups) {
                    foreach($this->collWritWritgroups as $obj) {
                        if($obj->isNew()) {
                            $collWritWritgroups[] = $obj;
                        }
                    }
                }

                $this->collWritWritgroups = $collWritWritgroups;
                $this->collWritWritgroupsPartial = false;
            }
        }

        return $this->collWritWritgroups;
    }

    /**
     * Sets a collection of WritWritgroup objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $writWritgroups A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Writgroup The current object (for fluent API support)
     */
    public function setWritWritgroups(PropelCollection $writWritgroups, PropelPDO $con = null)
    {
        $this->writWritgroupsScheduledForDeletion = $this->getWritWritgroups(new Criteria(), $con)->diff($writWritgroups);

        foreach ($this->writWritgroupsScheduledForDeletion as $writWritgroupRemoved) {
            $writWritgroupRemoved->setWritgroup(null);
        }

        $this->collWritWritgroups = null;
        foreach ($writWritgroups as $writWritgroup) {
            $this->addWritWritgroup($writWritgroup);
        }

        $this->collWritWritgroups = $writWritgroups;
        $this->collWritWritgroupsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related WritWritgroup objects.
     *
     * @param Criteria $criteria
     * @param boolean $distinct
     * @param PropelPDO $con
     * @return int             Count of related WritWritgroup objects.
     * @throws PropelException
     */
    public function countWritWritgroups(Criteria $criteria = null, $distinct = false, PropelPDO $con = null)
    {
        $partial = $this->collWritWritgroupsPartial && !$this->isNew();
        if (null === $this->collWritWritgroups || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collWritWritgroups) {
                return 0;
            } else {
                if($partial && !$criteria) {
                    return count($this->getWritWritgroups());
                }
                $query = WritWritgroupQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByWritgroup($this)
                    ->count($con);
            }
        } else {
            return count($this->collWritWritgroups);
        }
    }

    /**
     * Method called to associate a WritWritgroup object to this object
     * through the WritWritgroup foreign key attribute.
     *
     * @param    WritWritgroup $l WritWritgroup
     * @return Writgroup The current object (for fluent API support)
     */
    public function addWritWritgroup(WritWritgroup $l)
    {
        if ($this->collWritWritgroups === null) {
            $this->initWritWritgroups();
            $this->collWritWritgroupsPartial = true;
        }
        if (!in_array($l, $this->collWritWritgroups->getArrayCopy(), true)) { // only add it if the **same** object is not already associated
            $this->doAddWritWritgroup($l);
        }

        return $this;
    }

    /**
     * @param	WritWritgroup $writWritgroup The writWritgroup object to add.
     */
    protected function doAddWritWritgroup($writWritgroup)
    {
        $this->collWritWritgroups[]= $writWritgroup;
        $writWritgroup->setWritgroup($this);
    }

    /**
     * @param	WritWritgroup $writWritgroup The writWritgroup object to remove.
     * @return Writgroup The current object (for fluent API support)
     */
    public function removeWritWritgroup($writWritgroup)
    {
        if ($this->getWritWritgroups()->contains($writWritgroup)) {
            $this->collWritWritgroups->remove($this->collWritWritgroups->search($writWritgroup));
            if (null === $this->writWritgroupsScheduledForDeletion) {
                $this->writWritgroupsScheduledForDeletion = clone $this->collWritWritgroups;
                $this->writWritgroupsScheduledForDeletion->clear();
            }
            $this->writWritgroupsScheduledForDeletion[]= $writWritgroup;
            $writWritgroup->setWritgroup(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Writgroup is new, it will return
     * an empty collection; or if this Writgroup has previously
     * been saved, it will retrieve related WritWritgroups from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Writgroup.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param PropelPDO $con optional connection object
     * @param string $join_behavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return PropelObjectCollection|WritWritgroup[] List of WritWritgroup objects
     */
    public function getWritWritgroupsJoinWrit($criteria = null, $con = null, $join_behavior = Criteria::LEFT_JOIN)
    {
        $query = WritWritgroupQuery::create(null, $criteria);
        $query->joinWith('Writ', $join_behavior);

        return $this->getWritWritgroups($query, $con);
    }

    /**
     * Clears out the collWrits collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return Writgroup The current object (for fluent API support)
     * @see        addWrits()
     */
    public function clearWrits()
    {
        $this->collWrits = null; // important to set this to null since that means it is uninitialized
        $this->collWritsPartial = null;

        return $this;
    }

    /**
     * Initializes the collWrits collection.
     *
     * By default this just sets the collWrits collection to an empty collection (like clearWrits());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initWrits()
    {
        $this->collWrits = new PropelObjectCollection();
        $this->collWrits->setModel('Writ');
    }

    /**
     * Gets a collection of Writ objects related by a many-to-many relationship
     * to the current object by way of the writ_writGroup cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this Writgroup is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|Writ[] List of Writ objects
     */
    public function getWrits($criteria = null, PropelPDO $con = null)
    {
        if (null === $this->collWrits || null !== $criteria) {
            if ($this->isNew() && null === $this->collWrits) {
                // return empty collection
                $this->initWrits();
            } else {
                $collWrits = WritQuery::create(null, $criteria)
                    ->filterByWritgroup($this)
                    ->find($con);
                if (null !== $criteria) {
                    return $collWrits;
                }
                $this->collWrits = $collWrits;
            }
        }

        return $this->collWrits;
    }

    /**
     * Sets a collection of Writ objects related by a many-to-many relationship
     * to the current object by way of the writ_writGroup cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param PropelCollection $writs A Propel collection.
     * @param PropelPDO $con Optional connection object
     * @return Writgroup The current object (for fluent API support)
     */
    public function setWrits(PropelCollection $writs, PropelPDO $con = null)
    {
        $this->clearWrits();
        $currentWrits = $this->getWrits();

        $this->writsScheduledForDeletion = $currentWrits->diff($writs);

        foreach ($writs as $writ) {
            if (!$currentWrits->contains($writ)) {
                $this->doAddWrit($writ);
            }
        }

        $this->collWrits = $writs;

        return $this;
    }

    /**
     * Gets the number of Writ objects related by a many-to-many relationship
     * to the current object by way of the writ_writGroup cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related Writ objects
     */
    public function countWrits($criteria = null, $distinct = false, PropelPDO $con = null)
    {
        if (null === $this->collWrits || null !== $criteria) {
            if ($this->isNew() && null === $this->collWrits) {
                return 0;
            } else {
                $query = WritQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByWritgroup($this)
                    ->count($con);
            }
        } else {
            return count($this->collWrits);
        }
    }

    /**
     * Associate a Writ object to this object
     * through the writ_writGroup cross reference table.
     *
     * @param  Writ $writ The WritWritgroup object to relate
     * @return Writgroup The current object (for fluent API support)
     */
    public function addWrit(Writ $writ)
    {
        if ($this->collWrits === null) {
            $this->initWrits();
        }
        if (!$this->collWrits->contains($writ)) { // only add it if the **same** object is not already associated
            $this->doAddWrit($writ);

            $this->collWrits[]= $writ;
        }

        return $this;
    }

    /**
     * @param	Writ $writ The writ object to add.
     */
    protected function doAddWrit($writ)
    {
        $writWritgroup = new WritWritgroup();
        $writWritgroup->setWrit($writ);
        $this->addWritWritgroup($writWritgroup);
    }

    /**
     * Remove a Writ object to this object
     * through the writ_writGroup cross reference table.
     *
     * @param Writ $writ The WritWritgroup object to relate
     * @return Writgroup The current object (for fluent API support)
     */
    public function removeWrit(Writ $writ)
    {
        if ($this->getWrits()->contains($writ)) {
            $this->collWrits->remove($this->collWrits->search($writ));
            if (null === $this->writsScheduledForDeletion) {
                $this->writsScheduledForDeletion = clone $this->collWrits;
                $this->writsScheduledForDeletion->clear();
            }
            $this->writsScheduledForDeletion[]= $writ;
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
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
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
        if ($deep) {
            if ($this->collTasks) {
                foreach ($this->collTasks as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWritWritgroups) {
                foreach ($this->collWritWritgroups as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collWrits) {
                foreach ($this->collWrits as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        if ($this->collTasks instanceof PropelCollection) {
            $this->collTasks->clearIterator();
        }
        $this->collTasks = null;
        if ($this->collWritWritgroups instanceof PropelCollection) {
            $this->collWritWritgroups->clearIterator();
        }
        $this->collWritWritgroups = null;
        if ($this->collWrits instanceof PropelCollection) {
            $this->collWrits->clearIterator();
        }
        $this->collWrits = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(WritgroupPeer::DEFAULT_STRING_FORMAT);
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

}
