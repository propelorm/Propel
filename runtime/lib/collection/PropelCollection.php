<?php

/**
 * This file is part of the Propel package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * Class for iterating over a list of Propel elements
 * The collection keys must be integers - no associative array accepted
 *
 * @method     PropelCollection fromXML(string $data) Populate the collection from an XML string
 * @method     PropelCollection fromYAML(string $data) Populate the collection from a YAML string
 * @method     PropelCollection fromJSON(string $data) Populate the collection from a JSON string
 * @method     PropelCollection fromCSV(string $data) Populate the collection from a CSV string
 *
 * @method     string toXML(boolean $usePrefix, boolean $includeLazyLoadColumns) Export the collection to an XML string
 * @method     string toYAML(boolean $usePrefix, boolean $includeLazyLoadColumns) Export the collection to a YAML string
 * @method     string toJSON(boolean $usePrefix, boolean $includeLazyLoadColumns) Export the collection to a JSON string
 * @method     string toCSV(boolean $usePrefix, boolean $includeLazyLoadColumns) Export the collection to a CSV string
 *
 * @author     Francois Zaninotto
 * @package    propel.runtime.collection
 */
class PropelCollection implements \ArrayAccess, \SeekableIterator, \Countable, \Serializable
{
    /**
     * @var       string
     */
    protected $model = '';

