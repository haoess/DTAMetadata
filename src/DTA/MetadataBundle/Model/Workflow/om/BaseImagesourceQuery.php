<?php

namespace DTA\MetadataBundle\Model\Workflow\om;

use \Criteria;
use \Exception;
use \ModelCriteria;
use \ModelJoin;
use \PDO;
use \Propel;
use \PropelCollection;
use \PropelException;
use \PropelObjectCollection;
use \PropelPDO;
use DTA\MetadataBundle\Model\Data\Publication;
use DTA\MetadataBundle\Model\Workflow\Imagesource;
use DTA\MetadataBundle\Model\Workflow\ImagesourcePeer;
use DTA\MetadataBundle\Model\Workflow\ImagesourceQuery;
use DTA\MetadataBundle\Model\Workflow\License;
use DTA\MetadataBundle\Model\Workflow\Partner;

/**
 * @method ImagesourceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method ImagesourceQuery orderByPublicationId($order = Criteria::ASC) Order by the publication_id column
 * @method ImagesourceQuery orderByPartnerId($order = Criteria::ASC) Order by the partner_id column
 * @method ImagesourceQuery orderByCataloguesignature($order = Criteria::ASC) Order by the cataloguesignature column
 * @method ImagesourceQuery orderByCatalogueurl($order = Criteria::ASC) Order by the catalogueurl column
 * @method ImagesourceQuery orderByNumfaksimiles($order = Criteria::ASC) Order by the numfaksimiles column
 * @method ImagesourceQuery orderByExtentasofcatalogue($order = Criteria::ASC) Order by the extentasofcatalogue column
 * @method ImagesourceQuery orderByNumpages($order = Criteria::ASC) Order by the numpages column
 * @method ImagesourceQuery orderByImageurl($order = Criteria::ASC) Order by the imageurl column
 * @method ImagesourceQuery orderByImageurn($order = Criteria::ASC) Order by the imageurn column
 * @method ImagesourceQuery orderByLicenseId($order = Criteria::ASC) Order by the license_id column
 *
 * @method ImagesourceQuery groupById() Group by the id column
 * @method ImagesourceQuery groupByPublicationId() Group by the publication_id column
 * @method ImagesourceQuery groupByPartnerId() Group by the partner_id column
 * @method ImagesourceQuery groupByCataloguesignature() Group by the cataloguesignature column
 * @method ImagesourceQuery groupByCatalogueurl() Group by the catalogueurl column
 * @method ImagesourceQuery groupByNumfaksimiles() Group by the numfaksimiles column
 * @method ImagesourceQuery groupByExtentasofcatalogue() Group by the extentasofcatalogue column
 * @method ImagesourceQuery groupByNumpages() Group by the numpages column
 * @method ImagesourceQuery groupByImageurl() Group by the imageurl column
 * @method ImagesourceQuery groupByImageurn() Group by the imageurn column
 * @method ImagesourceQuery groupByLicenseId() Group by the license_id column
 *
 * @method ImagesourceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method ImagesourceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method ImagesourceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method ImagesourceQuery leftJoinPublication($relationAlias = null) Adds a LEFT JOIN clause to the query using the Publication relation
 * @method ImagesourceQuery rightJoinPublication($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Publication relation
 * @method ImagesourceQuery innerJoinPublication($relationAlias = null) Adds a INNER JOIN clause to the query using the Publication relation
 *
 * @method ImagesourceQuery leftJoinLicense($relationAlias = null) Adds a LEFT JOIN clause to the query using the License relation
 * @method ImagesourceQuery rightJoinLicense($relationAlias = null) Adds a RIGHT JOIN clause to the query using the License relation
 * @method ImagesourceQuery innerJoinLicense($relationAlias = null) Adds a INNER JOIN clause to the query using the License relation
 *
 * @method ImagesourceQuery leftJoinPartner($relationAlias = null) Adds a LEFT JOIN clause to the query using the Partner relation
 * @method ImagesourceQuery rightJoinPartner($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Partner relation
 * @method ImagesourceQuery innerJoinPartner($relationAlias = null) Adds a INNER JOIN clause to the query using the Partner relation
 *
 * @method Imagesource findOne(PropelPDO $con = null) Return the first Imagesource matching the query
 * @method Imagesource findOneOrCreate(PropelPDO $con = null) Return the first Imagesource matching the query, or a new Imagesource object populated from the query conditions when no match is found
 *
 * @method Imagesource findOneByPublicationId(int $publication_id) Return the first Imagesource filtered by the publication_id column
 * @method Imagesource findOneByPartnerId(int $partner_id) Return the first Imagesource filtered by the partner_id column
 * @method Imagesource findOneByCataloguesignature(string $cataloguesignature) Return the first Imagesource filtered by the cataloguesignature column
 * @method Imagesource findOneByCatalogueurl(string $catalogueurl) Return the first Imagesource filtered by the catalogueurl column
 * @method Imagesource findOneByNumfaksimiles(int $numfaksimiles) Return the first Imagesource filtered by the numfaksimiles column
 * @method Imagesource findOneByExtentasofcatalogue(string $extentasofcatalogue) Return the first Imagesource filtered by the extentasofcatalogue column
 * @method Imagesource findOneByNumpages(int $numpages) Return the first Imagesource filtered by the numpages column
 * @method Imagesource findOneByImageurl(string $imageurl) Return the first Imagesource filtered by the imageurl column
 * @method Imagesource findOneByImageurn(string $imageurn) Return the first Imagesource filtered by the imageurn column
 * @method Imagesource findOneByLicenseId(int $license_id) Return the first Imagesource filtered by the license_id column
 *
 * @method array findById(int $id) Return Imagesource objects filtered by the id column
 * @method array findByPublicationId(int $publication_id) Return Imagesource objects filtered by the publication_id column
 * @method array findByPartnerId(int $partner_id) Return Imagesource objects filtered by the partner_id column
 * @method array findByCataloguesignature(string $cataloguesignature) Return Imagesource objects filtered by the cataloguesignature column
 * @method array findByCatalogueurl(string $catalogueurl) Return Imagesource objects filtered by the catalogueurl column
 * @method array findByNumfaksimiles(int $numfaksimiles) Return Imagesource objects filtered by the numfaksimiles column
 * @method array findByExtentasofcatalogue(string $extentasofcatalogue) Return Imagesource objects filtered by the extentasofcatalogue column
 * @method array findByNumpages(int $numpages) Return Imagesource objects filtered by the numpages column
 * @method array findByImageurl(string $imageurl) Return Imagesource objects filtered by the imageurl column
 * @method array findByImageurn(string $imageurn) Return Imagesource objects filtered by the imageurn column
 * @method array findByLicenseId(int $license_id) Return Imagesource objects filtered by the license_id column
 */
