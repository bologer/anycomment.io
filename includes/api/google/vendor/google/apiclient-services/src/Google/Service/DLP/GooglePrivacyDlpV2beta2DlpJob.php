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

class Google_Service_DLP_GooglePrivacyDlpV2beta2DlpJob extends Google_Collection
{
  protected $collection_key = 'errorResults';
  public $createTime;
  public $endTime;
  protected $errorResultsType = 'Google_Service_DLP_GoogleRpcStatus';
  protected $errorResultsDataType = 'array';
  protected $inspectDetailsType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2InspectDataSourceDetails';
  protected $inspectDetailsDataType = '';
  public $name;
  protected $riskDetailsType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2AnalyzeDataSourceRiskDetails';
  protected $riskDetailsDataType = '';
  public $startTime;
  public $state;
  public $type;

  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  public function getCreateTime()
  {
    return $this->createTime;
  }
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * @param Google_Service_DLP_GoogleRpcStatus
   */
  public function setErrorResults($errorResults)
  {
    $this->errorResults = $errorResults;
  }
  /**
   * @return Google_Service_DLP_GoogleRpcStatus
   */
  public function getErrorResults()
  {
    return $this->errorResults;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2InspectDataSourceDetails
   */
  public function setInspectDetails(Google_Service_DLP_GooglePrivacyDlpV2beta2InspectDataSourceDetails $inspectDetails)
  {
    $this->inspectDetails = $inspectDetails;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2InspectDataSourceDetails
   */
  public function getInspectDetails()
  {
    return $this->inspectDetails;
  }
  public function setName($name)
  {
    $this->name = $name;
  }
  public function getName()
  {
    return $this->name;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2AnalyzeDataSourceRiskDetails
   */
  public function setRiskDetails(Google_Service_DLP_GooglePrivacyDlpV2beta2AnalyzeDataSourceRiskDetails $riskDetails)
  {
    $this->riskDetails = $riskDetails;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2AnalyzeDataSourceRiskDetails
   */
  public function getRiskDetails()
  {
    return $this->riskDetails;
  }
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  public function getStartTime()
  {
    return $this->startTime;
  }
  public function setState($state)
  {
    $this->state = $state;
  }
  public function getState()
  {
    return $this->state;
  }
  public function setType($type)
  {
    $this->type = $type;
  }
  public function getType()
  {
    return $this->type;
  }
}
