
/**
 * Update the aggregate column in the related <?php echo $relationName ?> object
 *
 * @param PropelPDO $con A connection object
 */
protected function updateRelated<?php echo $relationName ?>For<?php echo $aggregateColumn ?>(PropelPDO $con)
{
	if ($<?php echo $variableName ?> = $this->get<?php echo $relationName ?>()) {
		$<?php echo $variableName ?>-><?php echo $updateMethodName ?>($con);
	}
	if ($this->old<?php echo $relationName ?>For<?php echo $aggregateColumn ?>) {
		$this->old<?php echo $relationName ?>For<?php echo $aggregateColumn ?>-><?php echo $updateMethodName ?>($con);
		$this->old<?php echo $relationName ?>For<?php echo $aggregateColumn ?> = null;
	}
}
