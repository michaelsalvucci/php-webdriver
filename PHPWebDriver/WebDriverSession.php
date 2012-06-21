<?php
// Copyright 2004-present Facebook. All Rights Reserved.
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//     http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.

require_once('WebDriverContainer.php');
require_once('WebDriverSimpleItem.php');

class PHPWebDriver_WebDriverSession extends PHPWebDriver_WebDriverContainer {
  protected function methods() {
    return array(
      'url' => 'GET', // for POST, use open($url)
      'forward' => 'POST',
      'back' => 'POST',
      'refresh' => 'POST',
      'execute' => 'POST',
      'execute_async' => 'POST',
      'screenshot' => 'GET',
      'window_handle' => 'GET',
      'window_handles' => 'GET',
      'frame' => 'POST',
      'source' => 'GET',
      'title' => 'GET',
      'keys' => 'POST',
      'orientation' => array('GET', 'POST'),
      'alert_text' => array('GET', 'POST'),
      'accept_alert' => 'POST',
      'dismiss_alert' => 'POST',
      'moveto' => 'POST',
      'click' => 'POST',
      'buttondown' => 'POST',
      'buttonup' => 'POST',
      'doubleclick' => 'POST',
    );
  }

  // /session/:sessionId/url (POST)
  public function open($url, $curl_opts = array()) {
    $this->curl('POST',
                '/url',
                array('url' => $url),
                $curl_opts);
    return $this;
  }

  // /session/:sessionId (GET)
  public function capabilities($curl_opts = array()) {
    $result = $this->curl('GET', '', '', $curl_opts);
    return $result['value'];
  }

  // /session/:sessionId (DELETE)
  public function close($curl_opts = array()) {
    $result = $this->curl('DELETE', '', '', $curl_opts);
    return $result['value'];
  }

  // /session/:sessionId/cookie (GET)
  public function getAllCookies($curl_opts = array()) {
    $result = $this->curl('GET', '/cookie', '', $curl_opts);
    return $result['value'];
  }

  // /session/:sessionId/cookie (GET)
  public function getCookie($name, $curl_opts = array()) {
    $result = $this->curl('GET', '/cookie', '', $curl_opts);
    foreach ($result['value'] as $cookie) {
      if ($cookie["name"] == $name) {
        return $cookie;
      }
    }
  }

  // /session/:sessionId/cookie (POST)
  public function setCookie($cookie_json, $curl_opts = array()) {
    $this->curl('POST', '/cookie', array('cookie' => $cookie_json), $curl_opts);
    return $this;
  }

  // /session/:sessionId/cookie (DELETE)
  public function deleteAllCookies($curl_opts = array()) {
    $this->curl('DELETE', '/cookie', '', $curl_opts);
    return $this;
  }

  // /session/:sessionId/cookie/:name (DELETE)
  public function deleteCookie($cookie_name, $curl_opts = array()) {
    $this->curl('DELETE', '/cookie/' . $cookie_name, '', $curl_opts);
    return $this;
  }

  public function timeouts() {
    trigger_error("timeouts() is deprecated; use setTimeouts() instead", E_USER_DEPRECATED);
    $item = new PHPWebDriver_WebDriverSimpleItem($this->url . '/timeouts');
    return $item->setMethods(array(
      'async_script' => 'POST',
      'implicit_wait' => 'POST',
    ));
  }

  // /session/:sessionId/timeouts (POST)
  public function setTimeouts($timeout, $curl_opts = array()) {
    $this->curl('POST', '/timeouts', $timeout, $curl_opts);
    return $this;
  }

  public function implicitlyWait($s) {
      $ms = $s * 1000;
      $this->setTimeouts(array('type' => 'implicit', 'ms' => $ms));
      return $this;
  }

  public function setScriptTimeout($s) {
      $ms = $s * 1000;
      $this->setTimeouts(array('type' => 'script', 'ms' => $ms));
      return $this;
  }
  
  public function setPageLoadTimeout($s) {
      $ms = $s * 1000;
      $this->setTimeouts(array('type' => 'page load', 'ms' => $ms));
      return $this;
  }

  public function ime() {
    $item = new PHPWebDriver_WebDriverSimpleItem($this->url . '/ime');
    return $item->setMethods(array(
      'available_engines' => 'GET',
      'active_engine' => 'GET',
      'activated' => 'GET',
      'deactivate' => 'POST',
      'activate' => 'POST',
    ));
  }

  // /session/:sessionId/window (POST)
  public function focusWindow($name, $curl_opts = array()) {
    $this->curl('POST', '/window', array('name' => $name), $curl_opts);
    return $this;
  }

  public function window($window_handle = 'current') {
    $item = new PHPWebDriver_WebDriverSimpleItem($this->url . '/window/' . $window_handle);
    return $item->setMethods(array(
      'size' => array('GET', 'POST'),
      'position' => array('GET', 'POST'),
      'maximize' => array('POST')
    ));
  }

  // /session/:sessionId/element/active (POST)
  public function activeElement($curl_opts = array()) {
    $results = $this->curl('POST', '/element/active', $curl_opts);
    return $this->webDriverElement($results['value']);
  }
  
  public function touch() {
    $item = new PHPWebDriver_WebDriverSimpleItem($this->url . '/touch');
    return $item->setMethods(array(
      'click' => 'POST',
      'down' => 'POST',
      'up' => 'POST',
      'move' => 'POST',
      'scroll' => 'POST',
      'doubleclick' => 'POST',
      'longclick' => 'POST',
      'flick' => 'POST',
    ));
  }

  protected function getElementPath($element_id) {
    return sprintf('%s/element/%s', $this->url, $element_id);
  }
}
