<?php namespace {
  
  class mysql extends \mysqli {
    
    /**
     * [__construct description]
     * 
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $db
     * @param array  $opt
     */
    public function __construct( $host = NULL, $user = NULL, $pass = NULL, $db = NULL, $opt = [] ) {
      // merge options
      $opt = [
        'charset' => 'utf8',
        'collate' => 'utf8_general_ci',
        'persist' => TRUE
      ] + $opt;
      // map options to props
      foreach( $opt as $prop => $value )
        $this->$prop = $value;
      // persistant connection?
      $host = $this->persist ? "p:{$host}" : $host;
      // call parent constructor
      parent::__construct( $host, $user, $pass, $db );
      // set charset
      $this->set_charset( $this->charset );
      // check connection
      if( $this->errno ) {
        throw new exception(
          $this->error,
          $this->errno
        );
      }
    }
    
    /**
     * [statement description]
     * 
     * @param  string $sql
     * @param  array  $params
     * @return mixed
     */
    public function statement( $sql, $params = [] ) {
      // prepare statement
      $stmt = $this->prepare( $sql );
      // got statement?
      if( $stmt ) {
        // build param type string
        $types = '';
        foreach( $params as &$var ) {
          if( is_string($var) )     $types.= 's';
          else if( is_int($var) )   $types.= 'i';
          else if( is_float($var) ) $types.= 'd';
        }
        // bind parameters
        array_unshift( $params, $types );
        call_user_func_array(
          [ &$stmt, 'bind_param' ],
          $params
        );
        // run prepared statement against db
        $stmt->execute();
        // determine type of op through meta data
        $meta = $stmt->result_metadata();
        // is it a write op?
        if( !$meta ) {
          return [
            'affectedRows' => $stmt->affected_rows,
            'insertId'     => $stmt->insert_id
          ]
        }
        // it is a read op
        else {
          // store the result
          $stmt->store_result();
          // needed for referencing
          $params = [];
          $row    = [];
          // set result row referencees
          while( $field = $meta->fetch_field() )
            $params[] = &$row[ $field->name ];
          // dump meta data
          $meta->close();
          // bind result to row references
          call_user_func_array(
            [ &$stmt, 'bind_result' ],
            $params
          );
          // super-duper result row array
          $results = [];
          // map rows over to results
          while( $stmt->fetch() )
            $results[] = (object) $row;
          // free up resources
          $stmt->free_result();
        }
        // dump the statement
        $stmt->close();
        // finally, guess what?
        return $results;
      }
      // nothing worked as expected...
      return FALSE;
    }
    
  }
  
} ?>