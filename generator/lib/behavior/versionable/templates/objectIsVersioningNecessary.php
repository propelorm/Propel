
/**
 * Checks whether the current state must be recorded as a version
 *
 * @return  boolean
 */
public function isVersioningNecessary($con = null)
{
    if ($this->alreadyInSave) {
        return false;
    }

    if ($this->enforceVersion) {
        return true;
    }

    if (<?php echo $peerClassName ?>::isVersioningEnabled() && ($this->isNew() || $this->isModified() || $this->isDeleted())) {
        return true;
    }

<?php foreach ($fkGetters as $fkGetter) : ?>
    if (null !== ($object = $this->get<?php echo $fkGetter ?>($con)) && $object->isVersioningNecessary($con)) {
        return true;
    }

<?php endforeach; ?>
<?php foreach ($refFkGetters as $fkGetter) : ?>
    // to avoid infinite loops, emulate in save
    $this->alreadyInSave = true;
    foreach ($this->get<?php echo $fkGetter ?>(null, $con) as $relatedObject) {
        if ($relatedObject->isVersioningNecessary($con)) {
            $this->alreadyInSave = false;

            return true;
        }
    }
    $this->alreadyInSave = false;

<?php endforeach; ?>
    return false;
}
