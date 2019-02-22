<?php
/**
 * @author Anton Tuyakhov <atuyakhov@gmail.com>
 */

namespace tuyakhov\jsonapi;

/**
 * Interface for a “resource identifier object” that identifies an individual resource.
 */
interface ResourceIdentifierInterface
{
    /**
     * The "id" member of a resource object.
     * @return string an ID that in pair with type uniquely identifies the resource.
     */
    public function getId();

    /**
     * The "type" member of a resource object.
     * @return string a type that identifies the resource.
     */
    public function getType();
}
