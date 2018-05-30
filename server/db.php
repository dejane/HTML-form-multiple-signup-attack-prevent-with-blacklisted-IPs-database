<?php

require 'kint.php';

define("HOST", "your_db_host");
define("USER", "your_db_user");
define("PASS", "your_db_pass");
define("DB", "your_db_name");

/**
 * validate emails also in server side
 */
function validateEmail($email)
{
  $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';

  if (preg_match($pattern, $email) === 1) {
      return true;
  }
  else {
    return false;
  }
}

/**
 * function create IP request from client, sql statements are secure with PDO
 */
function createIP_request()
{
  $ip = getRealIpAddr();
  $time = time();
  $time = timeStamptoRedableDate($time);

  $conn = new PDO('mysql:dbname=DB;host=HOST;charset=utf8', USER, PASS);
  $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("INSERT INTO ips VALUES(:ip, :time_of)");
  $stmt->bindValue(':ip', $ip);
  $stmt->bindValue(':time_of', $time);
  $stmt->execute();
}


/**
 * function to add IP which made multiple request to blacklisted table in db
 */
function addIP_toBlackList($ip, $time_of)
{
  $conn = new PDO('mysql:dbname=DB;host=HOST;charset=utf8', USER, PASS);
  $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("INSERT INTO blacklisted VALUES(:blacklisted_ips, :time_of)");
  $stmt->bindValue(':blacklisted_ips', $ip);
  $stmt->bindValue(':time_of', $time_of);

  $stmt->execute();
}

/**
 * function to add signup to database, called after all validation passes, secure sql statements with PDO
 */
function addSignup($email, $time_of, $naziv, $ime, $priimek)
{
  $conn = new PDO('mysql:dbname=DB;host=HOST;charset=utf8', USER, PASS);
  $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $conn->prepare("INSERT INTO signup VALUES( :firstname, :lastname, :naziv, :email, :time_of )");

  $stmt->bindValue(':firstname', $ime);
  $stmt->bindValue(':lastname', $priimek);
  $stmt->bindValue(':naziv', $naziv);
  $stmt->bindValue(':email', $email);
  $stmt->bindValue(':time_of', $time_of);

  $stmt->execute();
}

/**
 * we get blacklisted IP from database and check if this ip is on the list. function also take time params, as blackling is just for some time
 */
function check_IP_Blacklisted($b_ip , $time_now , $time_blocked)
{
    $array  = array();

    $conn = new mysqli( HOST, USER, PASS, DB);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT blacklisted_ips FROM blacklisted WHERE  blacklisted_ips like '%$b_ip%' AND time_of >= '" . $time_blocked . "' AND time_of <= '" . $time_now . "'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
          return true;
    } else {
          return false;
    }
    $conn->close();
    return $array;
}

/**
 * we get all IPS from ip table
 */
function getAllIPs()
{
    $array  = array();
    $conn = new mysqli( HOST, USER, PASS, DB);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT ip, time_of FROM ips";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {

            $array[] = array(
                  'ip'      => $row["ip"],
                  'time_of' => $row["time_of"],
            );
        }
    }
    $conn->close();
    return $array;
}


/**
 * we get all IPS from this IP from ips tablem used to count request from one ip, functions also take time from to
 */
function getAllIPs_fromThisIP( $s_ip , $time_now,  $time_now_start )
{
    $array  = array();

    $conn = new mysqli( HOST, USER, PASS, DB);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT ip, time_of FROM ips WHERE ip like '%$s_ip%' AND  time_of >= '" . $time_now_start . "' AND time_of <= '" . $time_now . "'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $array = $result;
    } else {
        return false;
    }
    $conn->close();

    return $array;
}

/**
 * function to get user real IP address
 */
function getRealIpAddr()
{
  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

/**
 * function to get us timestamp in human readable format
 */
function timeStamptoRedableDate($timestamp)
{
    $datetimeFormat = 'Y-m-d H:i:s';
    $date = new \DateTime();
    $date->setTimestamp($timestamp);

    return $date->format($datetimeFormat);
}

?>
