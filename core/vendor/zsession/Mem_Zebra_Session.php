<?php


class Zebra_Session
{

    function Zebra_Session($dblink , $session_lifetime = '', $gc_probability = '', $gc_divisor = '', $security_code = 'sEcUr1tY_c0dE', $table_name = 'session_data', $lock_timeout = 60, $link = '')
    {

        $this->link = new V_Cache("memcached");

        // continue if there is an active MySQL connection
        if (@$this->_mysql_ping()) {

            // make sure session cookies never expire so that session lifetime
            // will depend only on the value of $session_lifetime
            ini_set('session.cookie_lifetime', 0);

            // if $session_lifetime is specified and is an integer number
            if ($session_lifetime != '' && is_integer($session_lifetime)) {

                // set the new value
                ini_set('session.gc_maxlifetime', $session_lifetime);

            }

            // if $gc_probability is specified and is an integer number
            if ($gc_probability != '' && is_integer($gc_probability)) {

                // set the new value
                ini_set('session.gc_probability', $gc_probability);

            }

            // if $gc_divisor is specified and is an integer number
            if ($gc_divisor != '' && is_integer($gc_divisor)) {

                // set the new value
                ini_set('session.gc_divisor', $gc_divisor);

            }

            // get session lifetime
            $this->session_lifetime = ini_get('session.gc_maxlifetime');

            // we'll use this later on in order to try to prevent HTTP_USER_AGENT spoofing
            $this->security_code = $security_code;

            // the table to be used by the class
            $this->table_name = $table_name;

            // the maximum amount of time (in seconds) for which a process can lock the session
            $this->lock_timeout = $lock_timeout;

            // register the new handler
            session_set_save_handler(
                array(&$this, 'open'),
                array(&$this, 'close'),
                array(&$this, 'read'),
                array(&$this, 'write'),
                array(&$this, 'destroy'),
                array(&$this, 'gc')
            );

            // start the session
            session_start();

        // if no MySQL connections could be found
        // trigger a fatal error message and stop execution
        } else trigger_error('<br>No MySQL connection!<br>Error', E_USER_ERROR);

    }

    /**
     *  Custom close() function
     *
     *  @access private
     */
    function close()
    {


        return true;

    }

    /**
     *  Custom destroy() function
     *
     *  @access private
     */
    function destroy($session_id)
    {
        return $this->link->delete("sid_$session_id");

    }

    /**
     *  Custom gc() function (garbage collector)
     *
     *  @access private
     */
    function gc($maxlifetime)
    {
        return true;
    }

    /**
     *  Get the number of active sessions - sessions that have not expired.
     *
     *  <i>The returned value does not represent the exact number of active users as some sessions may be unused
     *  although they haven't expired.</i>
     *
     *  <code>
     *  //  include the class
     *  require 'path/to/Zebra_Session.php';
     *
     *  //  start the session
     *  $session = new Zebra_Session();
     *
     *  //  get the (approximate) number of active sessions
     *  $active_sessions = $session->get_active_sessions();
     *  </code>
     *
     *  @return integer     Returns the number of active (not expired) sessions.
     */
    function get_active_sessions()
    {return 1;


    }

    /**
     *  Queries the system for the values of <i>session.gc_maxlifetime</i>, <i>session.gc_probability</i> and <i>session.gc_divisor</i>
     *  and returns them as an associative array.
     *
     *  To view the result in a human-readable format use:
     *  <code>
     *  //  include the class
     *  require 'path/to/Zebra_Session.php';
     *
     *  //  instantiate the class
     *  $session = new Zebra_Session();
     *
     *  //  get default settings
     *  print_r('<pre>');
     *  print_r($session->get_settings());
     *
     *  //  would output something similar to (depending on your actual settings)
     *  //  Array
     *  //  (
     *  //      [session.gc_maxlifetime] => 1440 seconds (24 minutes)
     *  //      [session.gc_probability] => 1
     *  //      [session.gc_divisor] => 1000
     *  //      [probability] => 0.1%
     *  //  )
     *  </code>
     *
     *  @since 1.0.8
     *
     *  @return array   Returns the values of <i>session.gc_maxlifetime</i>, <i>session.gc_probability</i> and <i>session.gc_divisor</i>
     *                  as an associative array.
     *
     */
    function get_settings()
    {

        // get the settings
        $gc_maxlifetime = ini_get('session.gc_maxlifetime');
        $gc_probability = ini_get('session.gc_probability');
        $gc_divisor     = ini_get('session.gc_divisor');

        // return them as an array
        return array(
            'session.gc_maxlifetime'    =>  $gc_maxlifetime . ' seconds (' . round($gc_maxlifetime / 60) . ' minutes)',
            'session.gc_probability'    =>  $gc_probability,
            'session.gc_divisor'        =>  $gc_divisor,
            'probability'               =>  $gc_probability / $gc_divisor * 100 . '%',
        );

    }

