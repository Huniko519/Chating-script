<?php

session_start();
include_once 'connect.php';

function checkLogin($username, $pass) {
    global $dbPrefix, $pdo;
    try {
        $stmt = $pdo->prepare('SELECT * FROM ' . $dbPrefix . 'users WHERE username = ? AND password = ?');
        $stmt->execute([$username, md5($pass)]);
        $user = $stmt->fetch();

        if ($user) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function checkLoginToken($token, $roomId, $isAdmin = false) {
    global $dbPrefix, $pdo;
    try {
        if ($isAdmin) {
            $stmt = $pdo->prepare('SELECT * FROM ' . $dbPrefix . 'agents WHERE token = ? AND roomId = ?');
            $stmt->execute([$token, $roomId]);
        } else {
            $stmt = $pdo->prepare('SELECT * FROM ' . $dbPrefix . 'users WHERE token = ? AND roomId = ? AND is_blocked = 0');
            $stmt->execute([$token, $roomId]);
        }

        $user = $stmt->fetch();

        if ($user) {
            return json_encode($user);
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function insertScheduling($agent, $visitor, $agenturl, $visitorurl, $pass, $session, $datetime, $duration, $shortagenturl, $shortvisitorurl, $agentId = null, $agenturl_broadcast = null, $visitorurl_broadcast = null, $shortagenturl_broadcast = null, $shortvisitorurl_broadcast = null) {
    global $dbPrefix, $pdo;
    try {
        $sql = "INSERT INTO " . $dbPrefix . "rooms (agent, visitor, agenturl, visitorurl, password, roomId, datetime, duration, shortagenturl, shortvisitorurl, agent_id, agenturl_broadcast, visitorurl_broadcast, shortagenturl_broadcast, shortvisitorurl_broadcast) "
                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$agent, $visitor, $agenturl, $visitorurl, md5($pass), $session, $datetime, $duration, $shortagenturl, $shortvisitorurl, $agentId, $agenturl_broadcast, $visitorurl_broadcast, $shortagenturl_broadcast, $shortvisitorurl_broadcast]);
        return 200;
    } catch (Exception $e) {
        return 'Error';
    }
}

function insertChat($roomId, $message, $agent, $from, $participants, $agentId = null, $system = null, $avatar = null, $datetime = null) {
    global $dbPrefix, $pdo;
    try {

        $sql = "INSERT INTO " . $dbPrefix . "chats (`room_id`, `message`, `agent`, `agent_id`, `from`, `date_created`, `participants`, `system`, `avatar`) "
                . "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $pdo->prepare($sql)->execute([$roomId, $message, $agent, $agentId, $from, date("Y-m-d H:i:s", strtotime($datetime)), $participants, $system, $avatar]);
        return 200;
    } catch (Exception $e) {
        return 'Error';
    }
}

function getChat($roomId, $sessionId, $agentId = null) {

    global $dbPrefix, $pdo;
    try {

        $additional = '';
        $array = [$roomId, "%$sessionId%"];
        if ($agentId && $agentId != 'false') {
            $additional = ' AND agent_id = ?';
            $array = [$roomId, $agentId, "%$sessionId%"];
        }
        $stmt = $pdo->prepare("SELECT * FROM " . $dbPrefix . "chats WHERE `room_id`= ? $additional and participants like ? order by date_created asc");
        $stmt->execute($array);
        $rows = array();
        while ($r = $stmt->fetch()) {
            $r['date_created'] = strtotime($r['date_created']);
            $rows[] = $r;
        }
        return json_encode($rows);
    } catch (Exception $e) {
        return false;
    }
}

function getAgent($tenant) {

    global $dbPrefix, $pdo;
    try {
        $array = [$tenant];
        $stmt = $pdo->prepare("SELECT * FROM " . $dbPrefix . "agents WHERE `tenant`= ?");
        $stmt->execute($array);
        $user = $stmt->fetch();

        if ($user) {
            return json_encode($user);
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function getRoom($roomId) {

    global $dbPrefix, $pdo;
    try {
        $array = [$roomId];
        $stmt = $pdo->prepare("SELECT * FROM " . $dbPrefix . "rooms WHERE `roomId`= ? AND `is_active` = 1");
        $stmt->execute($array);
        $room = $stmt->fetch();
        if ($room) {
            return json_encode($room);
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function getDrawing($roomId) {

    global $dbPrefix, $pdo;
    try {
        $array = [$roomId];
        $stmt = $pdo->prepare("SELECT * FROM " . $dbPrefix . "drawings WHERE `room_id`= ? order by drawing_id asc");
        $stmt->execute($array);
        $rows = array();
        while ($r = $stmt->fetch()) {
            $rows[] = $r;
        }
        return json_encode($rows);
    } catch (Exception $e) {
        return false;
    }
}

function addDrawing($room_id, $drawing) {
    global $dbPrefix, $pdo;
    try {
        if ($drawing) {
            $sql = 'INSERT INTO ' . $dbPrefix . 'drawings (room_id, drawing) VALUES (?, ?)';
            $pdo->prepare($sql)->execute([$room_id, $drawing]);
        } else {
            $sql = 'DELETE FROM ' . $dbPrefix . 'drawings WHERE room_id=?';
            $pdo->prepare($sql)->execute([$room_id]);
        }
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getRooms($agentId = false) {

    global $dbPrefix, $pdo;
    try {
        $additional = '';
        $array = [];
        if ($agentId && $agentId != 'false') {
            $additional = ' WHERE agent_id = ? ';
            $array = [$agentId];
        }
        $stmt = $pdo->prepare('SELECT * FROM ' . $dbPrefix . 'rooms ' . $additional . ' order by room_id desc');
        $stmt->execute($array);
        $rows = array();
        while ($r = $stmt->fetch()) {
            $rows[] = $r;
        }
        return json_encode($rows);
    } catch (Exception $e) {
        return false;
    }
}

function deleteRoom($roomId, $agentId = false) {
    global $dbPrefix, $pdo;
    try {
        $additional = '';
        $array = [$roomId];
        if ($agentId && $agentId != 'false') {
            $additional = ' AND agent_id = ?';
            $array = [$roomId, $agentId];
        }
        $sql = 'DELETE FROM ' . $dbPrefix . 'rooms WHERE room_id = ?' . $additional;
        $pdo->prepare($sql)->execute($array);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getAgents() {
    global $dbPrefix, $pdo;
    try {
        $stmt = $pdo->prepare('SELECT * FROM ' . $dbPrefix . 'agents order by agent_id desc');
        $stmt->execute();
        $rows = array();
        while ($r = $stmt->fetch()) {
            $rows[] = $r;
        }
        return json_encode($rows);
    } catch (Exception $e) {
        return false;
    }
}

function deleteAgent($agentId) {
    global $dbPrefix, $pdo;
    try {

        $sql = 'DELETE FROM ' . $dbPrefix . 'agents WHERE agent_id = ?';
        $pdo->prepare($sql)->execute([$agentId]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function editAgent($agentId, $firstName, $lastName, $email, $tenant, $pass = null) {
    global $dbPrefix, $pdo;
    try {
        $array = [$firstName, $lastName, $email, $tenant, $agentId];
        $additional = '';
        if ($pass) {
            $additional = ', password = ?';
            $array = [$firstName, $lastName, $email, $tenant, md5($pass), $agentId];
        }

        $sql = 'UPDATE ' . $dbPrefix . 'agents SET first_name=?, last_name=?, email=?, tenant=? ' . $additional . ' WHERE agent_id = ?';

        $pdo->prepare($sql)->execute($array);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function endMeeting($roomId, $agentId = null) {
    global $dbPrefix, $pdo;
    try {
        $additional = '';
        $array = [$roomId];
        if ($agentId) {
            $additional = ' AND agent_id = ?';
            $array = [$roomId, $agentId];
        }
//        $sql = 'UPDATE ' . $dbPrefix . 'rooms SET is_active=0 WHERE roomId = ? ' . $additional;
//        $pdo->prepare($sql)->execute($array);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function blockUser($username) {
    global $dbPrefix, $pdo;
    try {
        $sql = 'UPDATE ' . $dbPrefix . 'users SET is_blocked=1 WHERE username = ?';

        $pdo->prepare($sql)->execute(array($username));
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function editAdmin($agentId, $firstName, $lastName, $email, $tenant, $pass = null) {
    global $dbPrefix, $pdo;
    try {
        $array = [$firstName, $lastName, $email, $tenant, $agentId];
        $additional = '';
        if ($pass) {
            $additional = ', password = ?';
            $array = [$firstName, $lastName, $email, $tenant, md5($pass), $agentId];
        }

        $sql = 'UPDATE ' . $dbPrefix . 'agents SET first_name=?, last_name=?, email=?, tenant=? ' . $additional . ' WHERE agent_id = ?';
        $_SESSION["agent"] = array('agent_id' => $agentId, 'first_name' => $firstName, 'last_name' => $lastName, 'tenant' => $tenant, 'email' => $email);
        $pdo->prepare($sql)->execute($array);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function addAgent($user, $pass, $firstName, $lastName, $email, $tenant) {
    global $dbPrefix, $pdo;
    try {

        $sql = 'INSERT INTO ' . $dbPrefix . 'agents (username, password, first_name, last_name, email, tenant) VALUES (?, ?, ?, ?, ?, ?)';
        $pdo->prepare($sql)->execute([$user, md5($pass), $firstName, $lastName, $email, $tenant]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function addFeedback($sessionId, $roomId, $rate, $text = '', $userId = '') {
    global $dbPrefix, $pdo;
    try {

        $sql = 'INSERT INTO ' . $dbPrefix . 'feedbacks (session_id, room_id, rate, text, user_id, date_added) VALUES (?, ?, ?, ?, ?, ?)';
        $pdo->prepare($sql)->execute([$sessionId, $roomId, $rate, $text, $userId, date("Y-m-d H:i:s")]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function getUsers() {

    global $dbPrefix, $pdo;
    try {
        $stmt = $pdo->prepare('SELECT * FROM ' . $dbPrefix . 'users order by user_id desc');
        $stmt->execute();
        $rows = array();
        while ($r = $stmt->fetch()) {
            $rows[] = $r;
        }
        return json_encode($rows);
    } catch (Exception $e) {
        return false;
    }
}

function deleteUser($userId) {
    global $dbPrefix, $pdo;
    try {
        $sql = 'DELETE FROM ' . $dbPrefix . 'users WHERE user_id = ?';
        $pdo->prepare($sql)->execute([$userId]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function editUser($userId, $name, $user) {
    global $dbPrefix, $pdo;
    try {
        $sql = 'UPDATE ' . $dbPrefix . 'users SET username=?, name=? WHERE user_id = ?';
        $pdo->prepare($sql)->execute([$user, $name, $userId]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function addUser($user, $name, $pass) {

    global $dbPrefix, $pdo;
    try {

        $sql = 'INSERT INTO ' . $dbPrefix . 'users (username, name, password) VALUES (?, ?, ?)';
        $pdo->prepare($sql)->execute([$user, $name, md5($pass)]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function loginAgent($username, $pass) {
    global $dbPrefix, $pdo;
    try {
        $stmt = $pdo->prepare('SELECT * FROM ' . $dbPrefix . 'agents WHERE username = ? AND password=?');
        $stmt->execute([$username, md5($pass)]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION["tenant"] = ($user['is_master']) ? 'lsv_mastertenant' : $user['tenant'];
            $_SESSION["username"] = $user['username'];
            $_SESSION["agent"] = array('agent_id' => $user['agent_id'], 'first_name' => $user['first_name'], 'last_name' => $user['last_name'], 'tenant' => $user['tenant'], 'email' => $user['email']);
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function getChats($agentId = false) {
    global $dbPrefix, $pdo;
    try {
        $additional = '';
        $array = [];
        if ($agentId && $agentId != 'false') {
            $additional = ' WHERE agent_id = ? ';
            $array = [$agentId];
        }
        $stmt = $pdo->prepare('SELECT * FROM ' . $dbPrefix . 'chats ' . $additional . ' group by room_id order by date_created desc');
        $stmt->execute($array);
        $rows = array();
        while ($r = $stmt->fetch()) {
            $stmt1 = $pdo->prepare('SELECT * FROM ' . $dbPrefix . 'chats where room_id=? order by date_created asc ');
            $stmt1->execute([$r['room_id']]);
            $rows1 = '<table>';
            while ($r1 = $stmt1->fetch()) {
                $rows1 .= '<tr><td><small>' . $r1['date_created'] . '</small></td><td>' . $r1['from'] . ': ' . $r1['message'] . '</td></tr>';
            }
            $rows1 .= '</table>';
            $r['messages'] = $rows1;
            $rows[] = $r;
        }
        return json_encode($rows);
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['type']) && $_POST['type'] == 'login') {
        echo checkLogin($_POST['email'], $_POST['password']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'logintoken') {
        echo checkLoginToken($_POST['token'], $_POST['roomId'], @$_POST['isAdmin']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'scheduling') {
        echo insertScheduling($_POST['agent'], $_POST['visitor'], $_POST['agenturl'], $_POST['visitorurl'], $_POST['password'], $_POST['session'], $_POST['datetime'], $_POST['duration'], $_POST['shortAgentUrl'], $_POST['shortVisitorUrl'], $_POST['agentId'], @$_POST['agenturl_broadcast'], @$_POST['visitorurl_broadcast'], @$_POST['shortAgentUrl_broadcast'], @$_POST['shortVisitorUrl_broadcast']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'addchat') {
        echo insertChat($_POST['roomId'], $_POST['message'], $_POST['agent'], $_POST['from'], $_POST['participants'], @$_POST['agentId'], @$_POST['system'], @$_POST['avatar'], @$_POST['datetime']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'getchat') {
        echo getChat($_POST['roomId'], $_POST['sessionId'], @$_POST['agentId']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'getrooms') {
        echo getRooms(@$_POST['agentId']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'getchats') {
        echo getChats(@$_POST['agentId']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'deleteroom') {
        echo deleteRoom($_POST['roomId'], $_POST['agentId']);
    }

    if (isset($_POST['type']) && $_POST['type'] == 'getagents') {
        echo getAgents();
    }
    if (isset($_POST['type']) && $_POST['type'] == 'deleteagent') {
        echo deleteAgent($_POST['agentId']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'editagent') {
        echo editAgent($_POST['agentId'], $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['tenant'], $_POST['password']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'editadmin') {
        echo editAdmin($_POST['agentId'], $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['tenant'], $_POST['password']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'loginagent') {
        echo loginAgent($_POST['username'], $_POST['password']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'addagent') {
        echo addAgent($_POST['username'], $_POST['password'], $_POST['firstName'], $_POST['lastName'], $_POST['email'], $_POST['tenant']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'getusers') {
        echo getUsers();
    }
    if (isset($_POST['type']) && $_POST['type'] == 'deleteuser') {
        echo deleteUser($_POST['userId']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'edituser') {
        echo editUser($_POST['userId'], $_POST['name'], $_POST['username']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'adduser') {
        echo addUser($_POST['username'], $_POST['name'], $_POST['password']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'getagent') {
        echo getAgent($_POST['tenant']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'blockuser') {
        echo blockUser($_POST['username']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'feedback') {
        echo addFeedback($_POST['sessionId'], $_POST['roomId'], $_POST['rate'], @$_POST['text'], @$_POST['userId']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'getroom') {
        echo getRoom($_POST['roomId']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'endmeeting') {
        echo endMeeting($_POST['roomId'], @$_POST['agentId']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'getdrawing') {
        echo getDrawing($_POST['roomId']);
    }
    if (isset($_POST['type']) && $_POST['type'] == 'adddrawing') {
        echo addDrawing($_POST['roomId'], $_POST['drawing']);
    }
}