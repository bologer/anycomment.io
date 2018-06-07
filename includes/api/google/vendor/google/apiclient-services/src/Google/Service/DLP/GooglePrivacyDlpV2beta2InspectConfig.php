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

class Google_Service_DLP_GooglePrivacyDlpV2beta2InspectConfig extends Google_Collection
{
  protected $collection_key = 'infoTypes';
  protected $customInfoTypesType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2CustomInfoType';
  protected $customInfoTypesDataType = 'array';
  public $excludeInfoTypes;
  public $includeQuote;
  protected $infoTypesType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2InfoType';
  protected $infoTypesDataType = 'array';
  protected $limitsType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2FindingLimits';
  protected $limitsDataType = '';
  public $minLikelihood;

  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2CustomInfoType
   */
  public function setCustomInfoTypes($customInfoTypes)
  {
    $this->customInfoTypes = $customInfoTypes;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2CustomInfoType
   */
  public function getCustomInfoTypes()
  {
    return $this->customInfoTypes;
  }
  public function setExcludeInfoTypes($excludeInfoTypes)
  {
    $this->excludeInfoTypes = $excludeInfoTypes;
  }
  public function getExcludeInfoTypes()
  {
    return $this->excludeInfoTypes;
  }
  public function setIncludeQuote($includeQuote)
  {
    $this->includeQuote = $includeQuote;
  }
  public function getIncludeQuote()
  {
    return $this->includeQuote;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2InfoType
   */
  public function setInfoTypes($infoTypes)
  {
    $this->infoTypes = $infoTypes;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2InfoType
   */
  public function getInfoTypes()
  {
    return $this->infoTypes;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2FindingLimits
   */
  public function setLimits(Google_Service_DLP_GooglePrivacyDlpV2beta2FindingLimits $limits)
  {
    $this->limits = $limits;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2FindingLimits
   */
  public function getLimits()
  {
    return $this->limits;
  }
  public function setMinLikelihood($minLikelihood)
  {
    $this->minLikelihood = $minLikelihood;
  }
  public function getMinLikelihood()
  {
    return $this->minLikelihood;
  }
}
