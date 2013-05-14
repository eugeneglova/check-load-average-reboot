<?php

    $hostname = 'ec2-23-22-207-172.compute-1.amazonaws.com';
    // SNMP
    $community = 'Ohqueo0o';
    // object id for load average for 1 minute
    // found here: http://www.debianadmin.com/linux-snmp-oids-for-cpumemory-and-disk-statistics.html
    $object_id = '.1.3.6.1.4.1.2021.10.1.3.1';
    // SSH
    $user = "root";
    $pass = "password";
    // $command = 'echo 1 > 1.txt'; // for test
    $command = 'reboot'; // for live

    if (!function_exists('snmpget')) {
        die('function snmpget doesn\'t exist');
    }

    // get load average by SNMP
    $la_srting = snmpget($hostname, $community, $object_id);
    $la = floatval(preg_replace('/^[^"]+"([^"]+)"$/', '\1', $la_srting));

    // reboot server
    if ($la > 1) {

        if (!function_exists('ssh2_connect')) {
            die('function ssh2_connect doesn\'t exist');
        }

        if (false === ($con = ssh2_connect($hostname))) {
            die('unable to connect to ssh server: ' . $hostname);
        } else {
            if(!ssh2_auth_password($con, $user, $pass)) {
                die('unable to login using user: ' . $user);
            } else {
                if (false === (ssh2_exec($con, $command))) {
                    die('unable to exec command: ' . $command);
                }
            }
        }

    }
