<?php
/**
 * Class containing base request.
 */

 namespace nba\shared\messaging;

use JsonSerializable;

 /**
  * class that is base for requests.
  */
abstract class Request implements JsonSerializable{

  /**
   * The type of the request.
   * 
   * @param string $type the Request type.
   */
}