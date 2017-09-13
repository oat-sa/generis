<?php

namespace oat\oatbox\TaskQueue;


interface MessageInterface extends \JsonSerializable
{
    const JSON_BODY_KEY = 'body';
    const JSON_METADATA_KEY = 'metadata';

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $body
     */
    public function setBody($body);

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param \DateTime $dateTime
     */
    public function setCreatedAt(\DateTime $dateTime);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param string $owner
     */
    public function setOwner($owner);

    /**
     * @return string
     */
    public function getOwner();

    /**
     * @return string
     */
    public function __toString();

    /**
     * Set message metadata
     *
     * Non-destructive setting of message metadata; always adds to the metadata, never overwrites
     * the entire metadata container.
     *
     * @param  string|int|array|\Traversable $spec
     * @param  mixed $value
     */
    public function setMetadata($spec, $value = null);

    /**
     * @param null|string|int $key
     * @param mixed $default
     * @return mixed
     */
    public function getMetadata($key = null, $default = null);
}