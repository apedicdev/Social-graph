<?php

namespace Contact\Model;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;

class City implements EventManagerAwareInterface {
    private $_db;
    protected $events;

    public function setEventManager(EventManagerInterface $events) {
        $events->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->events = $events;
        return $this;
    }

    public function getEventManager() {
        if (null === $this->events) {
            $this->setEventManager(new EventManager());
        }
        return $this->events;
    }

    public function __construct($db) {
        $this->_db = $db;
    }
    public function setSuggestedCities($friends, $user_id) {
        //print_r($friends);
        $friends_ids = array();
        foreach ($friends['rows']as $friendsOfMine) {
            if (!in_array($friendsOfMine['friend_id'], $friends_ids)) {
                $friends_ids[] = $friendsOfMine['friend_id'];
            }
        }
        foreach ($friends['rows1'] as $friendsOfFriends) {
            foreach ($friendsOfFriends as $friendsOfFriendsData) {
                if (!in_array($friendsOfFriendsData['friend_id'], $friends_ids)) {
                    $friends_ids[] = $friendsOfFriendsData['friend_id'];
                }
            }
        }
        $citiesVisitedByFriends = $this->getCitiesVisited($friends_ids, $user_id);
        return $this->orderCities($citiesVisitedByFriends);
    }

    public function getCitiesVisited($friends_ids, $user_id) {
        foreach ($friends_ids as $id) {
            $sql = "select id, name, `percentual` from `cities` c left join `cities_visited` cv on c.`id`=cv.`city_id` where cv.`user_id`=$id and c.id not in (select id from `cities` c left join `cities_visited` cv on c.`id`=cv.`city_id` where cv.`user_id`=$user_id)";
            $stat[] = $this->_db->query($sql)->fetchAll();
        }
        return $stat;
    }

    public function orderCities($citiesVisited) {
        $orderedCities = array();
        $cities_id = array();
        foreach ($citiesVisited as $cities) {
            foreach ($cities as $city) {
                if (!in_array($city['id'], $cities_id)) {
                    $cities_id[] = $city['id'];
                    $orderedCities[] = $city;
                }
            }
        }
        return $this->sortByPercentual($orderedCities);
    }

    public function sortByPercentual($orderedCities) {
        foreach ($orderedCities as $key => $row) {
            $percentual[$key] = $row['percentual'];
        }
        array_multisort($percentual, SORT_DESC, $orderedCities);
        return($orderedCities);
    }
}