    /**
     * @var       PropelFormatter
     */
    protected $formatter;

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->data = $data;
    }

    /**
     * @param mixed $value
     */
    public function append($value)
    {
        $this->data[] = $value;
    }

    /**
     * Get the data in the collection
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set the data in the collection
     *
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Gets the position of the internal pointer
     * This position can be later used in seek()
     *
     * @return integer
     */
    public function getPosition()
    {
        return key($this->data);
    }

    /**
     * Move the internal pointer to the beginning of the list
     * And get the first element in the collection
     *
     * @return mixed
     */
    public function getFirst()
    {
        if (0 === count($this->data)) {
            return null;
        }

        return reset($this->data);
    }

    /**
     * Check whether the internal pointer is at the beginning of the list
     *
     * @return boolean
     */
    public function isFirst()
    {
        if (0 === count($this->data)) {
            return true;
        }

        list($first) = $this->data;

        return $first === current($this->data);
    }

    /**
     * Move the internal pointer backward
     * And get the previous element in the collection
     *
     * @return mixed
     */
    public function getPrevious()
    {
        if (0 === ($pos = $this->getPosition()) || !count($this->data)) {
            return null;
        }

        return prev($this->data);
    }

    /**
     * Get the current element in the collection
     *
     * @return mixed
     */
    public function getCurrent()
    {
        if (!count($this->data)) {
            return null;
        }

        return current($this->data);
    }

    /**
     * Move the internal pointer forward
     * And get the next element in the collection
     *
     * @return mixed
     */
    public function getNext()
    {
        if (!count($this->data) || $this->isLast()) {
            return null;
        }

        return next($this->data);
    }

    /**
     * Move the internal pointer to the end of the list
     * And get the last element in the collection
     *
     * @return mixed
     */
    public function getLast()
    {
        if (count($this->data) === 0) {
            return null;
        } else {
            return end($this->data);
        }
    }

    /**
     * Check whether the internal pointer is at the end of the list
     *
     * @return boolean
     */
    public function isLast()
    {
        if (count($this->data) === 0) {
            // empty list... so yes, this is the last
            return true;
        } else {
            $copy = $this->data;

            return current($this->data) === end($copy);
        }
    }

    /**
     * Check if the collection is empty
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->data) === 0;
    }

    /**
     * Check if the current index is an odd integer
     *
     * @return boolean
     */
    public function isOdd()
    {
        return (Boolean)($this->getPosition() % 2);
    }

    /**
     * Check if the current index is an even integer
     *
     * @return boolean
     */
    public function isEven()
    {
        return !$this->isOdd();
    }

    /**
     * Get an element from its key
     * Alias for ArrayObject::offsetGet()
     *
     * @param mixed $key
     *
     * @return mixed The element
     *
     * @throws PropelException
     */
    public function get($key)
    {
        if (!$this->offsetExists($key)) {
            throw new PropelException('Unknown key '.$key);
        }

        return $this->offsetGet($key);
    }

    /**
     * Pops an element off the end of the collection
     *
     * @return mixed The popped element
     */
    public function pop()
    {
        if (count($this->data) === 0) {
            return null;
        }

        $array = $this->getArrayCopy();
        $ret = array_pop($array);
        $this->exchangeArray($array);

        return $ret;
    }

    /**
     * Pops an element off the beginning of the collection
     *
     * @return mixed The popped element
     */
    public function shift()
    {
        // the reindexing is complicated to deal with through the iterator
        // so let's use the simple solution
        $arr = $this->getArrayCopy();
        $ret = array_shift($arr);
        $this->exchangeArray($arr);

        return $ret;
    }

    /**
     * Prepend one or more elements to the beginning of the collection
     *
     * @param mixed $value the element to prepend
     *
     * @return integer The number of new elements in the array
     */
    public function prepend($value)
    {
        // the reindexing is complicated to deal with through the iterator
        // so let's use the simple solution
        $arr = $this->getArrayCopy();
        $ret = array_unshift($arr, $value);
        $this->exchangeArray($arr);

        return $ret;
    }

    /**
     * Add an element to the collection with the given key
     * Alias for ArrayObject::offsetSet()
     *
     * @param mixed $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * Removes a specified collection element
     * Alias for ArrayObject::offsetUnset()
     *
     * @param mixed $key
     *
     * @return mixed The removed element
     *
     * @throws PropelException
     */
    public function remove($key)
    {
        if (!$this->offsetExists($key)) {
            throw new PropelException('Unknown key '.$key);
        }

        return $this->offsetUnset($key);
    }

    /**
     * Clears the collection
     *
     * @return array The previous collection
     */
    public function clear()
    {
        return $this->data = array();
    }

    /**
     * Whether or not this collection contains a specified element
     *
     * @param mixed $element
     *
     * @return boolean
     */
    public function contains($element)
    {
        return in_array($element, $this->data, true);
    }

    /**
     * Search an element in the collection
     *
     * @param mixed $element
     *
     * @return mixed Returns the key for the element if it is found in the collection, FALSE otherwise
     */
    public function search($element)
    {
        return array_search($element, $this->data, true);
    }

    /**
     * Returns an array of objects present in the collection that
     * are not presents in the given collection.
     *
     * @param PropelCollection $collection A Propel collection.
     *
     * @return PropelCollection An array of Propel objects from the collection that are not presents in the given collection.
     */
    public function diff(PropelCollection $collection)
    {
        return array_diff($collection, $this->data);
    }

    // Serializable interface

    /**
     * @return string
     */
    public function serialize()
    {
        $repr = array(
            'data' => $this->getArrayCopy(),
            'model' => $this->model,
        );

        return serialize($repr);
    }

    /**
     * @param string $data
     *
     * @return void
     */
    public function unserialize($data)
    {
        $repr = unserialize($data);
        $this->exchangeArray($repr['data']);
        $this->model = $repr['model'];
    }

    // Propel collection methods

    /**
     * Set the model of the elements in the collection
     *
     * @param string $model Name of the Propel object classes stored in the collection
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Get the model of the elements in the collection
     *
     * @return string Name of the Propel object class stored in the collection
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Get the peer class of the elements in the collection
     *
     * @return string Name of the Propel peer class stored in the collection
     *
     * @throws PropelException
     */
    public function getPeerClass()
    {
        if ($this->model == '') {
            throw new PropelException('You must set the collection model before interacting with it');
        }

        return constant($this->getModel().'::PEER');
    }

    /**
     * @param PropelFormatter $formatter
     */
    public function setFormatter(PropelFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    /**
     * @return PropelFormatter
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Get a connection object for the database containing the elements of the collection
     *
     * @param string $type The connection type (Propel::CONNECTION_READ by default; can be Propel::connection_WRITE)
     *
     * @return PropelPDO A PropelPDO connection object
     */
    public function getConnection($type = Propel::CONNECTION_READ)
    {
        $databaseName = constant($this->getPeerClass().'::DATABASE_NAME');

        return Propel::getConnection($databaseName, $type);
    }

    /**
     * Populate the current collection from a string, using a given parser format
     * <code>
     * $coll = new PropelObjectCollection();
     * $coll->setModel('Book');
     * $coll->importFrom('JSON', '{{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}}');
     * </code>
     *
     * @param mixed $parser A PropelParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     *
     * @return BaseObject The current object, for fluid interface
     */
    public function importFrom($parser, $data)
    {
        if (!$parser instanceof PropelParser) {
            $parser = PropelParser::getParser($parser);
        }

        return $this->fromArray($parser->listToArray($data), BasePeer::TYPE_PHPNAME);
    }

    /**
     * Export the current collection to a string, using a given parser format
     * <code>
     * $books = BookQuery::create()->find();
     * echo $book->exportTo('JSON');
     *  => {{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}}');
     * </code>
     *
     * A PropelOnDemandCollection cannot be exported. Any attempt will result in a PropelExecption being thrown.
     *
     * @param mixed $parser A PropelParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param boolean $usePrefix (optional) If true, the returned element keys will be prefixed with the
     *                                            model class name ('Article_0', 'Article_1', etc). Defaults to TRUE.
     *                                            Not supported by PropelArrayCollection, as PropelArrayFormatter has
     *                                            already created the array used here with integers as keys.
     * @param boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     *                                            Not supported by PropelArrayCollection, as PropelArrayFormatter has
     *                                            already included lazy-load columns in the array used here.
     *
     * @return string The exported data
     */
    public function exportTo($parser, $usePrefix = true, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof PropelParser) {
            $parser = PropelParser::getParser($parser);
        }

        return $parser->listFromArray(
            $this->toArray(null, $usePrefix, BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns)
        );
    }

    /**
     * Catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you use a custom BaseObject
     *
     * @param string $name
     * @param mixed $params
     *
     * @return array|string
     *
     * @throws PropelException
     */
    public function __call($name, $params)
    {
        if (preg_match('/^from(\w+)$/', $name, $matches)) {
            return $this->importFrom($matches[1], reset($params));
        }
        if (preg_match('/^to(\w+)$/', $name, $matches)) {
            $usePrefix = isset($params[0]) ? $params[0] : true;
            $includeLazyLoadColumns = isset($params[1]) ? $params[1] : true;

            return $this->exportTo($matches[1], $usePrefix, $includeLazyLoadColumns);
        }
        throw new PropelException('Call to undefined method: '.$name);
    }

    /**
     * Returns a string representation of the current collection.
     * Based on the string representation of the underlying objects, defined in
     * the Peer::DEFAULT_STRING_FORMAT constant
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->exportTo(constant($this->getPeerClass().'::DEFAULT_STRING_FORMAT'));
    }

    /**
     * Creates clones of the containing data.
     */
    public function __clone()
    {
        foreach ($this as $key => $val) {
            // we need to clone only objects, all scalar values will be copied using "="
            if (is_object($val)) {
                $this[$key] = clone $val;
            } else {
                $this[$key] = $val;
            }
        }
    }

    /**
     * Get an array representation of the collection
     * Each object is turned into an array and the result is returned
     *
     * @param string $keyColumn If null, the returned array uses an incremental index.
     *                               Otherwise, the array is indexed using the specified column
     * @param boolean $usePrefix If true, the returned array prefixes keys
     *                               with the model class name ('Article_0', 'Article_1', etc).
     * @param string $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME,
     *                               BasePeer::TYPE_STUDLYPHPNAME, BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME,
     *                               BasePeer::TYPE_NUM. Defaults to BasePeer::TYPE_PHPNAME.
     * @param boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param array $alreadyDumpedObjects List of objects to skip to avoid recursion
     *
     * <code>
     * $bookCollection->toArray();
     * array(
     *  0 => array('Id' => 123, 'Title' => 'War And Peace'),
     *  1 => array('Id' => 456, 'Title' => 'Don Juan'),
     * )
     * $bookCollection->toArray('Id');
     * array(
     *  123 => array('Id' => 123, 'Title' => 'War And Peace'),
     *  456 => array('Id' => 456, 'Title' => 'Don Juan'),
     * )
     * $bookCollection->toArray(null, true);
     * array(
     *  'Book_0' => array('Id' => 123, 'Title' => 'War And Peace'),
     *  'Book_1' => array('Id' => 456, 'Title' => 'Don Juan'),
     * )
     * </code>
     *
     * @return array
     */
    public function toArray(
        $keyColumn = null,
        $usePrefix = false,
        $keyType = BasePeer::TYPE_PHPNAME,
        $includeLazyLoadColumns = true,
        $alreadyDumpedObjects = array()
    ) {
        $ret = array();
        $keyGetterMethod = 'get'.$keyColumn;

        /** @var $obj BaseObject */
        foreach ($this as $key => $obj) {
            $key = null === $keyColumn ? $key : $obj->$keyGetterMethod();
            $key = $usePrefix ? ($this->getModel().'_'.$key) : $key;
            $ret[$key] = $obj->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
        }

        return $ret;
    }

    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Move forward to next element
     *
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        next($this->data);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * Return the current element
     *
     * @return mixed Can return any type.
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean The return value will be casted to boolean and then evaluated.
     */
    public function valid()
    {
        return null !== key($this->data);
    }

    /**
     * Offset to set
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * Offset to unset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * @param array $input
     */
    public function exchangeArray($input)
    {
        $this->data = $input;
    }

    /**
     * Whether a offset exists
     *
     * @param mixed $offset
     * @return bool|void
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @param mixed $offset
     * @return mixed|void
     */
    public function &offsetGet($offset)
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }

        return null;
    }

    /**
     * @param int $position
     * @return void
     */
    public function seek($position)
    {
        if (!isset($this->data[$position])) {
            throw new \OutOfBoundsException("invalid seek position ($position)");
        }

        foreach ($this->data as $k => $d) {
            if ($k === $position) {
                return;
            }
        }
    }

    /**
     * @return array
     */
    public function getArrayCopy()
    {
        return $this->data;
    }
}
