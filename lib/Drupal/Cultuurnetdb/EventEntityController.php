<?php

namespace Drupal\Cultuurnetdb;

use \EntityAPIController;

class EventEntityController extends EntityAPIController
{
    /**
     * @var \CultuurNet\Search\ServiceInterface;
     */
    protected $service;

    public function __construct($entity_type) {
        parent::__construct($entity_type);

        // @todo Initialize search service here or use lazy loading?
        $this->service = cultuurnetdb_get_service();
    }

    /**
     * Retrieves events.
     *
     * @return The results in a Traversable object.
     *
     * @todo Handle conditions (query on properties?)
     */
    public function query($ids, $conditions, $revision_id = FALSE) {
        $entities = array();

        // always group on cdbid
        $params = array(
            new \CultuurNet\Search\Parameter\Group(),
            // @todo handle other types as well in one entity controller?
            new \CultuurNet\Search\Parameter\FilterQuery('type:event'),
        );

        if ($ids) {
            $luceneQuery = array();
            foreach ($ids as $id) {
                $luceneQuery[] = 'cdbid:' . $id;
            }

            $params[] = new \CultuurNet\Search\Parameter\Query(implode(' OR ', $luceneQuery));
        }
        else {
            // Match anything.
            $params[] = new \CultuurNet\Search\Parameter\Query('*:*');
        }

        $result = $this->service->search($params);

        foreach ($result->getItems() as $event) {
            // Get a local ID, which needs to be numeric for other APIs like the field API
            // Therefore we can not use the UUID as entity id

            // We wrap the event in a class implementing the necessary properties and methods for the entity API
            $entity = new $this->entityInfo['entity class']($event);
            $entities[$entity->id] = $entity;
        }

        return $entities;
    }
}
