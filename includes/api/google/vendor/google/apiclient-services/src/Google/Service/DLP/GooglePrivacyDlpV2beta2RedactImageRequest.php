<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

class Google_Service_DLP_GooglePrivacyDlpV2beta2RedactImageRequest extends Google_Collection
{
  protected $collection_key = 'imageRedactionConfigs';
  public $imageData;
  protected $imageRedactionConfigsType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2ImageRedactionConfig';
  protected $imageRedactionConfigsDataType = 'array';
  public $imageType;
  protected $inspectConfigType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2InspectConfig';
  protected $inspectConfigDataType = '';

  public function setImageData($imageData)
  {
    $this->imageData = $imageData;
  }
  public function getImageData()
  {
    return $this->imageData;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2ImageRedactionConfig
   */
  public function setImageRedactionConfigs($imageRedactionConfigs)
  {
    $this->imageRedactionConfigs = $imageRedactionConfigs;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2ImageRedactionConfig
   */
  public function getImageRedactionConfigs()
  {
    return $this->imageRedactionConfigs;
  }
  public function setImageType($imageType)
  {
    $this->imageType = $imageType;
  }
  public function getImageType()
  {
    return $this->imageType;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2InspectConfig
   */
  public function setInspectConfig(Google_Service_DLP_GooglePrivacyDlpV2beta2InspectConfig $inspectConfig)
  {
    $this->inspectConfig = $inspectConfig;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2InspectConfig
   */
  public function getInspectConfig()
  {
    return $this->inspectConfig;
  }
}
