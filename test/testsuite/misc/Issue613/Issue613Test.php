<?php

require_once dirname(__FILE__) . '/../../../tools/helpers/bookstore/BookstoreTestBase.php';

/**
 * Proves the issue described in #613 and the fix for it.
 *
 * Basically it describes that a standalone Criteria does not
 * contain the information which table belongs to which model class.
 * We've added with this issue a new method `Propel::buildAllTableMaps` which
 * can help. See the description of this method for more information.
 *
 * @see https://github.com/propelorm/Propel/issues/613
 */
class Issue613Test extends BookstoreTestBase
{

    /**
     * Creates a
     *
     * @return string The file path to the serialized Criteria
     */
    private function getPreparedCriteriaObject()
    {
        $tmpFile = sys_get_temp_dir().'propel-test-issue-613-serialized-object.tmp';
        BookPeer::clearRelatedInstancePool();
        $criteria = new Criteria();
        $criteria->addSelectColumn(BookPeer::ID);
        $criteria->addSelectColumn(AuthorPeer::LAST_NAME);
        $criteria->add(BookPeer::ID, 1);
        $criteria->addJoin(BookPeer::AUTHOR_ID, AuthorPeer::ID);
        file_put_contents($tmpFile, serialize($criteria));
        return $tmpFile;
    }

    private function fireTestUnserialization($pScript)
    {

        $tmpFile = $this->getPreparedCriteriaObject();
        $returnCode = 0;

        $cmd = 'php '.escapeshellcmd(__DIR__.'/'.$pScript) . ' ' . escapeshellarg($tmpFile);
        system($cmd, $returnCode);

        unlink($tmpFile);
        $this->assertEquals(0, $returnCode, 'The unserialization should work without exception.');
    }

    public function testUnserializeCriteria()
    {
        $this->fireTestUnserialization('unserialize_criteria.php');
        $this->fireTestUnserialization('unserialize_criteria_buildAllTableMaps.php');
    }

}