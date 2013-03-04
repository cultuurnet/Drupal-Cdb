<?php

namespace Drupal\Cultuurnetdb;

use \Entity;
use \CultuurNet\Search\ActivityStatsExtendedEntity;

/**
 * @property integer id
 * @property string cdbid
 * @property string remote_id
 */
class EventEntity extends Entity {

    /**
     * @var \CultureFeed_Cdb_Item_Event
     */
    protected $entry;

    /**
     * @var integer
     */
    protected $id;

    public function __construct(ActivityStatsExtendedEntity $entry = NULL) {
        $this->entityType = 'cultuurnetdb_event';
        $this->setUp();

        $this->entry = $entry;
    }

    /**
     * Magic __get pass-through to the original object.
     *
     * @param string $name
     */
    public function __get($name) {
      if ($name == 'id') {
        if (!isset($this->id)) {
          $trans = db_transaction();
          $id = cultuurnetdb_entity_get_local_id('event', $this->entry->getEntity()->getCdbId());

          if (!$id) {
            $id = cultuurnetdb_entity_generate_local_id('event', $this->entry->getEntity()->getCdbId());
          }

          $this->id = $id;
        }

        return $this->id;
      }
      else if ($name == 'entry') {
        return $this->entry;
      }
    }

    /**
     * Magic __isset pass-through to the original object.
     *
     * @param string $name
     */
    public function __isset($name) {
      if (in_array($name, array('id', 'entry'))) {
        return TRUE;
      }
    }
}