abstract class BaseImagesourceQuery extends ModelCriteria
{
    /**
     * Initializes internal state of BaseImagesourceQuery object.
     *
     * @param     string $dbName The dabase name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'DTAMetadata', $modelName = 'DTA\\MetadataBundle\\Model\\Workflow\\Imagesource', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ImagesourceQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param   ImagesourceQuery|Criteria $criteria Optional Criteria to build the query from
     *
     * @return ImagesourceQuery
     */
    public static function create($modelAlias = null, $criteria = null)
    {
        if ($criteria instanceof ImagesourceQuery) {
            return $criteria;
        }
        $query = new ImagesourceQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return   Imagesource|Imagesource[]|mixed the result, formatted by the current formatter
     */
    public function findPk($key, $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = ImagesourcePeer::getInstanceFromPool((string) $key))) && !$this->formatter) {
            // the object is alredy in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getConnection(ImagesourcePeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Alias of findPk to use instance pooling
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Imagesource A model object, or null if the key is not found
     * @throws PropelException
     */
     public function findOneById($key, $con = null)
     {
        return $this->findPk($key, $con);
     }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return                 Imagesource A model object, or null if the key is not found
     * @throws PropelException
     */
    protected function findPkSimple($key, $con)
    {
        $sql = 'SELECT "id", "publication_id", "partner_id", "cataloguesignature", "catalogueurl", "numfaksimiles", "extentasofcatalogue", "numpages", "imageurl", "imageurn", "license_id" FROM "imagesource" WHERE "id" = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $obj = new Imagesource();
            $obj->hydrate($row);
            ImagesourcePeer::addInstanceToPool($obj, (string) $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     PropelPDO $con A connection object
     *
     * @return Imagesource|Imagesource[]|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($stmt);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     PropelPDO $con an optional connection object
     *
     * @return PropelObjectCollection|Imagesource[]|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection($this->getDbName(), Propel::CONNECTION_READ);
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $stmt = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($stmt);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ImagesourcePeer::ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ImagesourcePeer::ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id >= 12
     * $query->filterById(array('max' => 12)); // WHERE id <= 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ImagesourcePeer::ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ImagesourcePeer::ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::ID, $id, $comparison);
    }

    /**
     * Filter the query on the publication_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPublicationId(1234); // WHERE publication_id = 1234
     * $query->filterByPublicationId(array(12, 34)); // WHERE publication_id IN (12, 34)
     * $query->filterByPublicationId(array('min' => 12)); // WHERE publication_id >= 12
     * $query->filterByPublicationId(array('max' => 12)); // WHERE publication_id <= 12
     * </code>
     *
     * @see       filterByPublication()
     *
     * @param     mixed $publicationId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByPublicationId($publicationId = null, $comparison = null)
    {
        if (is_array($publicationId)) {
            $useMinMax = false;
            if (isset($publicationId['min'])) {
                $this->addUsingAlias(ImagesourcePeer::PUBLICATION_ID, $publicationId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($publicationId['max'])) {
                $this->addUsingAlias(ImagesourcePeer::PUBLICATION_ID, $publicationId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::PUBLICATION_ID, $publicationId, $comparison);
    }

    /**
     * Filter the query on the partner_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPartnerId(1234); // WHERE partner_id = 1234
     * $query->filterByPartnerId(array(12, 34)); // WHERE partner_id IN (12, 34)
     * $query->filterByPartnerId(array('min' => 12)); // WHERE partner_id >= 12
     * $query->filterByPartnerId(array('max' => 12)); // WHERE partner_id <= 12
     * </code>
     *
     * @see       filterByPartner()
     *
     * @param     mixed $partnerId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByPartnerId($partnerId = null, $comparison = null)
    {
        if (is_array($partnerId)) {
            $useMinMax = false;
            if (isset($partnerId['min'])) {
                $this->addUsingAlias(ImagesourcePeer::PARTNER_ID, $partnerId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($partnerId['max'])) {
                $this->addUsingAlias(ImagesourcePeer::PARTNER_ID, $partnerId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::PARTNER_ID, $partnerId, $comparison);
    }

    /**
     * Filter the query on the cataloguesignature column
     *
     * Example usage:
     * <code>
     * $query->filterByCataloguesignature('fooValue');   // WHERE cataloguesignature = 'fooValue'
     * $query->filterByCataloguesignature('%fooValue%'); // WHERE cataloguesignature LIKE '%fooValue%'
     * </code>
     *
     * @param     string $cataloguesignature The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByCataloguesignature($cataloguesignature = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($cataloguesignature)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $cataloguesignature)) {
                $cataloguesignature = str_replace('*', '%', $cataloguesignature);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::CATALOGUESIGNATURE, $cataloguesignature, $comparison);
    }

    /**
     * Filter the query on the catalogueurl column
     *
     * Example usage:
     * <code>
     * $query->filterByCatalogueurl('fooValue');   // WHERE catalogueurl = 'fooValue'
     * $query->filterByCatalogueurl('%fooValue%'); // WHERE catalogueurl LIKE '%fooValue%'
     * </code>
     *
     * @param     string $catalogueurl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByCatalogueurl($catalogueurl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($catalogueurl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $catalogueurl)) {
                $catalogueurl = str_replace('*', '%', $catalogueurl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::CATALOGUEURL, $catalogueurl, $comparison);
    }

    /**
     * Filter the query on the numfaksimiles column
     *
     * Example usage:
     * <code>
     * $query->filterByNumfaksimiles(1234); // WHERE numfaksimiles = 1234
     * $query->filterByNumfaksimiles(array(12, 34)); // WHERE numfaksimiles IN (12, 34)
     * $query->filterByNumfaksimiles(array('min' => 12)); // WHERE numfaksimiles >= 12
     * $query->filterByNumfaksimiles(array('max' => 12)); // WHERE numfaksimiles <= 12
     * </code>
     *
     * @param     mixed $numfaksimiles The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByNumfaksimiles($numfaksimiles = null, $comparison = null)
    {
        if (is_array($numfaksimiles)) {
            $useMinMax = false;
            if (isset($numfaksimiles['min'])) {
                $this->addUsingAlias(ImagesourcePeer::NUMFAKSIMILES, $numfaksimiles['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($numfaksimiles['max'])) {
                $this->addUsingAlias(ImagesourcePeer::NUMFAKSIMILES, $numfaksimiles['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::NUMFAKSIMILES, $numfaksimiles, $comparison);
    }

    /**
     * Filter the query on the extentasofcatalogue column
     *
     * Example usage:
     * <code>
     * $query->filterByExtentasofcatalogue('fooValue');   // WHERE extentasofcatalogue = 'fooValue'
     * $query->filterByExtentasofcatalogue('%fooValue%'); // WHERE extentasofcatalogue LIKE '%fooValue%'
     * </code>
     *
     * @param     string $extentasofcatalogue The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByExtentasofcatalogue($extentasofcatalogue = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($extentasofcatalogue)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $extentasofcatalogue)) {
                $extentasofcatalogue = str_replace('*', '%', $extentasofcatalogue);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::EXTENTASOFCATALOGUE, $extentasofcatalogue, $comparison);
    }

    /**
     * Filter the query on the numpages column
     *
     * Example usage:
     * <code>
     * $query->filterByNumpages(1234); // WHERE numpages = 1234
     * $query->filterByNumpages(array(12, 34)); // WHERE numpages IN (12, 34)
     * $query->filterByNumpages(array('min' => 12)); // WHERE numpages >= 12
     * $query->filterByNumpages(array('max' => 12)); // WHERE numpages <= 12
     * </code>
     *
     * @param     mixed $numpages The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByNumpages($numpages = null, $comparison = null)
    {
        if (is_array($numpages)) {
            $useMinMax = false;
            if (isset($numpages['min'])) {
                $this->addUsingAlias(ImagesourcePeer::NUMPAGES, $numpages['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($numpages['max'])) {
                $this->addUsingAlias(ImagesourcePeer::NUMPAGES, $numpages['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::NUMPAGES, $numpages, $comparison);
    }

    /**
     * Filter the query on the imageurl column
     *
     * Example usage:
     * <code>
     * $query->filterByImageurl('fooValue');   // WHERE imageurl = 'fooValue'
     * $query->filterByImageurl('%fooValue%'); // WHERE imageurl LIKE '%fooValue%'
     * </code>
     *
     * @param     string $imageurl The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByImageurl($imageurl = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($imageurl)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $imageurl)) {
                $imageurl = str_replace('*', '%', $imageurl);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::IMAGEURL, $imageurl, $comparison);
    }

    /**
     * Filter the query on the imageurn column
     *
     * Example usage:
     * <code>
     * $query->filterByImageurn('fooValue');   // WHERE imageurn = 'fooValue'
     * $query->filterByImageurn('%fooValue%'); // WHERE imageurn LIKE '%fooValue%'
     * </code>
     *
     * @param     string $imageurn The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByImageurn($imageurn = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($imageurn)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $imageurn)) {
                $imageurn = str_replace('*', '%', $imageurn);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::IMAGEURN, $imageurn, $comparison);
    }

    /**
     * Filter the query on the license_id column
     *
     * Example usage:
     * <code>
     * $query->filterByLicenseId(1234); // WHERE license_id = 1234
     * $query->filterByLicenseId(array(12, 34)); // WHERE license_id IN (12, 34)
     * $query->filterByLicenseId(array('min' => 12)); // WHERE license_id >= 12
     * $query->filterByLicenseId(array('max' => 12)); // WHERE license_id <= 12
     * </code>
     *
     * @see       filterByLicense()
     *
     * @param     mixed $licenseId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function filterByLicenseId($licenseId = null, $comparison = null)
    {
        if (is_array($licenseId)) {
            $useMinMax = false;
            if (isset($licenseId['min'])) {
                $this->addUsingAlias(ImagesourcePeer::LICENSE_ID, $licenseId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($licenseId['max'])) {
                $this->addUsingAlias(ImagesourcePeer::LICENSE_ID, $licenseId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ImagesourcePeer::LICENSE_ID, $licenseId, $comparison);
    }

    /**
     * Filter the query by a related Publication object
     *
     * @param   Publication|PropelObjectCollection $publication The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ImagesourceQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPublication($publication, $comparison = null)
    {
        if ($publication instanceof Publication) {
            return $this
                ->addUsingAlias(ImagesourcePeer::PUBLICATION_ID, $publication->getId(), $comparison);
        } elseif ($publication instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ImagesourcePeer::PUBLICATION_ID, $publication->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPublication() only accepts arguments of type Publication or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Publication relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function joinPublication($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Publication');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Publication');
        }

        return $this;
    }

    /**
     * Use the Publication relation Publication object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \DTA\MetadataBundle\Model\Data\PublicationQuery A secondary query class using the current class as primary query
     */
    public function usePublicationQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPublication($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Publication', '\DTA\MetadataBundle\Model\Data\PublicationQuery');
    }

    /**
     * Filter the query by a related License object
     *
     * @param   License|PropelObjectCollection $license The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ImagesourceQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByLicense($license, $comparison = null)
    {
        if ($license instanceof License) {
            return $this
                ->addUsingAlias(ImagesourcePeer::LICENSE_ID, $license->getId(), $comparison);
        } elseif ($license instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ImagesourcePeer::LICENSE_ID, $license->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByLicense() only accepts arguments of type License or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the License relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function joinLicense($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('License');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'License');
        }

        return $this;
    }

    /**
     * Use the License relation License object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \DTA\MetadataBundle\Model\Workflow\LicenseQuery A secondary query class using the current class as primary query
     */
    public function useLicenseQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinLicense($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'License', '\DTA\MetadataBundle\Model\Workflow\LicenseQuery');
    }

    /**
     * Filter the query by a related Partner object
     *
     * @param   Partner|PropelObjectCollection $partner The related object(s) to use as filter
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return                 ImagesourceQuery The current query, for fluid interface
     * @throws PropelException - if the provided filter is invalid.
     */
    public function filterByPartner($partner, $comparison = null)
    {
        if ($partner instanceof Partner) {
            return $this
                ->addUsingAlias(ImagesourcePeer::PARTNER_ID, $partner->getId(), $comparison);
        } elseif ($partner instanceof PropelObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(ImagesourcePeer::PARTNER_ID, $partner->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByPartner() only accepts arguments of type Partner or PropelCollection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Partner relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function joinPartner($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Partner');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Partner');
        }

        return $this;
    }

    /**
     * Use the Partner relation Partner object
     *
     * @see       useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return   \DTA\MetadataBundle\Model\Workflow\PartnerQuery A secondary query class using the current class as primary query
     */
    public function usePartnerQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinPartner($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Partner', '\DTA\MetadataBundle\Model\Workflow\PartnerQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   Imagesource $imagesource Object to remove from the list of results
     *
     * @return ImagesourceQuery The current query, for fluid interface
     */
    public function prune($imagesource = null)
    {
        if ($imagesource) {
            $this->addUsingAlias(ImagesourcePeer::ID, $imagesource->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

}
