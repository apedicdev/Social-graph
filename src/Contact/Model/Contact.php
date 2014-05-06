<?php

namespace Contact\Model;

use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;

class Contact implements EventManagerAwareInterface {

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

    public function getAllRows() {
        $sql = "select * from contact";
        $stat = $this->_db->query($sql);
        return $stat->fetchAll();
    }

    public function getRow($id) {
        $sql = "select * from contact where id=?";

        $stat = $this->_db->prepare($sql);
        $stat->execute(array($id));
        return $stat->fetch();
    }

    public function showFriends($id) {
        $this->getEventManager()->trigger('event.friends', $this);
        $sql = "select friend_id, firstName, surname, age, gender from connections left join contact on `friend_id`=id where user_id={$id}";
        $stat = $this->_db->query($sql);
        return $stat->fetchAll();
    }

    public function showFriendsOfFriends($id, $friends) {
        $this->getEventManager()->trigger('event.friends', $this);
        foreach ($friends['rows'] as $friend) {
            $sql = "select friend_id, firstName, surname, age, gender from connections left join contact on `friend_id`=id where user_id={$friend['friend_id']} and friend_id not in (select friend_id from connections where user_id=$id) and friend_id!=$id";
            $stat[] = $this->_db->query($sql)->fetchAll();
        }
        return $stat;
    }

    public function setSuggestedFriends($friends) {
        //print_r($friends);
        foreach ($friends as $suggestedsFriends) {
            if (count($suggestedsFriends) > 2) {
                foreach ($suggestedsFriends as $suggestedsFriend) {
                    if (!in_array($suggestedsFriend['friend_id'], $friends_ids)) {
                        $friends_ids[] = $suggestedsFriend['friend_id'];
                        $suggesteds[] = $suggestedsFriend;
                    }
                }
            }
        }
        return ($suggesteds);
    }

    
    

}