    /**
     *  Custom open() function
     *
     *  @access private
     */
    function open($save_path, $session_name)
    {

        return true;

    }

    /**
     *  Custom read() function
     *
     *  @access private
     */
    function read($session_id)
    {
	$data = $this->link->get("sid_$session_id");

        if ($data) {

            return $data;

        }
        // on error return an empty string - this HAS to be an empty string
        return '';

    }

    /**
     *  Regenerates the session id.
     *
     *  <b>Call this method whenever you do a privilege change in order to prevent session hijacking!</b>
     *
     *  <code>
     *  //  include the class
     *  require 'path/to/Zebra_Session.php';
     *
     *  //  start the session
     *  $session = new Zebra_Session();
     *
     *  //  regenerate the session's ID
     *  $session->regenerate_id();
     *  </code>
     *
     *  @return void
     */
    function regenerate_id()
    {

        // saves the old session's id
        $old_session_id = session_id();

        // regenerates the id
        // this function will create a new session, with a new id and containing the data from the old session
        // but will not delete the old session
        session_regenerate_id();

        // because the session_regenerate_id() function does not delete the old session,
        // we have to delete it manually
        $this->destroy($old_session_id);

    }

    /**
     *  Deletes all data related to the session
     *
     *  <code>
     *  //  include the class
     *  require 'path/to/Zebra_Session.php';
     *
     *  //  start the session
     *  $session = new Zebra_Session();
     *
     *  //  end current session
     *  $session->stop();
     *  </code>
     *
     *  @since 1.0.1
     *
     *  @return void
     */
    function stop()
    {

        $this->regenerate_id();

        session_unset();

        session_destroy();

    }

    /**
     *  Custom write() function
     *
     *  @access private
     */
    function write($session_id, $session_data)
    {
        return $this->link->set("sid_$session_id",$session_data,$this->session_lifetime);

    }

    /**
     *  Wrapper for "mysql_affected_rows".
     *
     *  Executes "mysql_affected_rows" with or without the MySQL link identifier, depending if it was given as argument
     *  to the constructor.
     *
     *  @access private
     */
    function _mysql_affected_rows()
    {    
	return 1;    
        // if a MySQL link identifier was specified, use it when calling the function
        return $this->link->affected_rows();
    }

    /**
     *  Wrapper for "mysql_error".
     *
     *  Executes "mysql_error" with or without the MySQL link identifier, depending if it was given as argument to the
     *  constructor.
     *
     *  @access private
     */
    function _mysql_error()
    {   
        return "error 588";

        // if a MySQL link identifier was NOT specified, ignore it when calling the function
        if ($this->link == '') return mysql_error();

        // if a MySQL link identifier was specified, use it when calling the function
        else return mysql_error($this->link);

    }

    /**
     *  Wrapper for "mysql_query".
     *
     *  Executes "mysql_query" with or without the MySQL link identifier, depending if it was given as argument to the
     *  constructor.
     *
     *  @access private
     */
    function _mysql_query($query)
    {return true;
    }

    /**
     *  Wrapper for "mysql_ping".
     *
     *  Executes "mysql_ping" with or without the MySQL link identifier, depending if it was given as argument to the
     *  constructor.
     *
     *  @access private
     */
    function _mysql_ping()
    {
        return true;

        // if a MySQL link identifier was NOT specified, ignore it when calling the function
        if ($this->link == '') return mysql_ping();

        // if a MySQL link identifier was specified, use it when calling the function
        else return mysql_ping($this->link);

    }

    /**
     *  Wrapper for "mysql_real_escape_string".
     *
     *  Executes "mysql_real_escape_string" with or without the MySQL link identifier, depending if it was given as
     *  argument to the constructor.
     *
     *  @access private
     */
    function _mysql_real_escape_string($string)
    {     
        return $string;
    }

}

