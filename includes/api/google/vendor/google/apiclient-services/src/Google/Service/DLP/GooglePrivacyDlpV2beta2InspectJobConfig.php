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

class Google_Service_DLP_GooglePrivacyDlpV2beta2InspectJobConfig extends Google_Model
{
  protected $inspectConfigType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2InspectConfig';
  protected $inspectConfigDataType = '';
  public $inspectTemplateName;
  protected $outputConfigType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2OutputStorageConfig';
  protected $outputConfigDataType = '';
  protected $storageConfigType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2StorageConfig';
  protected $storageConfigDataType = '';

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
  public function setInspectTemplateName($inspectTemplateName)
  {
    $this->inspectTemplateName = $inspectTemplateName;
  }
  public function getInspectTemplateName()
  {
    return $this->inspectTemplateName;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2OutputStorageConfig
   */
  public function setOutputConfig(Google_Service_DLP_GooglePrivacyDlpV2beta2OutputStorageConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2OutputStorageConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2StorageConfig
   */
  public function setStorageConfig(Google_Service_DLP_GooglePrivacyDlpV2beta2StorageConfig $storageConfig)
  {
    $this->storageConfig = $storageConfig;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2StorageConfig
   */
  public function getStorageConfig()
  {
    return $this->storageConfig;
  }
}
