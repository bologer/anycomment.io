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

class Google_Service_DLP_GooglePrivacyDlpV2beta2CustomInfoType extends Google_Model
{
  protected $dictionaryType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2Dictionary';
  protected $dictionaryDataType = '';
  protected $infoTypeType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2InfoType';
  protected $infoTypeDataType = '';
  protected $surrogateTypeType = 'Google_Service_DLP_GooglePrivacyDlpV2beta2SurrogateType';
  protected $surrogateTypeDataType = '';

  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2Dictionary
   */
  public function setDictionary(Google_Service_DLP_GooglePrivacyDlpV2beta2Dictionary $dictionary)
  {
    $this->dictionary = $dictionary;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2Dictionary
   */
  public function getDictionary()
  {
    return $this->dictionary;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2InfoType
   */
  public function setInfoType(Google_Service_DLP_GooglePrivacyDlpV2beta2InfoType $infoType)
  {
    $this->infoType = $infoType;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2InfoType
   */
  public function getInfoType()
  {
    return $this->infoType;
  }
  /**
   * @param Google_Service_DLP_GooglePrivacyDlpV2beta2SurrogateType
   */
  public function setSurrogateType(Google_Service_DLP_GooglePrivacyDlpV2beta2SurrogateType $surrogateType)
  {
    $this->surrogateType = $surrogateType;
  }
  /**
   * @return Google_Service_DLP_GooglePrivacyDlpV2beta2SurrogateType
   */
  public function getSurrogateType()
  {
    return $this->surrogateType;
  }
}
