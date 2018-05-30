<?php

if(isset($_POST['action']) && !empty($_POST['action'])) {

    require 'db.php';

    $email  = $_POST['email'];
    $naziv = $_POST['naziv'];
    $ime = $_POST['ime'];
    $priimek = $_POST['priimek'];
    $return = true;

    /**
     *  we do server side validation here
     */

    createIP_request();
    $this_ip = getRealIpAddr();
    $time_now = date("Y-m-d H:i:s");
    $time_now_start =  date("Y-m-d H:i:s", strtotime($time_now)-30 );
    $time_blocked =  date("Y-m-d H:i:s", strtotime($time_now)-300 );
    $blacklisted = check_IP_Blacklisted( $this_ip , $time_now , $time_blocked );

    if($blacklisted == true) {
        echo "blacklisted";
        die();
    }

    $all_request_from_this_ip = getAllIPs_fromThisIP( $this_ip,  $time_now,  $time_now_start );  // must return count between

    if( $all_request_from_this_ip->num_rows  > 15 ) {
      $return = false;
      addIP_toBlackList($this_ip, $time_now);
    }

    $emaiValid = validateEmail($email);

    if($emaiValid == false) {
      $return == false;
      echo "false email";
    }

    /**
     * after successfull validation we create POST REQUEST
    */

    if($return == true) {

      addSignup($email, $time_now, $naziv, $ime, $priimek);

        $url = 'your_form_action_url';
        $data = array(
          'email' => $email,
          'firstname' => $ime,
          'lastname' => $priimek,
          'naziv'   => $naziv
        );

        // use key 'http' even if you send the request to https://...
        $options = array(
          'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
          ),
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        echo "success";
        die();

    } else {

      /**
       * in case of false validation we dont do post request
       */

      echo "false";
      die();

    }
}
